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

        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'calculation_date' => fake()->date(),
            'hijri_year' => now()->format('Y'),
            'nisab_gold' => 600000,
            'nisab_silver' => 50000,
            'cash_value' => fake()->randomFloat(2, 0, 500000),
            'bank_value' => fake()->randomFloat(2, 0, 1000000),
            'total_wealth' => $total,
            'total_zakatable' => $zakatable,
            'exceeds_nisab' => $zakatable > 600000,
            'zakat_amount' => $zakatable > 600000 ? round($zakatable * 0.025, 2) : 0,
        ];
    }
}
