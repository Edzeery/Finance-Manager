<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Jobs\SendSubscriptionExpiryNotification;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Mark expired subscriptions and enter grace period';

    public function handle(): int
    {
        $count = 0;
        $graceCount = 0;
        $trialCount = 0;

        // معالجة الاشتراكات النشطة المنتهية
        Subscription::withoutWorkspace()
            ->where('status', SubscriptionStatus::Active->value)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->chunk(100, function ($subscriptions) use (&$count, &$graceCount) {
                foreach ($subscriptions as $subscription) {
                    $hasPending = Payment::where('subscription_id', $subscription->id)
                        ->where('status', PaymentStatus::CheckoutPending->value)
                        ->exists();

                    if ($hasPending) {
                        continue;
                    }

                    $subscription->update(['status' => SubscriptionStatus::Expired->value]);
                    $subscription->enterGracePeriod();
                    $graceCount++;

                    SendSubscriptionExpiryNotification::dispatch($subscription, 'grace');
                }
                $count += $subscriptions->count();
            });

        // معالجة الاشتراكات التجريبية المنتهية
        Subscription::withoutWorkspace()
            ->where('status', SubscriptionStatus::Trialing->value)
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', now())
            ->chunk(100, function ($subscriptions) use (&$trialCount) {
                foreach ($subscriptions as $subscription) {
                    $subscription->update(['status' => SubscriptionStatus::Expired->value]);
                    $trialCount++;
                }
            });

        $this->info("Processed {$count} active + {$trialCount} trial expired subscriptions. {$graceCount} entered grace period.");

        return self::SUCCESS;
    }
}
