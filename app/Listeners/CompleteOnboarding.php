<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;

class CompleteOnboarding
{
    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment;

        $user = $payment->user;

        if (! $user || ! $user->pending_plan_id) {
            return;
        }

        $user->markPlanConfirmed();
    }
}
