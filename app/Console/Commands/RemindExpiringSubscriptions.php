<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Jobs\SendSubscriptionExpiryNotification;
use App\Models\Subscription;
use Illuminate\Console\Command;

class RemindExpiringSubscriptions extends Command
{
    protected $signature = 'subscriptions:remind-expiry';

    protected $description = 'Send reminders for expiring subscriptions';

    public function handle(): int
    {
        Subscription::withoutWorkspace()
            ->where('status', SubscriptionStatus::Active->value)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now()->addDays(config('finance.expiry_reminder_days', 3)))
            ->where('ends_at', '>', now())
            ->chunk(100, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    SendSubscriptionExpiryNotification::dispatch($subscription, 'reminder');
                }
            });

        $this->info('Expiry reminders dispatched.');

        return self::SUCCESS;
    }
}
