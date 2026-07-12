<?php

namespace App\Repositories\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait FiltersByOwnership
{
    protected function applyOwnershipFilter(Builder $query, string $permissionSlug): void
    {
        $user = auth()->user();
        if ($user && !$user->hasPermission($permissionSlug)) {
            $query->where('user_id', $user->id);
        }
    }
}
