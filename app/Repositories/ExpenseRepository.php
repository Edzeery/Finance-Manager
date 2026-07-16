<?php

namespace App\Repositories;

use App\Contracts\Repositories\ExpenseRepositoryInterface;
use App\Models\BudgetCategory;
use App\Models\Expense;
use App\Repositories\Concerns\FiltersByOwnership;
use App\Repositories\Concerns\HasMonthlyTransactions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ExpenseRepository extends BaseRepository implements ExpenseRepositoryInterface
{
    use FiltersByOwnership;
    use HasMonthlyTransactions;

    public function __construct()
    {
        parent::__construct(new Expense);
    }

    public function forUser(array $filters = []): LengthAwarePaginator
    {
        $query = Expense::with('category');
        $this->applyOwnershipFilter($query, 'expense.view');

        $trashed = $filters['trashed'] ?? false;
        if ($trashed) {
            $query->onlyTrashed();
        } elseif (! empty($filters['archived'])) {
            $query->archived();
        } elseif (! empty($filters['active'])) {
            $query->active()->where('is_archived', false);
        }

        if (! empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (! empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        if (! empty($filters['search'])) {
            $term = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', $term)
                    ->orWhere('notes', 'like', $term);
            });
        }

        $perPage = min((int) ($filters['per_page'] ?? 15), config('finance.per_page_max', 100));

        return $query->orderBy('date', 'desc')->paginate($perPage);
    }

    public function bulkDelete(array $ids): bool
    {
        /** @var Collection<int, Expense> $expenses */
        $expenses = Expense::withTrashed()->whereIn('id', $ids)->get();

        $result = (bool) Expense::whereIn('id', $ids)
            ->delete();

        $this->syncBudgetForExpenses($expenses);

        return $result;
    }

    public function bulkRestore(array $ids): bool
    {
        /** @var Collection<int, Expense> $expenses */
        $expenses = Expense::withTrashed()->whereIn('id', $ids)->get();

        $result = (bool) Expense::withTrashed()
            ->whereIn('id', $ids)
            ->restore();

        $this->syncBudgetForExpenses($expenses);

        return $result;
    }

    /**
     * @param  Collection<int, Expense>  $expenses
     */
    private function syncBudgetForExpenses(Collection $expenses): void
    {
        foreach ($expenses as $expense) {
            $categories = BudgetCategory::whereHas('budget', fn ($q) => $q->where('user_id', $expense->user_id)
            )->where('expense_category_id', $expense->category_id)->get();

            foreach ($categories as $bc) {
                $start = $bc->budget->start_date;
                $end = $bc->budget->end_date ?? now();

                if ($expense->date->between($start, $end)) {
                    $bc->recalculateSpentAmount();
                }
            }
        }
    }
}
