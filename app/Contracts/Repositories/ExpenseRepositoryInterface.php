<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ExpenseRepositoryInterface extends RepositoryInterface
{
    public function forUser(array $filters = []): LengthAwarePaginator;

    public function monthlyTotal(int $year, int $month): float;

    public function monthlyTotals(string $start, string $end): Collection;

    public function bulkDelete(array $ids): bool;

    public function bulkRestore(array $ids): bool;
}
