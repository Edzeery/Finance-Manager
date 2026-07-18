<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workspace;
use App\Models\ZakatRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class ZakatRecordFactory extends Factory
{
    protected $model = ZakatRecord::class;

    public function definition(): array
    {
        $total = fake()->randomFloat(2, 100000, 5000000);
        $zakatable = fake()->randomFloat(2, 0, $total);
        $debts = fake()->randomFloat(2, 0, $zakatable * 0.3);
        $net = max($zakatable - $debts, 0);
        $exceeds = $net >= 600000;

        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'calculation_date' => fake()->date(),
            'hijri_year' => now()->format('Y'),
            'nisab_gold' => 600000,
            'nisab_silver' => 50000,
            'gold_price_per_gram' => fake()->randomFloat(2, 2000, 4000),
            'silver_price_per_gram' => fake()->randomFloat(2, 20, 80),
            'gold_weight' => fake()->randomFloat(4, 0, 100),
            'silver_weight' => fake()->randomFloat(4, 0, 500),
            'cash_value' => fake()->randomFloat(2, 0, 500000),
            'bank_value' => fake()->randomFloat(2, 0, 1000000),
            'total_wealth' => $total,
            'total_zakatable' => $zakatable,
            'total_debts' => $debts,
            'net_zakatable' => $net,
            'exceeds_nisab' => $exceeds,
            'zakat_amount' => $exceeds ? round($net * 0.025, 2) : 0,
            'cash_zakat' => fake()->randomFloat(2, 0, 5000),
            'gold_zakat' => fake()->randomFloat(2, 0, 5000),
            'silver_zakat' => fake()->randomFloat(2, 0, 1000),
            'business_zakat' => fake()->randomFloat(2, 0, 3000),
            'investments_zakat' => fake()->randomFloat(2, 0, 2000),
        ];
    }
}
