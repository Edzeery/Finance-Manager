<?php

namespace App\Services\Payments;

use App\Models\Payment;

class CashGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'cash';
    }

    public function validate(array $data): ValidationResult
    {
        return ValidationResult::valid();
    }

    public static function requiredFields(): array
    {
        return [];
    }

    public function charge(array $data): PaymentResult
    {
        return PaymentResult::success(
            message: 'Cash payment recorded. Awaiting admin verification.',
            reference: $data['reference'] ?? null,
            metadata: [
                'notes' => $data['notes'] ?? null,
            ],
        );
    }

    public function refund(Payment $payment, ?float $amount = null): PaymentResult
    {
        return PaymentResult::failed('Cash refunds are processed manually.');
    }

    public function verify(Payment $payment): PaymentResult
    {
        return PaymentResult::failed('Cash payments are verified manually by admin.');
    }

    public function isOnline(): bool
    {
        return false;
    }

    public function isOffline(): bool
    {
        return true;
    }

    public function supportedCurrencies(): array
    {
        return ['DZD', 'USD', 'EUR'];
    }
}
