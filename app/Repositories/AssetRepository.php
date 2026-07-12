<?php

namespace App\Repositories;

use App\Contracts\Repositories\AssetRepositoryInterface;
use App\Models\Asset;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AssetRepository extends BaseRepository implements AssetRepositoryInterface
{
    use \App\Repositories\Concerns\FiltersByOwnership;

    public function __construct()
    {
        parent::__construct(new Asset);
    }

    public function forUser(array $filters = []): LengthAwarePaginator
    {
        $query = Asset::query();
        $this->applyOwnershipFilter($query, 'asset.view');

        $trashed = $filters['trashed'] ?? false;
        if ($trashed) {
            $query->onlyTrashed();
        } elseif (!empty($filters['liquid'])) {
            $query->liquid();
        } elseif (!empty($filters['zakatable'])) {
            $query->zakatable();
        }

        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('notes', 'like', $term);
            });
        }

        $perPage = min((int) ($filters['per_page'] ?? 15), config('finance.per_page_max', 100));
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function totalValue(): float
    {
        return (float) Asset::sum('total_value');
    }

    public function liquidValue(): float
    {
        return (float) Asset::liquid()->sum('total_value');
    }

    public function zakatableValue(): float
    {
        return (float) Asset::zakatable()->sum('total_value');
    }

    public function bulkDelete(array $ids): bool
    {
        return (bool) Asset::whereIn('id', $ids)
            ->delete();
    }

    public function bulkRestore(array $ids): bool
    {
        return (bool) Asset::withTrashed()
            ->whereIn('id', $ids)
            ->restore();
    }
}
