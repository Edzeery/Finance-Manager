<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ChartDataServiceInterface;
use App\Contracts\Services\DashboardServiceInterface;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\Budget;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Services\DateFilterService;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    use HasBreadcrumbs;

    public function __construct(
        private DashboardServiceInterface $dashboardService,
        private ChartDataServiceInterface $chartDataService,
        private DateFilterService $dateFilter,
    ) {}

    public function index()
    {
        $this->resetBreadcrumbs()->homeBreadcrumb();

        $period = request('period', 'all_time');
        $startDate = request('start_date');
        $endDate = request('end_date');

        $kpi = $this->dashboardService->getKpiData($period, $startDate, $endDate);

        $incomeExpenseDto = $this->chartDataService->monthlyIncomeExpense($period, $startDate, $endDate);
        $expenseCategoriesDto = $this->chartDataService->expenseByCategory($period, $startDate, $endDate);
        $growthDto = $this->chartDataService->netBalanceHistory($period, $startDate, $endDate);

        $incomeExpense = [
            'labels' => $incomeExpenseDto->labels,
            'incomeData' => $incomeExpenseDto->datasets[0]['data'] ?? [],
            'expenseData' => $incomeExpenseDto->datasets[1]['data'] ?? [],
        ];

        $expenseCategories = [
            'labels' => $expenseCategoriesDto->labels,
            'data' => $expenseCategoriesDto->datasets[0]['data'] ?? [],
            'colors' => $expenseCategoriesDto->datasets[0]['colors'] ?? [],
        ];

        $growth = [
            'labels' => $growthDto->labels,
            'data' => $growthDto->datasets[0]['data'] ?? [],
        ];

        $range = $this->dateFilter->resolveDateRange($period, $startDate, $endDate);
        $recentTransactions = $this->getRecentTransactions($range['start'], $range['end']);

        $goals = FinancialGoal::inProgress()
            ->latest()
            ->take(4)
            ->get();

        $budgetAlerts = Budget::active()
            ->current()
            ->with('categories')
            ->get()
            ->filter(fn ($b) => $b->is_exceeded)
            ->take(4);

        $debtReminders = Debt::active()
            ->whereNotNull('due_date')
            ->where('due_date', '<=', now()->addDays(30))
            ->orderBy('due_date')
            ->take(4)
            ->get();

        $periods = $this->dateFilter->getPeriods();

        return response()
            ->view('dashboard.index', $this->withBreadcrumbs(compact(
                'kpi', 'incomeExpense', 'expenseCategories', 'growth',
                'recentTransactions', 'goals', 'budgetAlerts', 'debtReminders',
                'period', 'startDate', 'endDate', 'periods',
            )))
            ->header('Cache-Control', 'no-store, must-revalidate');
    }

    private function getRecentTransactions($start, $end): Collection
    {
        $locale = app()->getLocale();

        $incomeQuery = Income::active()->with('category');
        $expenseQuery = Expense::active()->with('category');

        if ($start && $end) {
            $incomeQuery->whereBetween('date', [$start, $end]);
            $expenseQuery->whereBetween('date', [$start, $end]);
        }

        $recentIncomes = $incomeQuery
            ->latest('date')
            ->take(5)
            ->get()
            ->map(fn ($i) => [
                'date' => $i->date,
                'description' => $i->description,
                'category' => $i->category?->{'name_'.$locale} ?? '—',
                'amount' => $i->amount,
                'type' => 'income',
            ]);

        $recentExpenses = $expenseQuery
            ->latest('date')
            ->take(5)
            ->get()
            ->map(fn ($e) => [
                'date' => $e->date,
                'description' => $e->description,
                'category' => $e->category?->{'name_'.$locale} ?? '—',
                'amount' => $e->amount,
                'type' => 'expense',
            ]);

        return collect()
            ->merge($recentIncomes)
            ->merge($recentExpenses)
            ->sortByDesc('date')
            ->take(10);
    }
}
