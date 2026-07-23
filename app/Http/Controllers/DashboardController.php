<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ChartDataServiceInterface;
use App\Contracts\Services\DashboardServiceInterface;
use App\Enums\AssetType;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\Asset;
use App\Models\Budget;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Services\DateFilterService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        $currentTab = request('tab', 'overview');

        $kpi = $this->dashboardService->getKpiData($period, $startDate, $endDate);
        $periods = $this->dateFilter->getPeriods();
        $range = $this->dateFilter->resolveDateRange($period, $startDate, $endDate);

        $data = match ($currentTab) {
            'debts' => $this->getDebtsData($period, $startDate, $endDate),
            'transactions' => $this->getTransactionsData($period, $startDate, $endDate),
            'assets' => $this->getAssetsData(),
            'budgets' => $this->getBudgetsData(),
            'goals' => $this->getGoalsData(),
            default => $this->getOverviewData($period, $startDate, $endDate, $range),
        };

        $data['current_tab'] = $currentTab;

        return response()
            ->view('dashboard.index', $this->withBreadcrumbs(compact(
                'kpi', 'periods', 'period', 'startDate', 'endDate', 'currentTab', 'data',
            )))
            ->header('Cache-Control', 'no-store, must-revalidate');
    }

    private function getOverviewData(string $period, ?string $startDate, ?string $endDate, array $range): array
    {
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

        return [
            'incomeExpense' => $incomeExpense,
            'expenseCategories' => $expenseCategories,
            'growth' => $growth,
            'recentTransactions' => $this->getRecentTransactions($range['start'], $range['end']),
            'goals' => FinancialGoal::inProgress()->latest()->take(4)->get(),
            'budgetAlerts' => Budget::active()->current()->with('categories')->get()
                ->filter(fn ($b) => $b->is_exceeded)->take(4),
            'debtReminders' => Debt::active()
                ->whereNotNull('due_date')
                ->where('due_date', '<=', now()->addDays(30))
                ->orderBy('due_date')
                ->take(4)
                ->get(),
        ];
    }

    private function getDebtsData(string $period, ?string $startDate, ?string $endDate): array
    {
        $debtType = request('debt_type', '');
        $debtStatus = request('debt_status', '');
        $search = request('search', '');

        $query = Debt::query()->with('payments');

        if ($debtType && in_array($debtType, ['owed', 'owing'])) {
            $query->where('type', $debtType);
        }

        if ($debtStatus && in_array($debtStatus, ['active', 'partial', 'paid', 'overdue'])) {
            $query->where('status', $debtStatus);
        }

        if ($search) {
            $query->where('counterparty_name', 'like', "%{$search}%");
        }

        $debts = $query->orderBy('due_date', 'desc')->paginate(10, ['*'], 'debts_page');

        $stats = Debt::active()
            ->selectRaw('type, status, COUNT(*) as count, SUM(total_amount) as total_amount, SUM(paid_amount) as paid_amount')
            ->groupBy('type', 'status')
            ->get()
            ->keyBy(fn ($r) => $r->type->value.'_'.$r->status->value);

        $totalOwed = (float) Debt::owed()->active()->sum(DB::raw('total_amount - paid_amount'));
        $totalOwing = (float) Debt::owing()->active()->sum(DB::raw('total_amount - paid_amount'));
        $totalPaidAll = (float) Debt::sum('paid_amount');
        $totalAllDebts = (float) Debt::sum('total_amount');
        $collectionRate = $totalAllDebts > 0 ? round($totalPaidAll / $totalAllDebts * 100, 1) : 0;
        $overdueCount = Debt::overdue()->count();
        $activeCount = Debt::active()->count();

        return [
            'debts' => $debts,
            'stats' => $stats,
            'totalOwed' => $totalOwed,
            'totalOwing' => $totalOwing,
            'totalPaidAll' => $totalPaidAll,
            'collectionRate' => $collectionRate,
            'overdueCount' => $overdueCount,
            'activeCount' => $activeCount,
        ];
    }

    private function getTransactionsData(string $period, ?string $startDate, ?string $endDate): array
    {
        $type = request('txn_type', '');
        $categoryId = request('category_id', '');
        $search = request('search', '');

        $range = $this->dateFilter->resolveDateRange($period, $startDate, $endDate);

        $incomeQuery = Income::active()->with('category');
        $expenseQuery = Expense::active()->with('category');

        if ($range['start'] && $range['end']) {
            $incomeQuery->whereBetween('date', [$range['start'], $range['end']]);
            $expenseQuery->whereBetween('date', [$range['start'], $range['end']]);
        }

        if ($categoryId) {
            $incomeQuery->where('category_id', $categoryId);
            $expenseQuery->where('category_id', $categoryId);
        }

        if ($search) {
            $incomeQuery->where('description', 'like', "%{$search}%");
            $expenseQuery->where('description', 'like', "%{$search}%");
        }

        $locale = app()->getLocale();

        $incomes = $type !== 'expense' ? $incomeQuery->latest('date')->get()->map(fn ($i) => [
            'id' => $i->id,
            'date' => $i->date,
            'description' => $i->description,
            'category' => $i->category?->{'name_'.$locale} ?? '—',
            'amount' => $i->amount,
            'type' => 'income',
        ]) : collect();

        $expenses = $type !== 'income' ? $expenseQuery->latest('date')->get()->map(fn ($e) => [
            'id' => $e->id,
            'date' => $e->date,
            'description' => $e->description,
            'category' => $e->category?->{'name_'.$locale} ?? '—',
            'amount' => $e->amount,
            'type' => 'expense',
        ]) : collect();

        $allTransactions = collect()->merge($incomes)->merge($expenses)->sortByDesc('date')->values();

        $totalIncome = (float) $incomes->sum('amount');
        $totalExpense = (float) $expenses->sum('amount');
        $totalCount = $allTransactions->count();

        $perPage = 15;
        $currentPage = (int) request('txn_page', 1);
        $paginatedItems = $allTransactions->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedTransactions = new LengthAwarePaginator($paginatedItems, $totalCount, $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
            'pageName' => 'txn_page',
        ]);

        return [
            'transactions' => $paginatedTransactions,
            'totalCount' => $totalCount,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
        ];
    }

    private function getAssetsData(): array
    {
        $assetType = request('asset_type', '');

        $query = Asset::query();

        if ($assetType && in_array($assetType, AssetType::values())) {
            $query->where('type', $assetType);
        }

        $assets = $query->orderBy('total_value', 'desc')->paginate(10, ['*'], 'asset_page');

        $totalAssets = (float) Asset::sum('total_value');
        $liquidAssets = (float) Asset::liquid()->sum('total_value');
        $nonLiquid = $totalAssets - $liquidAssets;

        $byType = Asset::selectRaw('type, SUM(total_value) as total_value, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        return [
            'assets' => $assets,
            'totalAssets' => $totalAssets,
            'liquidAssets' => $liquidAssets,
            'nonLiquid' => $nonLiquid,
            'byType' => $byType,
        ];
    }

    private function getBudgetsData(): array
    {
        $budgetStatus = request('budget_status', '');

        $allBudgets = Budget::active()->current()->with('categories')->get();
        $exceededCount = $allBudgets->filter(fn ($b) => $b->is_exceeded)->count();
        $totalAllocated = (float) $allBudgets->sum('total_amount');
        $totalSpent = (float) $allBudgets->sum(fn ($b) => $b->totalSpent);

        $query = Budget::with('categories');

        if ($budgetStatus === 'active') {
            $query->active()->current()->latest();
        } elseif ($budgetStatus === 'exceeded') {
            $budgetIds = $allBudgets->filter(fn ($b) => $b->is_exceeded)->pluck('id')->toArray();
            $query->whereIn('id', $budgetIds)->latest();
        } elseif ($budgetStatus === 'inactive') {
            $query->inactive()->latest();
        } else {
            $query->latest();
        }

        $budgets = $query->paginate(10, ['*'], 'budget_page');

        return [
            'budgets' => $budgets,
            'exceededCount' => $exceededCount,
            'activeCount' => $allBudgets->count(),
            'totalAllocated' => $totalAllocated,
            'totalSpent' => $totalSpent,
        ];
    }

    private function getGoalsData(): array
    {
        $goalStatus = request('goal_status', '');

        $query = FinancialGoal::query();

        if ($goalStatus && in_array($goalStatus, ['in_progress', 'completed', 'cancelled'])) {
            $query->where('status', $goalStatus);
        } else {
            $query->latest();
        }

        $goals = $query->paginate(10, ['*'], 'goal_page');

        $activeGoals = FinancialGoal::inProgress()->count();
        $completedGoals = FinancialGoal::completed()->count();
        $totalTarget = (float) FinancialGoal::sum('target_amount');
        $totalCurrent = (float) FinancialGoal::sum('current_amount');
        $avgProgress = $totalTarget > 0 ? round($totalCurrent / $totalTarget * 100, 1) : 0;

        return [
            'goals' => $goals,
            'activeGoals' => $activeGoals,
            'completedGoals' => $completedGoals,
            'totalTarget' => $totalTarget,
            'totalCurrent' => $totalCurrent,
            'avgProgress' => $avgProgress,
        ];
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
