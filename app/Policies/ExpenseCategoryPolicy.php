<?php

namespace App\Policies;

use App\Models\ExpenseCategory;
use App\Models\User;

class ExpenseCategoryPolicy
{
    public function view(User $user, ExpenseCategory $category): bool
    {
        return $category->user_id === null
            || $category->user_id === $user->id
            || $user->hasPermission('expense-categories.view');
    }

    public function create(User $user): bool
    {
        return $user->currentWorkspace && $user->currentWorkspace->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, ExpenseCategory $category): bool
    {
        return ($category->user_id !== null && $category->user_id === $user->id)
            || $user->hasPermission('expense-categories.update');
    }

    public function delete(User $user, ExpenseCategory $category): bool
    {
        return ($category->user_id !== null && $category->user_id === $user->id)
            || $user->hasPermission('expense-categories.delete');
    }
}
