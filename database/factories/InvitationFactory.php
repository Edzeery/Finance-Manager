<?php

namespace Database\Factories;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'inviter_id' => User::factory(),
            'email' => fake()->safeEmail(),
            'role' => 'workspace_viewer',
            'token' => Invitation::generateToken(),
            'status' => InvitationStatus::Pending,
            'expires_at' => Invitation::defaultExpiry(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => InvitationStatus::Pending,
            'accepted_at' => null,
            'declined_at' => null,
            'cancelled_at' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => InvitationStatus::Expired,
            'expires_at' => now()->subDay(),
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status' => InvitationStatus::Accepted,
            'accepted_at' => now(),
        ]);
    }

    public function declined(): static
    {
        return $this->state(fn () => [
            'status' => InvitationStatus::Declined,
            'declined_at' => now(),
        ]);
    }
}
