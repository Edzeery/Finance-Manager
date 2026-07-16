<?php

namespace Database\Factories;

use App\Models\FinancialGoal;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialGoalFactory extends Factory
{
    protected $model = FinancialGoal::class;

    public function definition(): array
    {
        $target = fake()->randomFloat(2, 5000, 1000000);

        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'name_ar' => fake('ar_SA')->word(),
            'name_fr' => fake('fr_FR')->word(),
            'name_en' => fake()->word(),
            'target_amount' => $target,
            'current_amount' => fake()->randomFloat(2, 0, $target),
            'target_date' => fake()->dateTimeBetween('+1 month', '+5 years')->format('Y-m-d'),
            'status' => fake()->randomElement(['in_progress', 'completed', 'cancelled']),
            'icon' => fake()->randomElement(['bi-flag', 'bi-house', 'bi-car', 'bi-plane']),
            'color' => fake()->hexColor(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'completed',
            'current_amount' => fn (array $attrs) => $attrs['target_amount'] ?? 0,
            'completed_at' => now(),
        ]);
    }
}
