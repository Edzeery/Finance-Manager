<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Mail\SubscriptionCancelled;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\Payments\GatewayManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SubscriptionCancellationService
{
    public function __construct(
        private readonly GatewayManager $gatewayManager,
    ) {}

    public function cancelSubscription(Subscription $subscription, string $type = 'period_end'): void
    {
        $subscription->update([
            'status' => SubscriptionStatus::Canceled->value,
            'canceled_at' => now(),
            'auto_renew' => false,
        ]);

        if ($type === 'immediate') {
            $subscription->update(['ends_at' => now()]);
            $this->attemptRefund($subscription);
        } else {
            $subscription->enterGracePeriod();
        }

        if ($subscription->user && $subscription->user->email) {
            Mail::to($subscription->user->email)
                ->queue(new SubscriptionCancelled($subscription));
        }
    }

    public function cancelCurrentSubscription(?Subscription $currentSub, Subscription $newSub): void
    {
        if ($currentSub && $currentSub->id !== $newSub->id && $currentSub->isActive()) {
            $currentSub->update([
                'status' => SubscriptionStatus::Canceled->value,
                'canceled_at' => now(),
                'auto_renew' => false,
            ]);
            $currentSub->enterGracePeriod();
        }
    }

    private function attemptRefund(Subscription $subscription): void
    {
        $completedPayment = Payment::withoutWorkspace()
            ->where('subscription_id', $subscription->id)
            ->where('status', PaymentStatus::CheckoutPaid->value)
            ->latest()
            ->first();

        if (!$completedPayment || !$completedPayment->method) {
            return;
        }

        try {
            $gateway = $this->gatewayManager->driver($completedPayment->method);

            if (!$gateway->isOnline()) {
                return;
            }

            $result = $gateway->refund($completedPayment);

            if ($result->success) {
                Log::info('Refund processed for subscription', [
                    'subscription_id' => $subscription->id,
                    'payment_id' => $completedPayment->id,
                    'transaction_id' => $result->transactionId,
                ]);
            } else {
                Log::warning('Refund failed for subscription', [
                    'subscription_id' => $subscription->id,
                    'payment_id' => $completedPayment->id,
                    'message' => $result->message,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Refund exception for subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
