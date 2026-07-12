<?php

namespace App\Repositories;

use App\Contracts\Repositories\ZakatRepositoryInterface;
use App\Models\ZakatRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ZakatRepository extends BaseRepository implements ZakatRepositoryInterface
{
    use \App\Repositories\Concerns\FiltersByOwnership;

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
