<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionExpiryWarning;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class SendSubscriptionExpiryNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public Subscription $subscription,
        public string $type, // 'reminder' | 'grace'
    ) {}

    public function handle(): void
    {
        $user = $this->subscription->user ?? User::find($this->subscription->user_id);
        if (!$user) return;

        $user->notify(new SubscriptionExpiryWarning(
            subscription: $this->subscription,
            type: $this->type,
        ));
    }
}
