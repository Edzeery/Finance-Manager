<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TransactionController extends Controller
{
    use HasBreadcrumbs;

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()
            ->homeBreadcrumb()
            ->addBreadcrumb(__('transactions.title'), route('transactions.index'));

        $locale = app()->getLocale();
        $perPage = (int) $request->input('per_page', 15);
        $search = $request->input('search');
        $tab = $request->input('tab', 'all');
        $type = $tab === 'all' ? $request->input('type') : ($tab === 'all' ? null : $tab);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $sortField = $request->input('sort', 'date');
        $sortDir = $request->input('direction', 'desc');

        $incomesQuery = Income::active()
            ->with('category:id,name_ar,name_fr,name_en,color');

        $expensesQuery = Expense::active()
            ->with('category:id,name_ar,name_fr,name_en,color');

        if ($search) {
            $incomesQuery->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%");
            });
            $expensesQuery->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        if ($dateFrom) {
            $incomesQuery->where('date', '>=', $dateFrom);
            $expensesQuery->where('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $incomesQuery->where('date', '<=', $dateTo);
            $expensesQuery->where('date', '<=', $dateTo);
        }

        $incomeCount = (clone $incomesQuery)->count();
        $expenseCount = (clone $expensesQuery)->count();

        $incomes = $incomesQuery->get()->map(fn($i) => $this->mapTransaction($i, 'income', $locale));
        $expenses = $expensesQuery->get()->map(fn($e) => $this->mapTransaction($e, 'expense', $locale));

        $all = $incomes->concat($expenses);

        if ($type && in_array($type, ['income', 'expense'])) {
            $all = $all->where('type', $type);
        }

        $sortField = match ($sortField) {
            'amount' => 'amount',
            'type' => 'type',
            'category' => 'category_name',
            default => 'date',
        };

        $all = $sortDir === 'asc'
            ? $all->sortBy($sortField, SORT_REGULAR, false)
            : $all->sortByDesc($sortField);

        $all = $all->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $total = $all->count();
        $items = $all->forPage($page, $perPage)->values();

        $transactions = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        $tabs = [
            'all' => ['label' => __('general.all'), 'count' => $incomeCount + $expenseCount],
            'income' => ['label' => __('transactions.income'), 'count' => $incomeCount],
            'expense' => ['label' => __('transactions.expense'), 'count' => $expenseCount],
        ];

        $categories = $this->getCategoryOptions($locale);

        return view('transactions.index', $this->withBreadcrumbs(compact(
            'transactions', 'search', 'tab', 'type', 'dateFrom', 'dateTo', 'sortField', 'sortDir', 'perPage', 'categories', 'tabs'
        )));
    }

    private function mapTransaction($model, string $type, string $locale): array
    {
        $category = $model->category;

        return [
            'id' => $model->id,
            'type' => $type,
            'amount' => (float) $model->amount,
            'date' => $model->date,
            'description' => $model->description ?? '',
            'category_name' => $category?->{'name_' . $locale} ?? '—',
            'category_color' => $category?->color ?? '#64748B',
            'category_id' => $category?->id,
            'is_archived' => (bool) $model->is_archived,
            'created_at' => $model->created_at,
        ];
    }

    private function getCategoryOptions(string $locale): array
    {
        $incomeCats = \App\Models\IncomeCategory::orderBy('name_' . $locale)
            ->get(['id', 'name_ar', 'name_fr', 'name_en'])
            ->map(fn($c) => [
                'id' => 'income_' . $c->id,
                'name' => $c->{'name_' . $locale},
            ]);

        $expenseCats = \App\Models\ExpenseCategory::orderBy('name_' . $locale)
            ->get(['id', 'name_ar', 'name_fr', 'name_en'])
            ->map(fn($c) => [
                'id' => 'expense_' . $c->id,
                'name' => $c->{'name_' . $locale},
            ]);

        return $incomeCats->concat($expenseCats)->sortBy('name')->values()->all();
    }
}
