<?php

namespace App\Contracts\Services;

use Illuminate\Support\Collection;

interface SearchServiceInterface
{
    public function search(string $query, ?int $workspaceId = null, ?int $userId = null): Collection;
}
