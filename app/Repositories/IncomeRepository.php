<?php

namespace App\Repositories;

use App\Contracts\Repositories\IncomeRepositoryInterface;
use App\Models\Income;
use App\Repositories\Concerns\FiltersByOwnership;
use App\Repositories\Concerns\HasMonthlyTransactions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IncomeRepository extends BaseRepository implements IncomeRepositoryInterface
{
    use FiltersByOwnership;
    use HasMonthlyTransactions;

    public function __construct()
    {
        parent::__construct(new Income);
    }

    public function forUser(array $filters = []): LengthAwarePaginator
    {
        $query = Income::with('category');
        $this->applyOwnershipFilter($query, 'income.view');

        $trashed = $filters['trashed'] ?? false;
        $archived = $filters['archived'] ?? false;
        if ($trashed) {
            $query->onlyTrashed();
        } elseif ($archived) {
            $query->archived();
        } else {
            $query->active();
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
        return (bool) Income::whereIn('id', $ids)
            ->delete();
    }

    public function bulkRestore(array $ids): bool
    {
        return (bool) Income::withTrashed()
            ->whereIn('id', $ids)
            ->restore();
    }
}
