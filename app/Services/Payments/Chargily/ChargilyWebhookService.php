<?php

namespace App\Services\Payments\Chargily;

use App\Enums\PaymentStatus;
use App\Enums\PaymentWebhookLogStatus;
use App\Enums\SubscriptionStatus;
use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use App\Models\Subscription;
use App\Services\Payments\Chargily\Exceptions\ChargilyException;
use App\Services\SubscriptionActivationService;
use App\Services\SubscriptionService;
use App\Services\Payments\PaymentTransitionValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class ChargilyWebhookService
{
    public function __construct(
        private readonly ChargilySignatureValidator $signatureValidator,
        private readonly SubscriptionService $subscriptionService,
        private readonly SubscriptionActivationService $activationService,
        private readonly PaymentTransitionValidator $transitionValidator,
    ) {}

    public function process(): void
    {
        $result = $this->signatureValidator->validate();
        $webhookElement = $result['webhook_element'];
        $rawPayload = $result['raw_payload'];
        $payload = json_decode($rawPayload, true) ?? [];

        $checkoutElement = $webhookElement->getData();

        if (! $checkoutElement) {
            throw ChargilyException::unhandledEvent('No checkout data');
        }

        $metadata = $checkoutElement->getMetadata();
        $paymentId = $metadata['payment_id'] ?? null;

        if (! $paymentId) {
            throw ChargilyException::unhandledEvent('Missing payment_id in metadata');
        }

        $eventType = $webhookElement->getType();
        $checkoutId = $checkoutElement->getId();

        $payment = Payment::withoutWorkspace()->find($paymentId);

        if (! $payment) {
            throw ChargilyException::unhandledEvent("Payment not found: {$paymentId}");
        }

        if (! $this->claimWebhookLog($checkoutId, $eventType, $paymentId, $webhookElement, $payload)) {
            return;
        }

        match ($eventType) {
            'checkout.paid' => $this->handlePaid($payment, $checkoutElement, $payload),
            'checkout.failed' => $this->handleFailed($payment, $payload),
            'checkout.canceled' => $this->handleCanceled($payment, $payload),
            'checkout.expired' => $this->handleExpired($payment, $payload),
            default => throw ChargilyException::unhandledEvent($eventType),
        };
    }

    private function claimWebhookLog(string $checkoutId, string $eventType, int $paymentId, $webhookElement, array $payload): bool
    {
        try {
            PaymentWebhookLog::create([
                'gateway' => 'chargily',
                'event_type' => $eventType,
                'checkout_id' => $checkoutId,
                'payment_id' => $paymentId,
                'payload' => $payload,
                'status' => PaymentWebhookLogStatus::Received->value,
            ]);

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE constraint')) {
                return false;
            }

            throw $e;
        }
    }

    private function handlePaid(Payment $payment, $checkoutElement, array $payload): void
    {
        if ($payment->status === PaymentStatus::CheckoutPaid) {
            return;
        }

        if (in_array($payment->status, [PaymentStatus::CheckoutFailed, PaymentStatus::CheckoutCanceled])) {
            return;
        }

        $checkoutAmount = (float) ($checkoutElement->getAmount() ?? 0);
        $expectedAmount = (float) $payment->amount;
        $tolerance = 0.01;

        if ($checkoutAmount > 0 && abs($checkoutAmount - $expectedAmount) > $tolerance) {
            throw ChargilyException::unhandledEvent(sprintf(
                'Amount mismatch: received %s, expected %s',
                $checkoutAmount,
                $expectedAmount
            ));
        }

        try {
            DB::transaction(function () use ($payment, $checkoutElement, $payload) {
                $payment = Payment::withoutWorkspace()->lockForUpdate()->find($payment->id);
                if (! $payment || $payment->isCompleted()) {
                    return;
                }

                $checkoutId = $checkoutElement->getId();
                $paymentMethod = $checkoutElement->getPaymentMethod();

                $extra = [
                    'transaction_id' => $checkoutId,
                    'gateway_reference' => $checkoutId,
                    'webhook_payload' => $payload,
                    'webhook_processed_at' => now(),
                ];

                if ($paymentMethod) {
                    $extra['payment_method_type'] = strtolower($paymentMethod);
                }

                $this->transitionValidator->transition($payment, PaymentStatus::CheckoutPaid, $extra);

                if ($payment->subscription_id) {
                    $sub = Subscription::withoutWorkspace()->find($payment->subscription_id);
                    if ($sub && $sub->status === SubscriptionStatus::PastDue && $sub->plan) {
                        $this->activationService->activateFromPayment(
                            $payment,
                            $sub->plan,
                            $sub->billing_period ?? 'monthly',
                        );
                    }
                }

                $this->updateWebhookLogStatus($payment, 'checkout.paid');

                Event::dispatch(new PaymentCompleted($payment, $payload));
            });
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'Illegal payment status transition')) {
                return;
            }

            throw $e;
        }
    }

    private function handleFailed(Payment $payment, array $payload): void
    {
        try {
            DB::transaction(function () use ($payment, $payload) {
                $payment = Payment::withoutWorkspace()->lockForUpdate()->find($payment->id);

                if (! $payment || $payment->isCompleted()) {
                    return;
                }

                $this->transitionValidator->transition($payment, PaymentStatus::CheckoutFailed, [
                    'webhook_payload' => $payload,
                    'webhook_processed_at' => now(),
                ]);

                $this->cancelPastDueSubscription($payment);

                $this->updateWebhookLogStatus($payment, 'checkout.failed');

                Event::dispatch(new PaymentFailed($payment, $payload));
            });
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'Illegal payment status transition')) {
                return;
            }

            throw $e;
        }
    }

    private function handleCanceled(Payment $payment, array $payload): void
    {
        try {
            DB::transaction(function () use ($payment, $payload) {
                $payment = Payment::withoutWorkspace()->lockForUpdate()->find($payment->id);

                if (! $payment || $payment->isCompleted()) {
                    return;
                }

                $this->transitionValidator->transition($payment, PaymentStatus::CheckoutCanceled, [
                    'webhook_payload' => $payload,
                    'webhook_processed_at' => now(),
                ]);

                $this->cancelPastDueSubscription($payment);

                $this->updateWebhookLogStatus($payment, 'checkout.canceled');

                Event::dispatch(new PaymentFailed($payment, $payload));
            });
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'Illegal payment status transition')) {
                return;
            }

            throw $e;
        }
    }

    private function handleExpired(Payment $payment, array $payload): void
    {
        try {
            DB::transaction(function () use ($payment, $payload) {
                $payment = Payment::withoutWorkspace()->lockForUpdate()->find($payment->id);

                if (! $payment || $payment->isCompleted()) {
                    return;
                }

                $this->transitionValidator->transition($payment, PaymentStatus::CheckoutExpired, [
                    'webhook_payload' => $payload,
                    'webhook_processed_at' => now(),
                ]);

                $this->cancelPastDueSubscription($payment);

                $this->updateWebhookLogStatus($payment, 'checkout.expired');

                Event::dispatch(new PaymentFailed($payment, $payload));
            });
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'Illegal payment status transition')) {
                return;
            }

            throw $e;
        }
    }

    private function cancelPastDueSubscription(Payment $payment): void
    {
        if ($payment->subscription_id) {
            $sub = Subscription::withoutWorkspace()->find($payment->subscription_id);
            if ($sub && $sub->status === SubscriptionStatus::PastDue) {
                $sub->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);
            }
        }
    }

    private function updateWebhookLogStatus(Payment $payment, string $eventType): void
    {
        PaymentWebhookLog::where('payment_id', $payment->id)
            ->where('event_type', $eventType)
            ->where('status', PaymentWebhookLogStatus::Received->value)
            ->latest()
            ->limit(1)
            ->update(['status' => PaymentWebhookLogStatus::Processed->value]);
    }
}
