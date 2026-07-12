<?php

namespace App\Repositories;

use App\Contracts\Repositories\GoalRepositoryInterface;
use App\Enums\GoalStatus;
use App\Models\FinancialGoal;
use App\Support\DatabaseHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GoalRepository extends BaseRepository implements GoalRepositoryInterface
{
    use \App\Repositories\Concerns\FiltersByOwnership;

    public function __construct()
    {
        parent::__construct(new FinancialGoal);
    }

    public function forUser(array $filters = []): LengthAwarePaginator
    {
        $query = FinancialGoal::query();
        $this->applyOwnershipFilter($query, 'goal.view');

        $trashed = $filters['trashed'] ?? false;
        if ($trashed) {
            $query->onlyTrashed();
        } elseif (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
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

        $orderExpr = DatabaseHelper::goalStatusOrderExpression();

        $perPage = min((int) ($filters['per_page'] ?? 15), config('finance.per_page_max', 100));
        return $query->orderByRaw("{$orderExpr}, target_date ASC")->paginate($perPage);
    }

    public function bulkDelete(array $ids): bool
    {
        return (bool) FinancialGoal::whereIn('id', $ids)
            ->delete();
    }

    public function bulkRestore(array $ids): bool
    {
        return (bool) FinancialGoal::withTrashed()
            ->whereIn('id', $ids)
            ->where('status', '!=', GoalStatus::Completed->value)
            ->restore();
    }
}
