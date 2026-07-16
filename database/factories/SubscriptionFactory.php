<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'subscription_plan_id' => SubscriptionPlan::factory(),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['active', 'past_due', 'expired', 'canceled']),
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'auto_renew' => true,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'active']);
    }

    public function trialing(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'trialing',
            'trial_ends_at' => now()->addDays(30),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'expired',
            'ends_at' => now()->subDay(),
        ]);
    }

    public function onGrace(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'expired',
            'ends_at' => now()->subDay(),
            'grace_ends_at' => now()->addDays(2),
        ]);
    }

    public function expiredTrial(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'trialing',
            'trial_ends_at' => now()->subDay(),
        ]);
    }

    public function withPlan(?SubscriptionPlan $plan = null): static
    {
        return $this->state(fn (array $attrs) => [
            'subscription_plan_id' => $plan?->id ?? SubscriptionPlan::factory(),
        ]);
    }
}
