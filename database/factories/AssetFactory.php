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
        $type = fake()->randomElement(['cash', 'bank_account', 'gold', 'silver', 'real_estate', 'stocks']);
        $isGold = $type === 'gold';
        $isMetal = in_array($type, ['gold', 'silver']);
        $karat = $isGold ? fake()->randomElement([24, 22, 21, 18, 14, 10]) : null;
        $weightGrams = $isMetal ? fake()->randomFloat(4, 1, 500) : null;

        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'type' => $type,
            'karat' => $karat,
            'weight_grams' => $weightGrams,
            'name' => fake()->word(),
            'description' => fake()->optional()->sentence(),
            'quantity' => $isMetal ? null : fake()->randomFloat(4, 1, 100),
            'unit_price' => $isMetal ? null : fake()->randomFloat(2, 100, 100000),
            'total_value' => fake()->randomFloat(2, 1000, 10000000),
            'is_liquid' => fake()->boolean(),
        ];
    }

    public function gold(): static
    {
        return $this->state(fn (array $attrs) => [
            'type' => 'gold',
            'karat' => fake()->randomElement([24, 22, 21, 18, 14, 10]),
            'weight_grams' => fake()->randomFloat(4, 1, 500),
        ]);
    }

    public function silver(): static
    {
        return $this->state(fn (array $attrs) => [
            'type' => 'silver',
            'weight_grams' => fake()->randomFloat(4, 1, 500),
        ]);
    }

    public function liquid(): static
    {
        return $this->state(fn (array $attrs) => ['is_liquid' => true]);
    }
}
