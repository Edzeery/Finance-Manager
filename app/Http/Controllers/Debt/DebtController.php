<?php

namespace App\Http\Controllers\Debt;

use App\Contracts\Repositories\DebtRepositoryInterface;
use App\Enums\DebtStatus;
use App\Enums\DebtType;
use App\Http\Controllers\BaseCrudController;
use App\Http\Requests\Debt\StoreDebtPaymentRequest;
use App\Http\Requests\Debt\StoreDebtRequest;
use App\Http\Requests\Debt\UpdateDebtRequest;
use App\Models\Debt;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class DebtController extends BaseCrudController
{
    protected string $model = Debt::class;

    public function __construct(
        private DebtRepositoryInterface $debtRepo,
    ) {}

    protected function getModelClass(): string
    {
        return Debt::class;
    }

    protected function getRoutePrefix(): string
    {
        return 'debt';
    }

    protected function getViewPrefix(): string
    {
        return 'debt';
    }

    protected function getLangPrefix(): string
    {
        return 'debt';
    }

    protected function getIcon(): string
    {
        return 'bi-credit-card-2-front';
    }

    protected function getRepositoryInterface(): string
    {
        return DebtRepositoryInterface::class;
    }

    protected function getRepository()
    {
        return $this->debtRepo;
    }

    protected function getBulkDeleteRedirect(): string
    {
        return 'debt.index';
    }

    protected function getBulkRestoreRedirect(): string
    {
        return 'debt.index';
    }

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()->resourceBreadcrumbs('general.debt', 'debt.index', 'bi-credit-card-2-front');

        $tab = $request->input('tab', $request->boolean('trashed') ? 'trashed' : 'all');
        $filters = array_merge(
            $request->only(['status', 'type', 'search', 'per_page']),
            ['trashed' => $tab === 'trashed']
        );
        if ($tab === 'paid') {
            $filters['status'] = 'paid';
        } elseif ($tab === 'overdue') {
            $filters['status'] = 'overdue';
        } elseif ($tab === 'active') {
            $filters['status'] = null;
        }

        $debts = $this->debtRepo->forUser(filters: $filters);

        $debtStats = $this->debtRepo->stats();
        $totalOwed = $debtStats['totalOwed'];
        $totalOwing = $debtStats['totalOwing'];
        $paidOwed = $debtStats['paidOwed'];
        $paidOwing = $debtStats['paidOwing'];

        $tabs = $this->buildTabs([
            'all' => ['label' => __('general.all'), 'scope' => 'active'],
            'active' => ['label' => __('debt.active'), 'scope' => fn ($q) => $q->whereIn('status', ['active', 'partial'])],
            'paid' => ['label' => __('debt.paid'), 'scope' => fn ($q) => $q->where('status', 'paid')],
            'overdue' => ['label' => __('debt.overdue'), 'scope' => 'overdue'],
            'trashed' => ['label' => __('general.trash'), 'scope' => fn ($q) => $q->onlyTrashed()],
        ]);

        $typeSubTabs = [
            '' => ['label' => __('general.all')],
            DebtType::Owed->value => ['label' => __('debt.owed')],
            DebtType::Owing->value => ['label' => __('debt.owing')],
        ];

        return view('debt.index', $this->withBreadcrumbs(compact('debts', 'totalOwed', 'totalOwing', 'paidOwed', 'paidOwing', 'tab', 'tabs', 'typeSubTabs')));
    }

    public function create()
    {
        $expenseCategories = ExpenseCategory::where('is_active', true)->orderBy('sort_order')->get();
        $incomeCategories = IncomeCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('debt.create', compact('expenseCategories', 'incomeCategories'));
    }

    public function store(StoreDebtRequest $request)
    {
        $data = $request->validated();
        $data['paid_amount'] ??= 0;
        $data['user_id'] = auth()->id();

        if ((float) $data['paid_amount'] > 0 && (float) $data['paid_amount'] >= (float) $data['total_amount']) {
            $data['status'] = DebtStatus::Paid->value;
        } elseif ((float) $data['paid_amount'] > 0) {
            $data['status'] = DebtStatus::Partial->value;
        }

        $debt = $this->debtRepo->create($data);

        if ((float) $data['paid_amount'] > 0) {
            $debt->payments()->create([
                'amount' => $data['paid_amount'],
                'payment_date' => now(),
                'notes' => 'Initial payment',
            ]);
        }

        return redirect()->route('debt.index')
            ->with('success', __('messages.debt_created'));
    }

    public function show(Debt $debt)
    {
        $this->authorize('view', $debt);
        $debt->load(['payments.expense', 'payments.income', 'expenseCategory', 'incomeCategory']);

        return view('debt.show', compact('debt'));
    }

    public function edit(Debt $debt)
    {
        $this->authorize('update', $debt);

        $expenseCategories = ExpenseCategory::where('is_active', true)->orderBy('sort_order')->get();
        $incomeCategories = IncomeCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('debt.edit', compact('debt', 'expenseCategories', 'incomeCategories'));
    }

    public function update(UpdateDebtRequest $request, Debt $debt)
    {
        $this->authorize('update', $debt);

        $data = $request->validated();
        $data['paid_amount'] ??= 0;

        if ((float) $data['paid_amount'] >= (float) $data['total_amount']) {
            $data['status'] = DebtStatus::Paid->value;
        } elseif ((float) $data['paid_amount'] > 0) {
            $data['status'] = DebtStatus::Partial->value;
        }

        $this->debtRepo->update($debt, $data);

        return redirect()->route('debt.index')
            ->with('success', __('messages.debt_updated'));
    }

    public function addPayment(StoreDebtPaymentRequest $request, Debt $debt)
    {
        $this->authorize('addPayment', $debt);

        if ((float) $request->amount > (float) $debt->remaining_amount) {
            return redirect()->back()
                ->withErrors(['amount' => __('validation.payment_exceeds_remaining', [
                    'amount' => number_format($debt->remaining_amount, 2),
                ])])
                ->withInput();
        }

        $debt->payments()->create($request->validated());

        return redirect()->route('debt.show', $debt)
            ->with('success', __('messages.payment_added'));
    }
}
