<?php

namespace App\Policies;

use App\Models\Income;
use App\Models\User;

class IncomePolicy
{
    public function view(User $user, Income $income): bool
    {
        return $income->user_id === $user->id || $user->hasPermission('income.view');
    }

    public function create(User $user): bool
    {
        return $user->currentWorkspace && $user->currentWorkspace->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Income $income): bool
    {
        return $income->user_id === $user->id || $user->hasPermission('income.update');
    }

    public function delete(User $user, Income $income): bool
    {
        return $income->user_id === $user->id || $user->hasPermission('income.delete');
    }

    public function restore(User $user, Income $income): bool
    {
        return $income->user_id === $user->id || $user->hasPermission('income.restore');
    }

    public function archive(User $user, Income $income): bool
    {
        return $income->user_id === $user->id || $user->hasPermission('income.archive');
    }

    public function forceDelete(User $user, Income $income): bool
    {
        return $income->user_id === $user->id || $user->hasPermission('income.force-delete');
    }
}
