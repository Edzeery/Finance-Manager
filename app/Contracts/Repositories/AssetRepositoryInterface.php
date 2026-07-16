<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AssetRepositoryInterface extends RepositoryInterface
{
    public function forUser(array $filters = []): LengthAwarePaginator;

    public function totalValue(): float;

    public function liquidValue(): float;

    public function zakatableValue(): float;

    public function bulkDelete(array $ids): bool;

    public function bulkRestore(array $ids): bool;
}
