<?php

namespace App\Services;

use App\Contracts\Services\DashboardServiceInterface;
use App\DTOs\KpiData;
use App\Models\Asset;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Support\Facades\Cache;

class DashboardService implements DashboardServiceInterface
{
    private const KPI_CACHE_TTL = 120;

    public function __construct(
        private DateFilterService $dateFilter,
    ) {}

    public function getKpiData(?string $period = null, ?string $startDate = null, ?string $endDate = null): KpiData
    {
        $cacheKey = $this->dateFilter->cacheKey('dashboard:kpi', $period, $startDate, $endDate);

        $range = $this->dateFilter->resolveDateRange($period, $startDate, $endDate);
        $start = $range['start'];
        $end = $range['end'];

        $cached = Cache::remember($cacheKey, self::KPI_CACHE_TTL, function () use ($start, $end) {
            $now = now();
            $monthStart = $now->copy()->startOfMonth();
            $monthEnd = $now->copy()->endOfMonth();
            $prevMonthStart = $now->copy()->subMonth()->startOfMonth();
            $prevMonthEnd = $now->copy()->subMonth()->endOfMonth();

            $incomeMonthly = Income::active()
                ->whereBetween('date', [$prevMonthStart, $monthEnd])
                ->selectRaw('
                    SUM(CASE WHEN date BETWEEN ? AND ? THEN amount ELSE 0 END) as this_month,
                    SUM(CASE WHEN date BETWEEN ? AND ? THEN amount ELSE 0 END) as prev_month
                ', [$monthStart, $monthEnd, $prevMonthStart, $prevMonthEnd])
                ->first();

            $expenseMonthly = Expense::active()
                ->whereBetween('date', [$prevMonthStart, $monthEnd])
                ->selectRaw('
                    SUM(CASE WHEN date BETWEEN ? AND ? THEN amount ELSE 0 END) as this_month,
                    SUM(CASE WHEN date BETWEEN ? AND ? THEN amount ELSE 0 END) as prev_month
                ', [$monthStart, $monthEnd, $prevMonthStart, $prevMonthEnd])
                ->first();

            $totalIncomeQuery = Income::active();
            $totalExpenseQuery = Expense::active();

            if ($start && $end) {
                $totalIncomeQuery->whereBetween('date', [$start, $end]);
                $totalExpenseQuery->whereBetween('date', [$start, $end]);
            }

            $totalIncomeForPeriod = (float) $totalIncomeQuery->sum('amount');
            $totalExpenseForPeriod = (float) $totalExpenseQuery->sum('amount');

            $totalIncomeAllTime = (float) Income::active()->sum('amount');
            $totalExpenseAllTime = (float) Expense::active()->sum('amount');

            $debtStats = Debt::active()
                ->selectRaw('
                    type,
                    SUM(total_amount) as total_amount,
                    SUM(paid_amount) as paid_amount
                ')
                ->groupBy('type')
                ->get()
                ->keyBy('type');

            $overdueDebts = Debt::overdue()->count();
            $totalAssets = (float) Asset::sum('total_value');

            $incomeThisMonth = (float) ($incomeMonthly?->this_month ?? 0);
            $incomePrevMonth = (float) ($incomeMonthly?->prev_month ?? 0);
            $expenseThisMonth = (float) ($expenseMonthly?->this_month ?? 0);
            $expensePrevMonth = (float) ($expenseMonthly?->prev_month ?? 0);

            $incomeChange = $incomePrevMonth > 0 ? round(($incomeThisMonth - $incomePrevMonth) / $incomePrevMonth * 100, 1) : 0;
            $expenseChange = $expensePrevMonth > 0 ? round(($expenseThisMonth - $expensePrevMonth) / $expensePrevMonth * 100, 1) : 0;

            $owed = $debtStats['owed'] ?? null;
            $owing = $debtStats['owing'] ?? null;

            return [
                'totalIncome' => $totalIncomeForPeriod,
                'totalExpense' => $totalExpenseForPeriod,
                'netBalance' => $totalIncomeForPeriod - $totalExpenseForPeriod,
                'incomeChange' => $incomeChange,
                'expenseChange' => $expenseChange,
                'totalDebts' => ($owed ? (float) $owed->total_amount - (float) $owed->paid_amount : 0),
                'overdueDebts' => $overdueDebts,
                'totalAssets' => $totalAssets,
                'totalSavings' => $totalIncomeAllTime - $totalExpenseAllTime,
                'totalIncomeAllTime' => $totalIncomeAllTime,
                'totalExpenseAllTime' => $totalExpenseAllTime,
                'totalDebtsOwing' => ($owing ? (float) $owing->total_amount - (float) $owing->paid_amount : 0),
            ];
        });

        if (is_array($cached)) {
            return new KpiData(...$cached);
        }

        return $cached;
    }

    public static function clearCache(?int $userId = null, ?int $workspaceId = null): void
    {
        app(DateFilterService::class)->bumpCacheVersion($userId, $workspaceId);
    }
}
