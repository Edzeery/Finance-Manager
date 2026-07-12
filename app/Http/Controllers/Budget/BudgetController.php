<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\BaseCrudController;
use App\Http\Requests\Budget\StoreBudgetRequest;
use App\Http\Requests\Budget\UpdateBudgetRequest;
use App\Models\Budget;
use App\Models\ExpenseCategory;
use App\Contracts\Repositories\BudgetRepositoryInterface;
use Illuminate\Http\Request;

class BudgetController extends BaseCrudController
{
    protected string $model = \App\Models\Budget::class;

    public function __construct(
        private BudgetRepositoryInterface $budgetRepo,
    ) {}

    protected function getModelClass(): string
    {
        return Budget::class;
    }

    protected function getRoutePrefix(): string
    {
        return 'budget';
    }

    protected function getViewPrefix(): string
    {
        return 'budget';
    }

    protected function getLangPrefix(): string
    {
        return 'budget';
    }

    protected function getIcon(): string
    {
        return 'bi-calculator-fill';
    }

    protected function getRepositoryInterface(): string
    {
        return BudgetRepositoryInterface::class;
    }

    protected function getRepository()
    {
        return $this->budgetRepo;
    }

    protected function beforeDestroy($model): void
    {
        $model->categories()->delete();
    }

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()->resourceBreadcrumbs('general.budget', 'budget.index', 'bi-calculator-fill');

        $tab = $request->input('tab', $request->boolean('trashed') ? 'trashed' : 'all');
        $filters = array_merge(
            $request->only(['search', 'per_page']),
            ['trashed' => $tab === 'trashed']
        );
        if ($tab === 'inactive') {
            $filters['inactive'] = true;
        }
        if ($tab === 'active') {
            $filters['active'] = true;
        }

        $budgets = $this->budgetRepo->forUser(filters: $filters);

        $tabs = $this->buildTabs([
            'all' => ['label' => __('general.all'), 'scope' => 'active'],
            'active' => ['label' => __('general.active'), 'scope' => fn($q) => $q->where('is_active', true)],
            'inactive' => ['label' => __('general.inactive'), 'scope' => fn($q) => $q->where('is_active', false)],
            'trashed' => ['label' => __('general.trash'), 'scope' => fn($q) => $q->onlyTrashed()],
        ]);

        return view('budget.index', $this->withBreadcrumbs(compact('budgets', 'tab', 'tabs')));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('budget.create', compact('categories'));
    }

    public function store(StoreBudgetRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        $budget = $this->budgetRepo->create($data);

        foreach ($categories as $cat) {
            $budget->categories()->create([
                'expense_category_id' => $cat['category_id'],
                'allocated_amount' => $cat['allocated_amount'],
                'spent_amount' => 0,
            ]);
        }

        return redirect()->route('budget.index')
            ->with('success', __('messages.budget_created'));
    }

    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);
        $budget->load('categories.category');

        return view('budget.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);
        $budget->load('categories');
        $categories = ExpenseCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('budget.edit', compact('budget', 'categories'));
    }

    public function update(UpdateBudgetRequest $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $data = $request->validated();
        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        $this->budgetRepo->update($budget, $data);

        $existingCategories = $budget->categories()->get()->keyBy('expense_category_id');

        foreach ($categories as $cat) {
            if ($existingCategories->has($cat['category_id'])) {
                $existingCategories->get($cat['category_id'])->update([
                    'allocated_amount' => $cat['allocated_amount'],
                ]);
                $existingCategories->forget($cat['category_id']);
            } else {
                $budget->categories()->create([
                    'expense_category_id' => $cat['category_id'],
                    'allocated_amount' => $cat['allocated_amount'],
                    'spent_amount' => 0,
                ]);
            }
        }

        foreach ($existingCategories as $removed) {
            $removed->delete();
        }

        return redirect()->route('budget.index')
            ->with('success', __('messages.budget_updated'));
    }
}
