<?php

namespace App\Http\Controllers\Income;

use App\Http\Controllers\Category\CategoryController;
use App\Models\IncomeCategory;

class IncomeCategoryController extends CategoryController
{
    protected function getModelClass(): string
    {
        return IncomeCategory::class;
    }

    protected function getValidationRules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_fr' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:7'],
            'type' => ['required', 'in:fixed,variable,recurring'],
            'is_active' => ['boolean'],
        ];
    }

    protected function getStoreView(): string
    {
        return 'income.categories';
    }

    protected function getIndexRoute(): string
    {
        return 'income.categories.index';
    }

    protected function getUpdateRoute(): string
    {
        return 'income.categories.update';
    }

    protected function getDestroyRoute(): string
    {
        return 'income.categories.destroy';
    }
}
