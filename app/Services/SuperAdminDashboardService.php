<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\PaymentVerification;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserStatus;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SuperAdminDashboardService
{
    private const CACHE_TTL = 300;

    public function __construct(
        private DateFilterService $dateFilter,
        private CurrencyHelper $currencyHelper,
    ) {}

    public function getOverviewKpis(?string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $range = $this->dateFilter->resolveDateRange($period, $startDate, $endDate);

        $cacheKey = $this->dateFilter->cacheKey('sa:dash:overview', $period, $startDate, $endDate);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($range) {
            $start = $range['start'];
            $end = $range['end'];

            $userQuery = User::query();
            $workspaceQuery = Workspace::query();
            $subQuery = Subscription::withoutWorkspace();

            if ($start && $end) {
                $userQuery->whereBetween('created_at', [$start, $end]);
                $workspaceQuery->whereBetween('created_at', [$start, $end]);
                $subQuery->whereBetween('created_at', [$start, $end]);
            }

            $activeSubs = Subscription::withoutWorkspace()->whereIn('status', [
                SubscriptionStatus::Active->value,
                SubscriptionStatus::Trialing->value,
            ]);

            $canceledSubs = Subscription::withoutWorkspace()->where('status', SubscriptionStatus::Canceled->value);

            if ($start && $end) {
                $activeSubs->where(function ($q) use ($start, $end) {
                    $q->whereBetween('starts_at', [$start, $end])
                        ->orWhereBetween('created_at', [$start, $end]);
                });
                $canceledSubs->whereBetween('canceled_at', [$start, $end]);
            }

            $revenueStats = $this->getRevenueTotals($start, $end);

            $subByPlan = SubscriptionPlan::withCount(['subscriptions' => function ($q) use ($start, $end) {
                if ($start && $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                }
            }])->get()->pluck('subscriptions_count', 'slug');

            $gatewayData = Payment::withoutWorkspace()->where('status', PaymentStatus::CheckoutPaid->value)->whereNull('refunded_at')
                ->selectRaw('method, currency, SUM(amount) as total')->groupBy('method', 'currency')->get();
            $byGateway = [];
            foreach ($gatewayData as $row) {
                $byGateway[$row->method] = ($byGateway[$row->method] ?? 0) + $this->currencyHelper->convert((float) $row->total, $row->currency, $this->currencyHelper->baseCurrency());
            }

            $superAdminCount = User::whereHas('roles', fn ($q) => $q->whereIn('slug', ['super_admin', 'deputy_super_admin']))->count();

            // Online users (not cached — changes in real-time)
            $onlineUsersCount = UserStatus::where('online_status', 'online')
                ->where('last_activity_at', '>=', now()->subMinutes(15))
                ->count();

            $couponQuery = Coupon::query();
            $totalCoupons = (clone $couponQuery)->count();
            $activeCoupons = (clone $couponQuery)->where('is_active', true)->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->count();
            $expiredCoupons = (clone $couponQuery)->where('expires_at', '<', now())->count();
            $totalUses = (clone $couponQuery)->sum('used_count');

            return [
                'total_users' => (clone $userQuery)->count(),
                'super_admins' => $superAdminCount,
                'active_users' => (clone $userQuery)->whereHas('statusRecord', fn ($q) => $q->where('status', 'active'))->count(),
                'online_users' => $onlineUsersCount,
                'total_workspaces' => (clone $workspaceQuery)->count(),
                'active_workspaces' => (clone $workspaceQuery)->where('is_active', true)->count(),
                'active_subscriptions' => (clone $activeSubs)->count(),
                'canceled_subscriptions' => (clone $canceledSubs)->count(),
                'subscriptions_by_plan' => $subByPlan->toArray(),
                'revenue_by_gateway' => collect($byGateway)->map(fn ($v) => round($v, 2))->toArray(),
                'total_revenue' => $revenueStats['gross'],
                'net_revenue' => $revenueStats['net'],
                'total_fees' => $revenueStats['fees'],
                'pending_payments' => $revenueStats['pending_count'],
                'completed_payments' => $revenueStats['completed_count'],
                'pending_amount' => $revenueStats['pending_amount'],
                'total_coupons' => $totalCoupons,
                'active_coupons' => $activeCoupons,
                'expired_coupons' => $expiredCoupons,
                'total_coupon_uses' => $totalUses,
                'revenue_this_month' => 0,
                'base_currency' => $this->currencyHelper->baseCurrency(),
            ];
        });
    }

    public function getRevenueStats(?string $period, ?string $startDate = null, ?string $endDate = null, ?string $gateway = null, ?int $planId = null): array
    {
        $range = $this->dateFilter->resolveDateRange($period, $startDate, $endDate);
        $cacheKey = $this->dateFilter->cacheKey('sa:dash:revenue', $period, $startDate, $endDate).":{$gateway}:{$planId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($range, $gateway, $planId) {
            $start = $range['start'];
            $end = $range['end'];

            $paidQuery = Payment::withoutWorkspace()->where('status', PaymentStatus::CheckoutPaid->value)
                ->whereNull('refunded_at');
            $pendingQuery = Payment::withoutWorkspace()->where('status', PaymentStatus::CheckoutPending->value);
            $refundedQuery = Payment::withoutWorkspace()->whereNotNull('refunded_at');

            if ($start && $end) {
                $paidQuery->whereBetween('paid_at', [$start, $end]);
                $pendingQuery->whereBetween('created_at', [$start, $end]);
                $refundedQuery->whereBetween('refunded_at', [$start, $end]);
            }
            if ($gateway) {
                $paidQuery->where('method', $gateway);
                $pendingQuery->where('method', $gateway);
                $refundedQuery->where('method', $gateway);
            }
            if ($planId) {
                $paidQuery->whereHas('subscription', fn ($q) => $q->where('plan_id', $planId));
                $pendingQuery->whereHas('subscription', fn ($q) => $q->where('plan_id', $planId));
                $refundedQuery->whereHas('subscription', fn ($q) => $q->where('plan_id', $planId));
            }

            $baseCurrency = $this->currencyHelper->baseCurrency();
            $converter = fn ($amount, $currency) => $this->currencyHelper->convert((float) $amount, $currency, $baseCurrency);

            $paidByCurrency = (clone $paidQuery)
                ->selectRaw('currency, SUM(amount) as gross, SUM(COALESCE(gateway_fee,0)) as fees, SUM(COALESCE(tax_added,0)) as taxes, COUNT(*) as count')
                ->groupBy('currency')->get()->keyBy('currency');

            $refundedByCurrency = (clone $refundedQuery)
                ->selectRaw('currency, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('currency')->get()->keyBy('currency');

            $pendingByCurrency = (clone $pendingQuery)
                ->selectRaw('currency, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('currency')->get()->keyBy('currency');

            $gross = 0;
            $net = 0;
            $fees = 0;
            foreach ($paidByCurrency as $cur => $row) {
                $gInBase = $converter($row->gross, $cur);
                $fInBase = $converter($row->fees, $cur);
                $tInBase = $converter($row->taxes, $cur);
                $gross += $gInBase;
                $fees += $fInBase;
                $net += $gInBase - $fInBase - $tInBase;
            }

            $refunded = 0;
            foreach ($refundedByCurrency as $cur => $row) {
                $refunded += $converter($row->total, $cur);
            }

            $pendingAmount = 0;
            $pendingCount = 0;
            foreach ($pendingByCurrency as $cur => $row) {
                $pendingAmount += $converter($row->total, $cur);
                $pendingCount += $row->count;
            }

            $refundRate = $gross > 0 ? round($refunded / $gross * 100, 2) : 0;

            $gatewayData = (clone $paidQuery)
                ->selectRaw('method, currency, SUM(amount) as total')
                ->groupBy('method', 'currency')->get();
            $byGateway = [];
            foreach ($gatewayData as $row) {
                $byGateway[$row->method] = ($byGateway[$row->method] ?? 0) + $converter($row->total, $row->currency);
            }

            $planBase = Payment::withoutWorkspace()->where('payments.status', PaymentStatus::CheckoutPaid->value)
                ->whereNull('payments.refunded_at');
            if ($start && $end) {
                $planBase->whereBetween('paid_at', [$start, $end]);
            }
            if ($gateway) {
                $planBase->where('payments.method', $gateway);
            }
            if ($planId) {
                $planBase->whereHas('subscription', fn ($q) => $q->where('plan_id', $planId));
            }
            $planData = (clone $planBase)
                ->join('subscriptions', 'payments.subscription_id', '=', 'subscriptions.id')
                ->selectRaw('subscriptions.subscription_plan_id, payments.currency, SUM(payments.amount) as total')
                ->groupBy('subscriptions.subscription_plan_id', 'payments.currency')->get();
            $byPlan = [];
            foreach ($planData as $row) {
                $planSlug = SubscriptionPlan::find($row->subscription_plan_id)?->slug ?? 'unknown';
                $byPlan[$planSlug] = ($byPlan[$planSlug] ?? 0) + $converter($row->total, $row->currency);
            }

            $monthlyLabels = $this->dateFilter->monthLabels($start, $end);
            $monthlyData = $this->getMonthlyRevenue($start, $end, $gateway, $planId);

            $paidCount = $paidByCurrency->sum('count');
            $completedCount = (clone $paidQuery)->count();

            $mrr = $this->calculateMrr();

            return [
                'gross' => round($gross, 2),
                'net' => round($net, 2),
                'fees' => round($fees, 2),
                'refunded' => round($refunded, 2),
                'refund_rate' => $refundRate,
                'pending_amount' => round($pendingAmount, 2),
                'pending_count' => $pendingCount,
                'paid_count' => $paidCount,
                'completed_count' => $completedCount,
                'by_gateway' => collect($byGateway)->map(fn ($v) => round($v, 2))->toArray(),
                'by_plan' => collect($byPlan)->map(fn ($v) => round($v, 2))->toArray(),
                'monthly_labels' => $monthlyLabels,
                'monthly_gross' => $monthlyData['gross'],
                'monthly_net' => $monthlyData['net'],
                'mrr' => $mrr,
                'arr' => $mrr * 12,
                'base_currency' => $baseCurrency,
            ];
        });
    }

    public function getSubscriptionStats(?string $period, ?string $startDate = null, ?string $endDate = null, ?int $planId = null): array
    {
        $range = $this->dateFilter->resolveDateRange($period, $startDate, $endDate);
        $cacheKey = $this->dateFilter->cacheKey('sa:dash:subs', $period, $startDate, $endDate).":{$planId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($range, $planId) {
            $start = $range['start'];
            $end = $range['end'];

            $baseQuery = Subscription::withoutWorkspace();
            if ($start && $end) {
                $baseQuery->whereBetween('created_at', [$start, $end]);
            }
            if ($planId) {
                $baseQuery->where('plan_id', $planId);
            }

            $active = (clone $baseQuery)->whereIn('status', [
                SubscriptionStatus::Active->value, SubscriptionStatus::Trialing->value,
            ])->count();
            $canceled = (clone $baseQuery)->where('status', SubscriptionStatus::Canceled->value)->count();
            $expired = (clone $baseQuery)->where('status', SubscriptionStatus::Expired->value)->count();
            $pastDue = (clone $baseQuery)->where('status', SubscriptionStatus::PastDue->value)->count();
            $total = $active + $canceled + $expired + $pastDue;

            $byPlan = SubscriptionPlan::withCount(['subscriptions' => function ($q) use ($start, $end, $planId) {
                if ($start && $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                }
                if ($planId) {
                    $q->where('plan_id', $planId);
                }
            }])->get()->pluck('subscriptions_count', 'slug');

            $churnRate = $total > 0 ? round($canceled / max($total, 1) * 100, 2) : 0;

            $avgLifetime = Subscription::withoutWorkspace()->whereNotNull('ends_at')
                ->selectRaw('AVG(DATEDIFF(ends_at, starts_at)) as avg_days')
                ->value('avg_days');

            return [
                'active' => $active,
                'canceled' => $canceled,
                'expired' => $expired,
                'suspended' => $pastDue,
                'total' => $total,
                'by_plan' => $byPlan->toArray(),
                'churn_rate' => $churnRate,
                'avg_lifetime_days' => round((float) ($avgLifetime ?? 0)),
                'monthly_labels' => $this->dateFilter->monthLabels($start, $end),
            ];
        });
    }

    public function getTeamPerformance(?string $period, ?string $startDate = null, ?string $endDate = null, ?int $memberId = null): array
    {
        $range = $this->dateFilter->resolveDateRange($period, $startDate, $endDate);
        $cacheKey = $this->dateFilter->cacheKey('sa:dash:team', $period, $startDate, $endDate).":{$memberId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($range, $memberId) {
            $start = $range['start'];
            $end = $range['end'];

            $roleSlugs = ['super_admin', 'deputy_super_admin', 'platform_manager', 'billing_manager', 'support_team', 'technical_team', 'qa_team'];
            $members = User::whereHas('roles', fn ($q) => $q->whereIn('slug', $roleSlugs))
                ->with(['roles' => fn ($q) => $q->whereIn('slug', $roleSlugs)])
                ->when($memberId, fn ($q) => $q->where('id', $memberId))
                ->orderBy('name')
                ->get();

            $baseCurrency = $this->currencyHelper->baseCurrency();
            $converter = fn ($amount, $currency) => $this->currencyHelper->convert((float) $amount, $currency, $baseCurrency);

            $results = [];
            foreach ($members as $member) {
                $verQuery = PaymentVerification::where('verified_by', $member->id);
                $refQuery = Payment::withoutWorkspace()->where('refunded_by', $member->id);

                if ($start && $end) {
                    $verQuery->whereBetween('verified_at', [$start, $end]);
                    $refQuery->whereBetween('refunded_at', [$start, $end]);
                }

                $verCount = (clone $verQuery)->count();
                $verTotal = 0;
                $verPayments = (clone $verQuery)->with('payment')->get();
                foreach ($verPayments as $v) {
                    if ($v->payment) {
                        $verTotal += $converter($v->payment->amount, $v->payment->currency);
                    }
                }

                $refCount = (clone $refQuery)->count();
                $refAmount = 0;
                $refunds = (clone $refQuery)->selectRaw('currency, SUM(amount) as total')->groupBy('currency')->get();
                foreach ($refunds as $r) {
                    $refAmount += $converter($r->total, $r->currency);
                }

                $lastVerification = (clone $verQuery)->latest('verified_at')->value('verified_at');

                $results[] = [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'role' => $member->roles->first()?->name ?? '—',
                    'role_slug' => $member->roles->first()?->slug ?? '',
                    'verifications_count' => $verCount,
                    'verifications_total' => round($verTotal, 2),
                    'refunds_count' => $refCount,
                    'refunds_total' => round($refAmount, 2),
                    'last_activity' => $lastVerification,
                ];
            }

            $allRoles = Role::whereIn('slug', $roleSlugs)->get()->map(fn ($r) => [
                'slug' => $r->slug,
                'name' => $r->name,
                'members_count' => $members->filter(fn ($m) => $m->roles->first()?->slug === $r->slug)->count(),
            ])->toArray();

            return [
                'members' => $results,
                'roles' => $allRoles,
                'base_currency' => $baseCurrency,
            ];
        });
    }

    private function getRevenueTotals(?Carbon $start, ?Carbon $end): array
    {
        $paid = Payment::withoutWorkspace()->where('status', PaymentStatus::CheckoutPaid->value)->whereNull('refunded_at');
        $pending = Payment::withoutWorkspace()->where('status', PaymentStatus::CheckoutPending->value);
        $refunded = Payment::withoutWorkspace()->whereNotNull('refunded_at');

        if ($start && $end) {
            $paid->whereBetween('paid_at', [$start, $end]);
            $pending->whereBetween('created_at', [$start, $end]);
        }

        $baseCurrency = $this->currencyHelper->baseCurrency();
        $converter = fn ($amount, $currency) => $this->currencyHelper->convert((float) $amount, $currency, $baseCurrency);

        $paidByCur = (clone $paid)->selectRaw('currency, SUM(amount) as gross, SUM(COALESCE(gateway_fee,0)) as fees, SUM(COALESCE(tax_added,0)) as taxes, COUNT(*) as count')->groupBy('currency')->get()->keyBy('currency');
        $pendingByCur = (clone $pending)->selectRaw('currency, SUM(amount) as total, COUNT(*) as count')->groupBy('currency')->get()->keyBy('currency');

        $gross = 0;
        $net = 0;
        $fees = 0;
        $completedCount = 0;
        foreach ($paidByCur as $cur => $row) {
            $gInBase = $converter($row->gross, $cur);
            $fInBase = $converter($row->fees, $cur);
            $gross += $gInBase;
            $fees += $fInBase;
            $net += $gInBase - $fInBase - $converter($row->taxes, $cur);
            $completedCount += $row->count;
        }

        $pendingAmount = 0;
        $pendingCount = 0;
        foreach ($pendingByCur as $cur => $row) {
            $pendingAmount += $converter($row->total, $cur);
            $pendingCount += $row->count;
        }

        return [
            'gross' => $gross, 'net' => $net, 'fees' => $fees,
            'completed_count' => $completedCount,
            'pending_count' => $pendingCount, 'pending_amount' => $pendingAmount,
        ];
    }

    private function getMonthlyRevenue(?Carbon $start, ?Carbon $end, ?string $gateway = null, ?int $planId = null): array
    {
        $months = $this->dateFilter->monthLabels($start, $end);
        $gross = [];
        $net = [];
        $baseCurrency = $this->currencyHelper->baseCurrency();
        $converter = fn ($amount, $currency) => $this->currencyHelper->convert((float) $amount, $currency, $baseCurrency);

        foreach ($months as $i => $label) {
            $mStart = $end ? (clone $end)->subMonths(count($months) - 1 - $i)->startOfMonth() : now()->subMonths(count($months) - 1 - $i)->startOfMonth();
            $mEnd = (clone $mStart)->endOfMonth();

            $q = Payment::withoutWorkspace()->where('status', PaymentStatus::CheckoutPaid->value)->whereNull('refunded_at')
                ->whereBetween('paid_at', [$mStart, $mEnd]);
            if ($gateway) {
                $q->where('method', $gateway);
            }
            if ($planId) {
                $q->whereHas('subscription', fn ($sq) => $sq->where('plan_id', $planId));
            }

            $byCur = (clone $q)->selectRaw('currency, SUM(amount) as total, SUM(COALESCE(gateway_fee,0)) as fees, SUM(COALESCE(tax_added,0)) as taxes')->groupBy('currency')->get();

            $mg = 0;
            $mn = 0;
            foreach ($byCur as $row) {
                $t = $converter($row->total, $row->currency);
                $f = $converter($row->fees, $row->currency);
                $tx = $converter($row->taxes, $row->currency);
                $mg += $t;
                $mn += $t - $f - $tx;
            }
            $gross[] = round($mg, 2);
            $net[] = round($mn, 2);
        }

        return ['gross' => $gross, 'net' => $net];
    }

    private function calculateMrr(): float
    {
        $activeSubs = Subscription::withoutWorkspace()->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Trialing->value])
            ->with('plan.planPrices')->get();

        $mrr = 0;
        $baseCurrency = $this->currencyHelper->baseCurrency();
        $converter = fn ($amount, $currency) => $this->currencyHelper->convert((float) $amount, $currency, $baseCurrency);

        foreach ($activeSubs as $sub) {
            $price = $sub->plan?->planPrices->first();
            if ($price) {
                $monthly = match ($price->billing_period) {
                    'yearly' => $price->price / 12,
                    'quarterly' => $price->price / 3,
                    'semestrial' => $price->price / 6,
                    default => $price->price,
                };
                $mrr += $converter($monthly, $price->currency);
            }
        }

        return round($mrr, 2);
    }
}
