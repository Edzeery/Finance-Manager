<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Services\Payments\Concerns\HasGatewaySettings;
use App\Services\Payments\ValidationResult;

class BaridiMobGateway implements PaymentGateway
{
    use HasGatewaySettings;

    public function name(): string
    {
        return 'baridimob';
    }

    public function validate(array $data): ValidationResult
    {
        if (!$this->gatewaySetting('rip_number')) {
            return ValidationResult::invalid('BaridiMob is not configured. Please contact support.');
        }
        return ValidationResult::valid();
    }

    public static function requiredFields(): array
    {
        return [];
    }

    public function charge(array $data): PaymentResult
    {
        $ripNumber = $this->gatewaySetting('rip_number');
        $accountHolder = $this->gatewaySetting('account_holder_name');

        if (!$ripNumber) {
            return PaymentResult::failed('BaridiMob is not configured. Please contact support.');
        }

        return PaymentResult::success(
            message: 'BaridiMob payment initiated. Awaiting confirmation.',
            reference: $data['reference'] ?? null,
            metadata: [
                'rip_number' => $ripNumber,
                'account_holder_name' => $accountHolder,
                'amount' => $data['amount'] ?? null,
                'currency' => $data['currency'] ?? 'DZD',
                'instructions' => 'قم بتحويل المبلغ عبر تطبيق BaridiMob إلى رقم RIP أعلاه، ثم أرفق صورة الوصل ورقم العملية.',
            ],
        );
    }

    public function refund(Payment $payment, ?float $amount = null): PaymentResult
    {
        return PaymentResult::failed('BaridiMob refunds are processed via the bank.');
    }

    public function verify(Payment $payment): PaymentResult
    {
        return PaymentResult::failed('BaridiMob verification is done manually via bank statement.');
    }

    public function isOnline(): bool { return false; }
    public function isOffline(): bool { return true; }
    public function supportedCurrencies(): array { return ['DZD']; }
}
