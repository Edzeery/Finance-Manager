<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\BaseCrudController;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Contracts\Repositories\ExpenseRepositoryInterface;
use Illuminate\Http\Request;

class ExpenseController extends BaseCrudController
{
    protected string $model = \App\Models\Expense::class;

    public function __construct(
        private ExpenseRepositoryInterface $expenseRepo,
    ) {}

    protected function getModelClass(): string
    {
        return Expense::class;
    }

    protected function getRoutePrefix(): string
    {
        return 'expense';
    }

    protected function getViewPrefix(): string
    {
        return 'expense';
    }

    protected function getLangPrefix(): string
    {
        return 'expense';
    }

    protected function getIcon(): string
    {
        return 'bi-cart';
    }

    protected function getRepositoryInterface(): string
    {
        return ExpenseRepositoryInterface::class;
    }

    protected function getRepository()
    {
        return $this->expenseRepo;
    }

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()->resourceBreadcrumbs('general.expense', 'expense.index', 'bi-cart');

        $tab = $request->input('tab', $request->boolean('trashed') ? 'trashed' : 'all');
        $filters = array_merge(
            $request->only(['category', 'type', 'date_from', 'date_to', 'search', 'per_page']),
            ['trashed' => $tab === 'trashed']
        );
        if ($tab === 'archived') {
            $filters['archived'] = true;
        }
        if ($tab === 'active') {
            $filters['active'] = true;
        }

        $expenses = $this->expenseRepo->forUser(filters: $filters);
        $categories = ExpenseCategory::where('is_active', true)->orderBy('sort_order')->get();

        $now = now();
        $totalExpense = $this->expenseRepo->monthlyTotal(year: $now->year, month: $now->month);

        $tabs = $this->buildTabs([
            'all' => ['label' => __('general.all'), 'scope' => 'active'],
            'active' => ['label' => __('general.active'), 'scope' => fn($q) => $q->active()->where('is_archived', false)],
            'archived' => ['label' => __('general.archived'), 'scope' => 'archived'],
            'trashed' => ['label' => __('general.trash'), 'scope' => fn($q) => $q->onlyTrashed()],
        ]);

        return view('expense.index', $this->withBreadcrumbs(compact(
            'expenses', 'categories', 'totalExpense', 'tab', 'tabs'
        )));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('expense.create', compact('categories'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $this->expenseRepo->create(array_merge($request->validated(), [
            'user_id' => auth()->id(),
        ]));

        return redirect()->route('expense.index')
            ->with('success', __('messages.expense_created'));
    }

    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);
        $categories = ExpenseCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('expense.edit', compact('expense', 'categories'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $this->authorize('update', $expense);
        $this->expenseRepo->update($expense, $request->validated());

        return redirect()->route('expense.index')
            ->with('success', __('messages.expense_updated'));
    }

    public function archive(Expense $expense)
    {
        $this->authorize('archive', $expense);
        $expense->update(['is_archived' => !$expense->is_archived]);

        return redirect()->back()->with('success', __('messages.expense_archived'));
    }
}
