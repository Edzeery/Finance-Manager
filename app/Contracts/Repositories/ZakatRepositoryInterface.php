<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ZakatRepositoryInterface extends RepositoryInterface
{
    public function history(?int $userId = null, array $filters = []): LengthAwarePaginator;
    public function bulkDelete(array $ids, ?int $userId = null): bool;
    public function bulkRestore(array $ids, ?int $userId = null): bool;
}
