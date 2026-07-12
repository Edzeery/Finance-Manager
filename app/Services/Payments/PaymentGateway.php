<?php

namespace App\Services\Payments;

use App\Models\Payment;

interface PaymentGateway
{
    public function name(): string;

    public function charge(array $data): PaymentResult;

    public function validate(array $data): ValidationResult;

    public static function requiredFields(): array;

    public function refund(Payment $payment, ?float $amount = null): PaymentResult;

    public function verify(Payment $payment): PaymentResult;

    public function isOnline(): bool;

    public function isOffline(): bool;

    public function supportedCurrencies(): array;
}
