<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Models\Workspace;

class SubscriptionProrationService
{
    private const MID_MONTH_DAYS = 30;

    /**
     * Calculate proportional proration amount for plan changes.
     *
     * Formula per the proportional prorata system:
     *   totalDays      = 30 (monthly) or 365 (yearly) — or actual days between start/end
     *   remainingDays  = remaining days in the current billing period
     *   dailyRateCurrent = currentPrice / totalDays
     *   dailyRateTarget  = targetPrice / totalDays
     *   remainingValue   = dailyRateCurrent x remainingDays
     *   costAtNewRate    = dailyRateTarget x remainingDays
     *   amountDue        = costAtNewRate - remainingValue
     *
     * Positive amountDue = upgrade (user pays)
     * Negative amountDue = downgrade (user receives credit)
     */
    public function calculateProration(Workspace $workspace, SubscriptionPlan $targetPlan, string $billingPeriod): array
    {
        $subscription = $workspace->owner()?->first()?->activeSubscription();

        if (!$subscription || !$subscription->isActive() || !$subscription->ends_at || $subscription->plan->isFree()) {
            return [
                'amount_due' => 0,
                'remaining_value' => 0,
                'remaining_days' => 0,
                'total_days' => 0,
                'is_upgrade' => false,
                'is_downgrade' => false,
            ];
        }

        $currentPrice = $subscription->plan_price_amount
            ?? ($billingPeriod === 'yearly' ? $subscription->plan->yearly_price : $subscription->plan->monthly_price);

        $totalDays = $subscription->ends_at->diffInDays($subscription->starts_at) ?: self::MID_MONTH_DAYS;
        $remainingDays = max(0, now()->diffInDays($subscription->ends_at, false));

        $targetPrice = $billingPeriod === 'yearly'
            ? $targetPlan->yearly_price
            : $targetPlan->monthly_price;

        if ($totalDays <= 0 || $remainingDays <= 0) {
            return [
                'amount_due' => 0,
                'remaining_value' => 0,
                'remaining_days' => $remainingDays,
                'total_days' => $totalDays,
                'is_upgrade' => false,
                'is_downgrade' => false,
            ];
        }

        $dailyRateCurrent = $currentPrice / $totalDays;
        $dailyRateTarget = $targetPrice / $totalDays;

        $remainingValue = $dailyRateCurrent * $remainingDays;
        $costAtNewRate = $dailyRateTarget * $remainingDays;

        $amountDue = $costAtNewRate - $remainingValue;

        return [
            'amount_due' => round($amountDue, 2),
            'remaining_value' => round($remainingValue, 2),
            'cost_at_new_rate' => round($costAtNewRate, 2),
            'remaining_days' => $remainingDays,
            'total_days' => $totalDays,
            'is_upgrade' => $amountDue > 0,
            'is_downgrade' => $amountDue < 0,
        ];
    }
}
