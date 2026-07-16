<?php

namespace App\Jobs;

use App\Events\SubscriptionActivated;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ActivateSubscription implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly int $paymentId,
        private readonly int $planId,
        private readonly string $billingPeriod = 'monthly',
    ) {}

    public function handle(SubscriptionService $subscriptionService): void
    {
        $payment = Payment::withoutWorkspace()->find($this->paymentId);

        if (! $payment) {
            logger()->channel('queue')->error('ActivateSubscription: payment not found', [
                'payment_id' => $this->paymentId,
            ]);

            return;
        }

        if (! $payment->isCompleted()) {
            logger()->channel('queue')->warning('ActivateSubscription: payment not completed', [
                'payment_id' => $this->paymentId,
                'status' => $payment->status,
            ]);

            return;
        }

        if ($payment->subscription_id) {
            $existingSub = Subscription::withoutWorkspace()->find($payment->subscription_id);
            if ($existingSub && $existingSub->isActive()) {
                logger()->channel('queue')->info('ActivateSubscription: already activated, skipping', [
                    'payment_id' => $this->paymentId,
                    'subscription_id' => $payment->subscription_id,
                    'status' => $existingSub->status,
                ]);

                return;
            }
        }

        $plan = SubscriptionPlan::find($this->planId);

        if (! $plan) {
            logger()->channel('queue')->error('ActivateSubscription: plan not found', [
                'payment_id' => $this->paymentId,
                'plan_id' => $this->planId,
            ]);

            return;
        }

        $subscription = $subscriptionService->activateFromPayment(
            $payment,
            $plan,
            $this->billingPeriod,
        );

        event(new SubscriptionActivated($subscription, $payment));
    }
}
