<?php

namespace Database\Factories;

use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    protected $model = Income::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'category_id' => IncomeCategory::factory(),
            'amount' => fake()->randomFloat(2, 100, 100000),
            'description' => fake()->sentence(),
            'date' => fake()->date(),
            'is_recurring' => false,
            'is_archived' => false,
        ];
    }

    public function recurring(): static
    {
        return $this->state(fn (array $attrs) => [
            'is_recurring' => true,
            'recurring_frequency' => fake()->randomElement(['monthly', 'yearly']),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attrs) => ['is_archived' => true]);
    }
}
