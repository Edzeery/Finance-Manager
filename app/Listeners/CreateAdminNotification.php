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

    public function handle(object $event): void
    {
        try {
            match (true) {
                $event instanceof Registered => $this->notificationService->newUserRegistered($event->user),
                $event instanceof PaymentCompleted => $this->handlePayment($event),
                $event instanceof SubscriptionActivated => $this->handleSubscription($event),
                default => null,
            };
        } catch (\Throwable $e) {
            Log::error('Failed to create admin notification', [
                'event' => class_basename($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function handlePayment(PaymentCompleted $event): void
    {
        $payment = $event->payment;
        $user = $payment->subscription?->workspace?->owner()?->first() ?? $payment->user;

        if ($user) {
            $this->notificationService->newPaymentReceived($payment, $user);
        }
    }

    private function handleSubscription(SubscriptionActivated $event): void
    {
        $subscription = $event->subscription;

        if ($subscription->user && $subscription->plan) {
            $this->notificationService->subscriptionActivated(
                $subscription->user->name,
                $subscription->plan->name,
                $subscription->user,
            );
        }
    }
}
