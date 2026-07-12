<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function view(User $user, Asset $asset): bool
    {
        return $asset->user_id === $user->id || $user->hasPermission('asset.view');
    }

    public function create(User $user): bool
    {
        return $user->currentWorkspace && $user->currentWorkspace->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Asset $asset): bool
    {
        return $asset->user_id === $user->id || $user->hasPermission('asset.update');
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $asset->user_id === $user->id || $user->hasPermission('asset.delete');
    }

    public function restore(User $user, Asset $asset): bool
    {
        return $asset->user_id === $user->id || $user->hasPermission('asset.restore');
    }

    public function forceDelete(User $user, Asset $asset): bool
    {
        return $asset->user_id === $user->id || $user->hasPermission('asset.force-delete');
    }
}
