<?php

namespace App\Repositories;

use App\Contracts\Repositories\ZakatRepositoryInterface;
use App\Models\ZakatRecord;
use App\Repositories\Concerns\FiltersByOwnership;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ZakatRepository extends BaseRepository implements ZakatRepositoryInterface
{
    use FiltersByOwnership;

    public function __construct()
    {
        parent::__construct(new ZakatRecord);
    }

    public function history(?int $userId = null, array $filters = []): LengthAwarePaginator
    {
        $query = ZakatRecord::orderBy('calculation_date', 'desc');
        $this->applyOwnershipFilter($query, 'zakat.view');

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhere('zakat_amount', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('calculation_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('calculation_date', '<=', $filters['date_to']);
        }

        if (! empty($filters['exceeds_nisab']) && in_array($filters['exceeds_nisab'], ['yes', 'no'])) {
            $query->where('exceeds_nisab', $filters['exceeds_nisab'] === 'yes');
        }

        $perPage = min((int) ($filters['per_page'] ?? 15), config('finance.per_page_max', 100));

        return $query->paginate($perPage);
    }

    public function bulkDelete(array $ids, ?int $userId = null): bool
    {
        $query = ZakatRecord::whereIn('id', $ids);
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return (bool) $query->delete();
    }

    public function bulkRestore(array $ids, ?int $userId = null): bool
    {
        $query = ZakatRecord::withTrashed()->whereIn('id', $ids);
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return (bool) $query->restore();
    }
}
