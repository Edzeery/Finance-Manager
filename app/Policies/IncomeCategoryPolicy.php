<?php

namespace App\Policies;

use App\Models\IncomeCategory;
use App\Models\User;

class IncomeCategoryPolicy
{
    public function view(User $user, IncomeCategory $category): bool
    {
        return $category->user_id === null
            || $category->user_id === $user->id
            || $user->hasPermission('income-categories.view');
    }

    public function create(User $user): bool
    {
        return $user->currentWorkspace && $user->currentWorkspace->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, IncomeCategory $category): bool
    {
        return ($category->user_id !== null && $category->user_id === $user->id)
            || $user->hasPermission('income-categories.update');
    }

    public function delete(User $user, IncomeCategory $category): bool
    {
        return ($category->user_id !== null && $category->user_id === $user->id)
            || $user->hasPermission('income-categories.delete');
    }
}
