<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'type' => fake()->randomElement(['budget_exceeded', 'debt_reminder', 'goal_achieved', 'zakat_reminder']),
            'title_ar' => fake('ar_SA')->sentence(),
            'title_fr' => fake('fr_FR')->sentence(),
            'title_en' => fake()->sentence(),
            'message_ar' => fake('ar_SA')->paragraph(),
            'message_fr' => fake('fr_FR')->paragraph(),
            'message_en' => fake()->paragraph(),
            'is_read' => false,
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attrs) => [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
