<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Events\SubscriptionActivated;
use App\Services\AdminNotificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class CreateAdminNotification
{
    public function __construct(
        private AdminNotificationService $notificationService,
    ) {}

    public function handlePaymentCompleted(PaymentCompleted $event): void
    {
        try {
            $payment = $event->payment;
            $user = $payment->subscription?->workspace?->owner()?->first() ?? $payment->user;

            if ($user) {
                $this->notificationService->newPaymentReceived($payment, $user);
            }
        } catch (\Throwable $e) {
            $this->logFailure($event, $e);
        }
    }

    public function handleSubscriptionActivated(SubscriptionActivated $event): void
    {
        try {
            $subscription = $event->subscription;

            if ($subscription->user && $subscription->plan) {
                $this->notificationService->subscriptionActivated(
                    $subscription->user->name,
                    $subscription->plan->name,
                    $subscription->user,
                );
            }
        } catch (\Throwable $e) {
            $this->logFailure($event, $e);
        }
    }

    public function handleUserRegistered(Registered $event): void
    {
        try {
            $this->notificationService->newUserRegistered($event->user);
        } catch (\Throwable $e) {
            $this->logFailure($event, $e);
        }
    }

    private function logFailure(object $event, \Throwable $e): void
    {
        Log::error('Failed to create admin notification', [
            'event' => class_basename($event),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
