<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class DebtFactory extends Factory
{
    protected $model = Debt::class;

    public function definition(): array
    {
        $total = fake()->randomFloat(2, 1000, 500000);
        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'type' => fake()->randomElement(['owed', 'owing']),
            'counterparty_name' => fake()->name(),
            'total_amount' => $total,
            'paid_amount' => fake()->randomFloat(2, 0, $total),
            'due_date' => fake()->date(),
            'status' => fake()->randomElement(['active', 'partial', 'paid', 'overdue']),
            'description' => fake()->optional()->sentence(),
        ];
    }

    public function owed(): static
    {
        return $this->state(fn(array $attrs) => ['type' => 'owed']);
    }

    public function owing(): static
    {
        return $this->state(fn(array $attrs) => ['type' => 'owing']);
    }

    public function overdue(): static
    {
        return $this->state(fn(array $attrs) => [
            'status' => 'overdue',
            'due_date' => now()->subDays(fake()->numberBetween(1, 90)),
        ]);
    }
}
