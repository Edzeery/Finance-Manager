<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetCategoryFactory extends Factory
{
    protected $model = BudgetCategory::class;

    public function definition(): array
    {
        $allocated = fake()->randomFloat(2, 1000, 100000);
        return [
            'budget_id' => Budget::factory(),
            'expense_category_id' => ExpenseCategory::factory(),
            'allocated_amount' => $allocated,
            'spent_amount' => fake()->randomFloat(2, 0, $allocated),
        ];
    }
}
