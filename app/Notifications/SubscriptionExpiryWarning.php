<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiryWarning extends Notification
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
        public string $type, // 'reminder' | 'grace'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->type === 'grace') {
            return (new MailMessage)
                ->subject(__('subscription.grace_subject'))
                ->line(__('subscription.grace_intro'))
                ->line(__('subscription.grace_expires', [
                    'date' => $this->subscription->grace_ends_at?->format('Y-m-d'),
                ]))
                ->action(__('subscription.renew'), route('account.subscriptions'));
        }

        return (new MailMessage)
            ->subject(__('subscription.expiry_reminder_subject'))
            ->line(__('subscription.expiry_reminder_intro'))
            ->line(__('subscription.expiry_reminder_date', [
                'date' => $this->subscription->ends_at?->format('Y-m-d'),
            ]))
            ->action(__('subscription.renew'), route('account.subscriptions'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'type' => $this->type,
            'grace_ends_at' => $this->subscription->grace_ends_at?->toDateTimeString(),
            'ends_at' => $this->subscription->ends_at?->toDateTimeString(),
        ];
    }
}
