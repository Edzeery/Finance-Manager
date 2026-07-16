<?php

namespace App\Http\Controllers\Income;

use App\Contracts\Repositories\IncomeRepositoryInterface;
use App\Http\Controllers\BaseCrudController;
use App\Http\Requests\Income\StoreIncomeRequest;
use App\Http\Requests\Income\UpdateIncomeRequest;
use App\Models\Income;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class IncomeController extends BaseCrudController
{
    protected string $model = Income::class;

    public function __construct(
        private IncomeRepositoryInterface $incomeRepo,
    ) {}

    protected function getModelClass(): string
    {
        return Income::class;
    }

    protected function getRoutePrefix(): string
    {
        return 'income';
    }

    protected function getViewPrefix(): string
    {
        return 'income';
    }

    protected function getLangPrefix(): string
    {
        return 'income';
    }

    protected function getIcon(): string
    {
        return 'bi-cash-stack';
    }

    protected function getRepositoryInterface(): string
    {
        return IncomeRepositoryInterface::class;
    }

    protected function getRepository()
    {
        return $this->incomeRepo;
    }

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()->resourceBreadcrumbs('general.income', 'income.index', 'bi-cash-stack');

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

        $incomes = $this->incomeRepo->forUser(filters: $filters);
        $categories = IncomeCategory::where('is_active', true)->orderBy('sort_order')->get();

        $now = now();
        $totalIncome = $this->incomeRepo->monthlyTotal(year: $now->year, month: $now->month);

        $tabs = $this->buildTabs([
            'all' => ['label' => __('general.all'), 'scope' => 'active'],
            'active' => ['label' => __('general.active'), 'scope' => fn ($q) => $q->active()->where('is_archived', false)],
            'archived' => ['label' => __('general.archived'), 'scope' => 'archived'],
            'trashed' => ['label' => __('general.trash'), 'scope' => fn ($q) => $q->onlyTrashed()],
        ]);

        $catSubTabs = collect(['' => ['label' => __('general.all')]])
            ->union($categories->mapWithKeys(fn ($cat) => [$cat->id => ['label' => locale_name($cat)]]))
            ->toArray();

        return view('income.index', $this->withBreadcrumbs(compact(
            'incomes', 'categories', 'totalIncome', 'tab', 'tabs', 'catSubTabs'
        )));
    }

    public function create()
    {
        $categories = IncomeCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('income.create', compact('categories'));
    }

    public function store(StoreIncomeRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['is_recurring'] = $request->boolean('is_recurring');

        $this->incomeRepo->create($data);

        return redirect()->route('income.index')
            ->with('success', __('messages.income_created'));
    }

    public function edit(Income $income)
    {
        $this->authorize('update', $income);
        $categories = IncomeCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('income.edit', compact('income', 'categories'));
    }

    public function update(UpdateIncomeRequest $request, Income $income)
    {
        $this->authorize('update', $income);

        $data = $request->validated();
        $data['is_recurring'] = $request->boolean('is_recurring');

        $this->incomeRepo->update($income, $data);

        return redirect()->route('income.index')
            ->with('success', __('messages.income_updated'));
    }

    public function archive(Income $income)
    {
        $this->authorize('archive', $income);
        $income->update(['is_archived' => ! $income->is_archived]);

        return redirect()->back()->with('success', __('messages.income_archived'));
    }
}
