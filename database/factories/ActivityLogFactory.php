<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'action' => fake()->randomElement(['created', 'updated', 'deleted']),
            'subject_type' => fake()->randomElement([
                'App\Models\Income',
                'App\Models\Expense',
                'App\Models\Debt',
            ]),
            'subject_id' => fake()->numberBetween(1, 1000),
            'description' => fake()->sentence(),
            'properties' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
