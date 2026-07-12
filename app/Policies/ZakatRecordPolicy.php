<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ZakatRecord;

class ZakatRecordPolicy
{
    public function view(User $user, ZakatRecord $record): bool
    {
        return $record->user_id === $user->id || $user->hasPermission('zakat.view');
    }

    public function create(User $user): bool
    {
        return $user->currentWorkspace && $user->currentWorkspace->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, ZakatRecord $record): bool
    {
        return $record->user_id === $user->id || $user->hasPermission('zakat.update');
    }

    public function delete(User $user, ZakatRecord $record): bool
    {
        return $record->user_id === $user->id || $user->hasPermission('zakat.delete');
    }

    public function restore(User $user, ZakatRecord $record): bool
    {
        return $record->user_id === $user->id || $user->hasPermission('zakat.restore');
    }

    public function forceDelete(User $user, ZakatRecord $record): bool
    {
        return $user->hasRole('super_admin');
    }
}
