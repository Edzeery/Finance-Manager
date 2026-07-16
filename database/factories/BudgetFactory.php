<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'name_ar' => fake('ar_SA')->word(),
            'name_fr' => fake('fr_FR')->word(),
            'name_en' => fake()->word(),
            'type' => fake()->randomElement(['monthly', 'yearly', 'custom']),
            'total_amount' => fake()->randomFloat(2, 10000, 500000),
            'start_date' => fake()->date(),
            'end_date' => fake()->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => ['is_active' => false]);
    }
}
