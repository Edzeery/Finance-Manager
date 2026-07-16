<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BudgetRepositoryInterface extends RepositoryInterface
{
    public function forUser(array $filters = []): LengthAwarePaginator;

    public function bulkDelete(array $ids): bool;

    public function bulkRestore(array $ids): bool;
}
