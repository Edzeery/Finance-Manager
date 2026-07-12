<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Jobs\SendSubscriptionExpiryNotification;
use App\Models\Payment;
use App\Models\Subscription;
use App\Enums\SubscriptionStatus;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Mark expired subscriptions and enter grace period';

    public function handle(): int
    {
        $count = 0;
        $graceCount = 0;

        Subscription::withoutWorkspace()
            ->where('status', SubscriptionStatus::Active->value)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->chunk(100, function ($subscriptions) use (&$count, &$graceCount) {
                foreach ($subscriptions as $subscription) {
                    // لا نُنهي الاشتراك إذا كان لديه دفعة معلقة (قد تكتمل ويُمدد)
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

        $this->info("Processed {$count} expired subscriptions. {$graceCount} entered grace period.");

        return self::SUCCESS;
    }
}
