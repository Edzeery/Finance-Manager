<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Services\Payments\Concerns\HasGatewaySettings;

class WiseManualGateway implements PaymentGateway
{
    use HasGatewaySettings;

    public function name(): string
    {
        return 'wise_manual';
    }

    public function charge(array $data): PaymentResult
    {
        $accountEmail = $this->gatewaySetting('account_email');
        $accountHolder = $this->gatewaySetting('account_holder_name');

        if (!$accountEmail) {
            return PaymentResult::failed('Wise (manual transfer) is not configured. Please contact support.');
        }

        return PaymentResult::success(
            message: 'Wise transfer initiated. Awaiting confirmation.',
            reference: $data['reference'] ?? null,
            metadata: [
                'account_email' => $accountEmail,
                'account_holder_name' => $accountHolder,
                'amount' => $data['amount'] ?? null,
                'currency' => $data['currency'] ?? 'USD',
                'instructions' => 'قم بإرسال التحويل عبر Wise إلى البريد الإلكتروني أعلاه، ثم أرفق صورة الإيصال ورقم العملية.',
            ],
        );
    }

    public function refund(Payment $payment, ?float $amount = null): PaymentResult
    {
        return PaymentResult::failed('Wise manual refunds are processed manually.');
    }

    public function verify(Payment $payment): PaymentResult
    {
        return PaymentResult::failed('Wise manual verification is done manually.');
    }

    public function isOnline(): bool { return false; }
    public function isOffline(): bool { return true; }
    public function supportedCurrencies(): array { return ['USD', 'EUR', 'GBP', 'DZD']; }
}
