<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'uuid' => 'pay-'.strtolower(fake()->bothify('????????????')),
            'workspace_id' => Workspace::factory(),
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 100, 100000),
            'currency' => 'USD',
            'status' => fake()->randomElement([PaymentStatus::CheckoutPending, PaymentStatus::CheckoutPaid, PaymentStatus::CheckoutFailed]),
            'original_amount' => fake()->randomFloat(2, 100, 100000),
            'discount_amount' => 0,
            'reference' => 'PAY-'.strtoupper(fake()->bothify('??####')),
        ];
    }

    public function forMethod(string $key): static
    {
        return $this->state(fn () => [
            'method_id' => PaymentMethod::withoutGlobalScopes()
                ->where('key', $key)->first()?->id,
        ]);
    }
}
