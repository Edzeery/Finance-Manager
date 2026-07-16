<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseCategoryFactory extends Factory
{
    protected $model = ExpenseCategory::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name_ar' => fake('ar_SA')->word(),
            'name_fr' => fake('fr_FR')->word(),
            'name_en' => fake()->word(),
            'icon' => 'bi-tag',
            'color' => fake()->hexColor(),
            'type' => fake()->randomElement(['fixed', 'variable']),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function global(): static
    {
        return $this->state(fn (array $attrs) => ['user_id' => null, 'workspace_id' => null]);
    }

    public function forWorkspace(mixed $workspaceId): static
    {
        return $this->state(fn (array $attrs) => ['workspace_id' => $workspaceId]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => ['is_active' => false]);
    }
}
