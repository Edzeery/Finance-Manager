<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DebtRepositoryInterface extends RepositoryInterface
{
    public function forUser(array $filters = []): LengthAwarePaginator;
    public function stats(): array;
    public function overdueCount(): int;
    public function bulkDelete(array $ids): bool;
    public function bulkRestore(array $ids): bool;
}
