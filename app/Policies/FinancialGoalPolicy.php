<?php

namespace App\Policies;

use App\Models\FinancialGoal;
use App\Models\User;

class FinancialGoalPolicy
{
    public function view(User $user, FinancialGoal $goal): bool
    {
        return $goal->user_id === $user->id || $user->hasPermission('goal.view');
    }

    public function create(User $user): bool
    {
        return $user->currentWorkspace && $user->currentWorkspace->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, FinancialGoal $goal): bool
    {
        return $goal->user_id === $user->id || $user->hasPermission('goal.update');
    }

    public function delete(User $user, FinancialGoal $goal): bool
    {
        return $goal->user_id === $user->id || $user->hasPermission('goal.delete');
    }

    public function restore(User $user, FinancialGoal $goal): bool
    {
        return $goal->user_id === $user->id || $user->hasPermission('goal.restore');
    }

    public function forceDelete(User $user, FinancialGoal $goal): bool
    {
        return $goal->user_id === $user->id || $user->hasPermission('goal.force-delete');
    }
}
