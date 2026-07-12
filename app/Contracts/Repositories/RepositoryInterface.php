<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function all(array $columns = ['*']): Collection;
    public function find(int $id): ?Model;
    public function findOrFail(int $id): Model;
    public function create(array $data): Model;
    public function update(Model $model, array $data): bool;
    public function delete(Model $model): bool;
    public function restore(Model $model): bool;
    public function withTrashedFindOrFail(int $id): Model;
    public function paginate(int $perPage = 15, array $columns = ['*']);
}
