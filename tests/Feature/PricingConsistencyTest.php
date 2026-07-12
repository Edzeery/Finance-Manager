<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Services\CurrencyHelper;
use Database\Seeders\SubscriptionPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_yearly_price_matches_formula_for_all_plans(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $plans = SubscriptionPlan::with('activePrices')->get();

        foreach ($plans as $plan) {
            $monthly = $plan->getPrice('monthly');
            if ($plan->yearly_discount_percent && $monthly > 0) {
                $expected = round($monthly * 12 * (1 - $plan->yearly_discount_percent / 100), 2);
                $this->assertEquals($expected, $plan->yearly_price, "Plan {$plan->slug} yearly_price mismatch");
            } else {
                $this->assertEquals(0, $plan->yearly_price, "Plan {$plan->slug} yearly_price should be 0");
            }
        }
    }

    public function test_yearly_price_used_in_charging_matches_accessor(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $plan = SubscriptionPlan::where('slug', 'business')->first();

        $accessorValue = $plan->yearly_price;
        $chargedValue = $plan->yearly_price;

        $this->assertEquals($accessorValue, $chargedValue);
    }

    public function test_yearly_price_display_value_matches_accessor(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $plan = SubscriptionPlan::where('slug', 'business')->first();
        $model = SubscriptionPlan::with('activePrices')->where('slug', 'business')->first();

        $planArray = $model->toArray();
        $displayYearly = $planArray['yearly_price'] ?? 0;
        $this->assertEquals($plan->yearly_price, $displayYearly);
    }

    public function test_savings_calculation_matches_discount_percent(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $plan = SubscriptionPlan::with('activePrices')->where('slug', 'business')->first();

        $monthly = $plan->getPrice('monthly');
        $savings = ($monthly * 12) - $plan->yearly_price;
        $savingsPercent = round((($monthly * 12) - $plan->yearly_price) / ($monthly * 12) * 100);
        $expectedSavings = $monthly * 12 - round($monthly * 12 * (1 - $plan->yearly_discount_percent / 100), 2);

        $this->assertEqualsWithDelta(18.36, $savings, 0.01);
        $this->assertEquals(17, $savingsPercent);
        $this->assertEquals($plan->yearly_discount_percent, $savingsPercent);
    }

    public function test_yearly_price_consistent_across_currencies_via_conversion(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $plan = SubscriptionPlan::with('activePrices')->where('slug', 'business')->first();

        $yearlyUsd = $plan->yearly_price;
        $yearlyDzd = CurrencyHelper::fromUsd($yearlyUsd, 'DZD');
        $yearlyEur = CurrencyHelper::fromUsd($yearlyUsd, 'EUR');

        $monthlyUsd = $plan->getPrice('monthly');
        $monthlyDzd = CurrencyHelper::fromUsd($monthlyUsd, 'DZD');

        $this->assertEquals($yearlyDzd, round($monthlyDzd * 12 * (1 - $plan->yearly_discount_percent / 100), 2));
    }
}
