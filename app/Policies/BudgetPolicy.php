<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;

class BudgetPolicy
{
    public function view(User $user, Budget $budget): bool
    {
        return $budget->user_id === $user->id || $user->hasPermission('budget.view');
    }

    public function create(User $user): bool
    {
        return $user->currentWorkspace && $user->currentWorkspace->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Budget $budget): bool
    {
        return $budget->user_id === $user->id || $user->hasPermission('budget.update');
    }

    public function delete(User $user, Budget $budget): bool
    {
        return $budget->user_id === $user->id || $user->hasPermission('budget.delete');
    }

    public function restore(User $user, Budget $budget): bool
    {
        return $budget->user_id === $user->id || $user->hasPermission('budget.restore');
    }

    public function forceDelete(User $user, Budget $budget): bool
    {
        return $budget->user_id === $user->id || $user->hasPermission('budget.force-delete');
    }
}
