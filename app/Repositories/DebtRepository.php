<?php

namespace App\Repositories;

use App\Contracts\Repositories\DebtRepositoryInterface;
use App\Enums\DebtStatus;
use App\Models\Debt;
use App\Repositories\Concerns\FiltersByOwnership;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DebtRepository extends BaseRepository implements DebtRepositoryInterface
{
    use FiltersByOwnership;

    public function __construct()
    {
        parent::__construct(new Debt);
    }

    public function forUser(array $filters = []): LengthAwarePaginator
    {
        $query = Debt::with('payments');
        $this->applyOwnershipFilter($query, 'debt.view');

        $trashed = $filters['trashed'] ?? false;
        if ($trashed) {
            $query->onlyTrashed();
        }

        if (! empty($filters['status'])) {
            if ($filters['status'] === DebtStatus::Overdue->value) {
                $query->whereDate('due_date', '<', now())->where('status', '!=', DebtStatus::Paid->value);
            } else {
                $query->where('status', $filters['status']);
            }
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('counterparty_name', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) ($filters['per_page'] ?? 15), config('finance.per_page_max', 100));

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function stats(): array
    {
        $debtStats = Debt::active()
            ->selectRaw('type, SUM(total_amount) as total_amount, SUM(paid_amount) as paid_amount')
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $owed = $debtStats['owed'] ?? null;
        $owing = $debtStats['owing'] ?? null;

        return [
            'totalOwed' => $owed ? (float) $owed->total_amount : 0,
            'paidOwed' => $owed ? (float) $owed->paid_amount : 0,
            'totalOwing' => $owing ? (float) $owing->total_amount : 0,
            'paidOwing' => $owing ? (float) $owing->paid_amount : 0,
        ];
    }

    public function overdueCount(): int
    {
        return Debt::overdue()->count();
    }

    public function bulkDelete(array $ids): bool
    {
        return (bool) Debt::whereIn('id', $ids)
            ->delete();
    }

    public function bulkRestore(array $ids): bool
    {
        return (bool) Debt::withTrashed()
            ->whereIn('id', $ids)
            ->restore();
    }
}
