<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'type' => fake()->randomElement(['cash', 'bank_account', 'gold', 'real_estate', 'stocks']),
            'name' => fake()->word(),
            'description' => fake()->optional()->sentence(),
            'quantity' => fake()->randomFloat(4, 1, 100),
            'unit_price' => fake()->randomFloat(2, 100, 100000),
            'total_value' => fake()->randomFloat(2, 1000, 10000000),
            'is_liquid' => fake()->boolean(),
        ];
    }

    public function liquid(): static
    {
        return $this->state(fn (array $attrs) => ['is_liquid' => true]);
    }
}
