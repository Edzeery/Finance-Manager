<?php

namespace App\Services;

use App\Contracts\Services\ChartDataServiceInterface;
use App\DTOs\ChartData;
use App\Models\Expense;
use App\Models\Income;
use App\Support\DatabaseHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ChartDataService implements ChartDataServiceInterface
{
    private const CHART_CACHE_TTL = 120;

    public function __construct(
        private DateFilterService $dateFilter,
    ) {}

    public function monthlyIncomeExpense(?string $period = null, ?string $startDate = null, ?string $endDate = null): ChartData
    {
        $cacheKey = $this->dateFilter->cacheKey('chart:monthly', $period, $startDate, $endDate);

        $cached = Cache::remember($cacheKey, self::CHART_CACHE_TTL, function () use ($period, $startDate, $endDate) {
            $range = $this->dateFilter->resolveDateRange($period, $startDate, $endDate);
            $start = $range['start'];
            $end = $range['end'];

            $monthExpr = DatabaseHelper::monthExpression();

            $incomeQuery = Income::active();
            $expenseQuery = Expense::active();

            if ($start && $end) {
                $incomeQuery->whereBetween('date', [$start, $end]);
                $expenseQuery->whereBetween('date', [$start, $end]);
            }

            $incomes = $incomeQuery
                ->selectRaw("$monthExpr as month, SUM(amount) as total")
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $expenses = $expenseQuery
                ->selectRaw("$monthExpr as month, SUM(amount) as total")
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $allMonths = $incomes->keys()->merge($expenses->keys())->unique()->sort()->values();

            if ($allMonths->isEmpty()) {
                return ['labels' => [], 'datasets' => [
                    ['label' => __('dashboard.total_income'), 'data' => []],
                    ['label' => __('dashboard.total_expense'), 'data' => []],
                ]];
            }

            $labels = $allMonths->map(fn ($ym) => Carbon::parse($ym.'-01')->format('M Y'))->toArray();
            $incomeData = [];
            $expenseData = [];

            foreach ($allMonths as $ym) {
                $incomeData[] = (float) ($incomes[$ym] ?? 0);
                $expenseData[] = (float) ($expenses[$ym] ?? 0);
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    ['label' => __('dashboard.total_income'), 'data' => $incomeData],
                    ['label' => __('dashboard.total_expense'), 'data' => $expenseData],
                ],
            ];
        });

        if (is_array($cached)) {
            return new ChartData($cached['labels'], $cached['datasets']);
        }

        return $cached;
    }

    public function expenseByCategory(?string $period = null, ?string $startDate = null, ?string $endDate = null): ChartData
    {
        $cacheKey = $this->dateFilter->cacheKey('chart:category', $period, $startDate, $endDate);

        $cached = Cache::remember($cacheKey, self::CHART_CACHE_TTL, function () use ($period, $startDate, $endDate) {
            $range = $this->dateFilter->resolveDateRange($period, $startDate, $endDate);
            $start = $range['start'];
            $end = $range['end'];

            $nameField = 'name_'.app()->getLocale();

            $query = Expense::active()
                ->select(
                    "expense_categories.{$nameField} as category_name",
                    'expense_categories.color as category_color',
                    DB::raw('SUM(expenses.amount) as total_amount')
                )
                ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id');

            if ($start && $end) {
                $query->whereBetween('expenses.date', [$start, $end]);
            }

            $results = $query->groupBy('expense_categories.id', 'expense_categories.color')
                ->orderByDesc('total_amount')
                ->get();

            $labels = [];
            $data = [];
            $colors = [];

            foreach ($results as $row) {
                $labels[] = $row->category_name ?? __('general.uncategorized');
                $data[] = (float) $row->total_amount;
                $colors[] = $row->category_color ?? '#64748B';
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    ['data' => $data, 'colors' => $colors],
                ],
            ];
        });

        if (is_array($cached)) {
            return new ChartData($cached['labels'], $cached['datasets']);
        }

        return $cached;
    }

    public function netBalanceHistory(?string $period = null, ?string $startDate = null, ?string $endDate = null): ChartData
    {
        $cacheKey = $this->dateFilter->cacheKey('chart:balance', $period, $startDate, $endDate);

        $cached = Cache::remember($cacheKey, self::CHART_CACHE_TTL, function () use ($period, $startDate, $endDate) {
            $range = $this->dateFilter->resolveDateRange($period, $startDate, $endDate);
            $start = $range['start'];
            $end = $range['end'];

            $monthExpr = DatabaseHelper::monthExpression();

            $incomeQuery = Income::active();
            $expenseQuery = Expense::active();

            if ($start && $end) {
                $incomeQuery->whereBetween('date', [$start, $end]);
                $expenseQuery->whereBetween('date', [$start, $end]);
            }

            $incomes = $incomeQuery
                ->selectRaw("$monthExpr as month, SUM(amount) as total")
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $expenses = $expenseQuery
                ->selectRaw("$monthExpr as month, SUM(amount) as total")
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $allMonths = $incomes->keys()->merge($expenses->keys())->unique()->sort()->values();

            if ($allMonths->isEmpty()) {
                return ['labels' => [], 'datasets' => [
                    ['label' => __('dashboard.net_balance'), 'data' => []],
                ]];
            }

            $labels = $allMonths->map(fn ($ym) => Carbon::parse($ym.'-01')->format('M Y'))->toArray();
            $data = [];
            $balance = 0;

            foreach ($allMonths as $ym) {
                $balance += ((float) ($incomes[$ym] ?? 0)) - ((float) ($expenses[$ym] ?? 0));
                $data[] = $balance;
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    ['label' => __('dashboard.net_balance'), 'data' => $data],
                ],
            ];
        });

        if (is_array($cached)) {
            return new ChartData($cached['labels'], $cached['datasets']);
        }

        return $cached;
    }

    public static function clearCache(?int $userId = null, ?int $workspaceId = null): void
    {
        app(DateFilterService::class)->bumpCacheVersion($userId, $workspaceId);
    }
}
