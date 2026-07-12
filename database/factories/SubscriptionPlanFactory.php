<?php

namespace Database\Factories;

use App\Models\PlanFeature;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    public function definition(): array
    {
        $name = fake()->word();
        return [
            'name' => $name,
            'slug' => fake()->unique()->slug(),
            'yearly_discount_percent' => 17,
            'is_active' => true,
            'is_public' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (SubscriptionPlan $plan) {
            $coreSlugs = ['income_expense', 'users', 'workspaces', 'transactions_per_month'];
            foreach ($coreSlugs as $slug) {
                $feature = PlanFeature::firstOrCreate(
                    ['slug' => $slug],
                    ['name_en' => $slug, 'name_ar' => $slug, 'name_fr' => $slug, 'type' => 'value', 'is_core' => false]
                );
                $value = match ($slug) {
                    'users' => '10',
                    'workspaces' => '5',
                    'transactions_per_month' => '1000',
                    default => '1',
                };
                if (!$plan->planFeatures()->where('plan_feature_id', $feature->id)->exists()) {
                    $plan->planFeatures()->attach($feature->id, ['value' => $value, 'sort_order' => 0]);
                }
            }
        });
    }

    public function free(): static
    {
        return $this->state(fn(array $attrs) => [
            'is_free' => true,
            'yearly_discount_percent' => null,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn(array $attrs) => [
            'is_free' => false,
        ]);
    }

    public function enterprise(): static
    {
        return $this->state(fn(array $attrs) => [
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'is_free' => false,
            'is_public' => false,
            'yearly_discount_percent' => null,
        ]);
    }
}
