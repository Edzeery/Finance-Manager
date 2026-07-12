<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Events\SubscriptionActivated;
use App\Services\AdminNotificationService;
use Illuminate\Auth\Events\Registered;

class CreateAdminNotification
{
    public function __construct(
        private AdminNotificationService $notificationService,
    ) {}

    public function handle(object $event): void
    {
        match (true) {
            $event instanceof Registered => $this->notificationService->newUserRegistered($event->user),
            $event instanceof PaymentCompleted => $this->notificationService->newPaymentReceived($event->payment, $event->payment->subscription?->workspace?->owner()?->first() ?? $event->payment->user),
            $event instanceof SubscriptionActivated => $this->notificationService->subscriptionActivated(
                $event->subscription->user->name,
                $event->subscription->plan->name,
                $event->subscription->user,
            ),
            default => null,
        };
    }
}
