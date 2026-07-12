<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function view(User $user, Expense $expense): bool
    {
        return $expense->user_id === $user->id || $user->hasPermission('expense.view');
    }

    public function create(User $user): bool
    {
        return $user->currentWorkspace && $user->currentWorkspace->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Expense $expense): bool
    {
        return $expense->user_id === $user->id || $user->hasPermission('expense.update');
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $expense->user_id === $user->id || $user->hasPermission('expense.delete');
    }

    public function restore(User $user, Expense $expense): bool
    {
        return $expense->user_id === $user->id || $user->hasPermission('expense.restore');
    }

    public function archive(User $user, Expense $expense): bool
    {
        return $expense->user_id === $user->id || $user->hasPermission('expense.archive');
    }

    public function forceDelete(User $user, Expense $expense): bool
    {
        return $expense->user_id === $user->id || $user->hasPermission('expense.force-delete');
    }
}
