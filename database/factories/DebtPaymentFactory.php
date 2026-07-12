<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class DebtPaymentFactory extends Factory
{
    protected $model = DebtPayment::class;

    public function definition(): array
    {
        return [
            'debt_id' => Debt::factory(),
            'amount' => fake()->randomFloat(2, 100, 50000),
            'payment_date' => fake()->date(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
