<?php

namespace App\Contracts\Models;

interface Searchable
{
    public function getSearchResult(int $userId, string $query, int $limit): iterable;
}
