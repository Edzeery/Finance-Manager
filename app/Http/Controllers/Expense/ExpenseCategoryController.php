<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Category\CategoryController;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends CategoryController
{
    protected function getModelClass(): string
    {
        return ExpenseCategory::class;
    }

    protected function getValidationRules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_fr' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:7'],
            'type' => ['required', 'in:fixed,variable,periodic'],
            'is_active' => ['boolean'],
        ];
    }

    protected function getStoreView(): string
    {
        return 'expense.categories';
    }

    protected function getIndexRoute(): string
    {
        return 'expense.categories.index';
    }

    protected function getUpdateRoute(): string
    {
        return 'expense.categories.update';
    }

    protected function getDestroyRoute(): string
    {
        return 'expense.categories.destroy';
    }
}
