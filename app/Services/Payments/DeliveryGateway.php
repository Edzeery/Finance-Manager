<?php

namespace App\Services\Payments;

use App\Models\Payment;

class DeliveryGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'delivery';
    }

    public function validate(array $data): ValidationResult
    {
        if (empty($data['address'])) {
            return ValidationResult::invalid('Delivery address is required.');
        }

        return ValidationResult::valid();
    }

    public static function requiredFields(): array
    {
        return ['deliveryAddress', 'deliveryPhone'];
    }

    public function charge(array $data): PaymentResult
    {
        $address = $data['address'] ?? null;

        if (! $address) {
            return PaymentResult::failed('Delivery address is required.');
        }

        return PaymentResult::success(
            message: 'Delivery payment scheduled.',
            reference: $data['reference'] ?? null,
            metadata: [
                'address' => $address,
                'notes' => $data['notes'] ?? null,
            ],
        );
    }

    public function refund(Payment $payment, ?float $amount = null): PaymentResult
    {
        return PaymentResult::failed('Delivery refunds are processed manually.');
    }

    public function verify(Payment $payment): PaymentResult
    {
        return PaymentResult::failed('Delivery payments are verified upon collection.');
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
        return ['DZD'];
    }
}
