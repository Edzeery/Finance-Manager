<?php

namespace App\Repositories;

use App\Contracts\Repositories\BudgetRepositoryInterface;
use App\Models\Budget;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BudgetRepository extends BaseRepository implements BudgetRepositoryInterface
{
    use \App\Repositories\Concerns\FiltersByOwnership;

    public function __construct()
    {
        parent::__construct(new Budget);
    }

    public function forUser(array $filters = []): LengthAwarePaginator
    {
        $query = Budget::with('categories.category');
        $this->applyOwnershipFilter($query, 'budget.view');

        $trashed = $filters['trashed'] ?? false;
        if ($trashed) {
            $query->onlyTrashed();
        } elseif (!empty($filters['inactive'])) {
            $query->inactive();
        } elseif (!empty($filters['active']) || empty($filters['status'])) {
            $query->active();
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_fr', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) ($filters['per_page'] ?? 15), config('finance.per_page_max', 100));
        return $query->latest()->paginate($perPage);
    }

    public function bulkDelete(array $ids): bool
    {
        Budget::whereIn('id', $ids)
            ->each(fn($b) => $b->categories()->delete());

        return (bool) Budget::whereIn('id', $ids)
            ->delete();
    }

    public function bulkRestore(array $ids): bool
    {
        return (bool) Budget::withTrashed()
            ->whereIn('id', $ids)
            ->restore();
    }
}
