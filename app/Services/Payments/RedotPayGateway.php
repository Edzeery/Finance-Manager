<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Services\Payments\Concerns\HasGatewaySettings;

class RedotPayGateway implements PaymentGateway
{
    use HasGatewaySettings;

    public function name(): string
    {
        return 'redotpay';
    }

    public function charge(array $data): PaymentResult
    {
        $accountId = $this->gatewaySetting('account_id', config('payment.gateways.redotpay.wallet_address'));

        if (!$accountId) {
            return PaymentResult::failed('RedotPay is not configured. Please contact support.');
        }

        return PaymentResult::success(
            message: 'RedotPay payment initiated.',
            reference: $data['reference'] ?? null,
            metadata: [
                'account_id' => $accountId,
                'amount' => $data['amount'] ?? null,
                'currency' => $data['currency'] ?? 'DZD',
                'instructions' => 'قم بإرسال المبلغ عبر RedotPay إلى معرف الحساب أعلاه، ثم أرفق صورة لقطة الشاشة.',
            ],
        );
    }

    public function refund(Payment $payment, ?float $amount = null): PaymentResult
    {
        return PaymentResult::failed('RedotPay refunds are processed manually.');
    }

    public function verify(Payment $payment): PaymentResult
    {
        return PaymentResult::failed('RedotPay verification is done manually.');
    }

    public function isOnline(): bool { return false; }
    public function isOffline(): bool { return true; }
    public function supportedCurrencies(): array { return ['USDT', 'BTC', 'ETH', 'USD']; }
}
