<?php

namespace App\Policies;

use App\Models\Debt;
use App\Models\User;

class DebtPolicy
{
    public function view(User $user, Debt $debt): bool
    {
        return $debt->user_id === $user->id || $user->hasPermission('debt.view');
    }

    public function create(User $user): bool
    {
        return $user->currentWorkspace && $user->currentWorkspace->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Debt $debt): bool
    {
        return $debt->user_id === $user->id || $user->hasPermission('debt.update');
    }

    public function delete(User $user, Debt $debt): bool
    {
        return $debt->user_id === $user->id || $user->hasPermission('debt.delete');
    }

    public function restore(User $user, Debt $debt): bool
    {
        return $debt->user_id === $user->id || $user->hasPermission('debt.restore');
    }

    public function addPayment(User $user, Debt $debt): bool
    {
        return $debt->user_id === $user->id || $user->hasPermission('debt.update');
    }

    public function forceDelete(User $user, Debt $debt): bool
    {
        return $debt->user_id === $user->id || $user->hasPermission('debt.force-delete');
    }
}
