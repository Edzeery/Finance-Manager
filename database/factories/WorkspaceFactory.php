<?php

namespace Database\Factories;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkspaceFactory extends Factory
{
    protected $model = Workspace::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company()."'s Workspace",
            'slug' => fake()->unique()->slug().'-'.fake()->randomNumber(6),
            'type' => 'personal',
            'currency' => 'DZD',
            'timezone' => 'Africa/Algiers',
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => ['is_active' => false]);
    }
}
