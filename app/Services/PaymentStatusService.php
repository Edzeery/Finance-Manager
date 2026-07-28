<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\Payments\PaymentTransitionValidator;

class PaymentStatusService
{
    public function __construct(
        private readonly PaymentTransitionValidator $transitionValidator,
    ) {}

    public function markFailed(Payment $payment): void
    {
        $this->transitionValidator->transition($payment, PaymentStatus::CheckoutFailed);
    }

    public function cancel(Payment $payment): void
    {
        $this->transitionValidator->transition($payment, PaymentStatus::CheckoutCanceled);
    }

    public function cancelWithSubscriptionCleanup(Payment $payment): void
    {
        $this->transitionValidator->transition($payment, PaymentStatus::CheckoutCanceled);

        if ($payment->subscription_id) {
            $sub = Subscription::withoutWorkspace()->find($payment->subscription_id);
            if ($sub && $sub->status === SubscriptionStatus::PastDue) {
                $sub->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);
            }
        }
    }

    public function resetToPending(Payment $payment, array $gatewayData): void
    {
        $payment->update([
            'transaction_id' => $gatewayData['transaction_id'],
            'gateway_reference' => $gatewayData['gateway_reference'],
            'gateway_payload' => $gatewayData['gateway_payload'],
            'status' => PaymentStatus::CheckoutPending,
            'canceled_at' => null,
            'metadata' => array_merge($payment->metadata ?? [], [
                'redirect_url' => $gatewayData['redirect_url'] ?? null,
                'gateway_response' => $gatewayData['gateway_response'] ?? [],
            ]),
        ]);
    }

    public function markPaid(Payment $payment): void
    {
        $this->transitionValidator->transition($payment, PaymentStatus::CheckoutPaid);
    }
}
