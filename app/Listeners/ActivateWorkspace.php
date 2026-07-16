<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;

class ActivateWorkspace
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
    ) {}

    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment;

        if ($payment->subscription_id) {
            return;
        }

        $workspace = $payment->workspace;
        $planId = $payment->user?->activeSubscription()?->subscription_plan_id
            ?? $payment->user?->pending_plan_id;

        if (! $planId) {
            return;
        }

        $plan = SubscriptionPlan::find($planId);

        if (! $plan) {
            return;
        }

        $this->subscriptionService->activateFromPayment($payment, $plan);
    }
}
