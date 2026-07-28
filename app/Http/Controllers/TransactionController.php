<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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

        $incomeQuery = Income::active()
            ->select(
                'incomes.id',
                DB::raw("'income' as type"),
                'incomes.amount',
                'incomes.date',
                'incomes.description',
                'incomes.is_archived',
                'incomes.created_at',
                'incomes.category_id',
                DB::raw("COALESCE(ic.name_{$locale}, '—') as category_name"),
                DB::raw("COALESCE(ic.color, '#64748B') as category_color")
            )
            ->leftJoin('income_categories as ic', 'incomes.category_id', '=', 'ic.id');

        $expenseQuery = Expense::active()
            ->select(
                'expenses.id',
                DB::raw("'expense' as type"),
                'expenses.amount',
                'expenses.date',
                'expenses.description',
                'expenses.is_archived',
                'expenses.created_at',
                'expenses.category_id',
                DB::raw("COALESCE(ec.name_{$locale}, '—') as category_name"),
                DB::raw("COALESCE(ec.color, '#64748B') as category_color")
            )
            ->leftJoin('expense_categories as ec', 'expenses.category_id', '=', 'ec.id');

        foreach ([$incomeQuery, $expenseQuery] as $q) {
            if ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('description', 'like', "%{$search}%")
                        ->orWhere('amount', 'like', "%{$search}%");
                });
            }
            if ($dateFrom) {
                $q->where('date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $q->where('date', '<=', $dateTo);
            }
        }

        $incomeCount = (clone $incomeQuery)->count();
        $expenseCount = (clone $expenseQuery)->count();

        if ($type === 'income') {
            $query = $incomeQuery;
        } elseif ($type === 'expense') {
            $query = $expenseQuery;
        } else {
            $query = $incomeQuery->unionAll($expenseQuery);
        }

        $sortColumn = match ($sortField) {
            'amount' => 'amount',
            'type' => 'type',
            'category' => 'category_name',
            default => 'date',
        };
        $query->orderBy($sortColumn, $sortDir);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $total = match ($type) {
            'income' => $incomeCount,
            'expense' => $expenseCount,
            default => $incomeCount + $expenseCount,
        };
        $items = $query->offset(($page - 1) * $perPage)->limit($perPage)->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'type' => $item->type,
                'amount' => (float) $item->amount,
                'date' => $item->date,
                'description' => $item->description ?? '',
                'category_name' => $item->category_name ?? '—',
                'category_color' => $item->category_color ?? '#64748B',
                'category_id' => $item->category_id,
                'is_archived' => (bool) $item->is_archived,
                'created_at' => $item->created_at,
            ]);

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

    private function getCategoryOptions(string $locale): array
    {
        $incomeCats = IncomeCategory::orderBy('name_'.$locale)
            ->get(['id', 'name_ar', 'name_fr', 'name_en'])
            ->map(fn ($c) => [
                'id' => 'income_'.$c->id,
                'name' => $c->{'name_'.$locale},
            ]);

        $expenseCats = ExpenseCategory::orderBy('name_'.$locale)
            ->get(['id', 'name_ar', 'name_fr', 'name_en'])
            ->map(fn ($c) => [
                'id' => 'expense_'.$c->id,
                'name' => $c->{'name_'.$locale},
            ]);

        return $incomeCats->concat($expenseCats)->sortBy('name')->values()->all();
    }
}
