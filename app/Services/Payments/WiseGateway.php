<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Services\Payments\Concerns\HasGatewaySettings;
use App\Services\Payments\ValidationResult;
use Illuminate\Support\Facades\Http;

class WiseGateway implements PaymentGateway
{
    use HasGatewaySettings;

    public function name(): string
    {
        return 'wise';
    }

    private function apiKey(): ?string
    {
        return $this->gatewaySetting('api_key', config('payment.gateways.wise.api_key'));
    }

    private function baseUrl(): string
    {
        $isSandbox = $this->gatewaySetting('sandbox', config('payment.gateways.wise.sandbox', true));
        return $isSandbox === true || $isSandbox === '1' || $isSandbox === 'true'
            ? 'https://api.sandbox.transferwise.tech'
            : 'https://api.transferwise.com';
    }

    public function validate(array $data): ValidationResult
    {
        if (!$this->apiKey()) {
            return ValidationResult::invalid('Wise gateway not configured.');
        }
        return ValidationResult::valid();
    }

    public static function requiredFields(): array
    {
        return [];
    }

    public function charge(array $data): PaymentResult
    {
        $key = $this->apiKey();
        if (!$key) {
            return PaymentResult::failed('Wise gateway not configured.');
        }

        $response = Http::withToken($key)
            ->post("{$this->baseUrl()}/v1/transfers", [
                'targetAccount' => $data['target_account'] ?? $this->gatewaySetting('recipient_account_id', config('payment.gateways.wise.recipient_account_id')),
                'quote' => [
                    'source' => $data['currency'] ?? 'USD',
                    'target' => $data['target_currency'] ?? 'DZD',
                    'amount' => $data['amount'],
                ],
                'customerTransactionId' => $data['reference'] ?? uniqid('wise_', true),
                'details' => [
                    'reference' => $data['description'] ?? 'Finance Manager Subscription',
                ],
            ]);

        if (!$response->successful()) {
            return PaymentResult::failed('Wise transfer failed: ' . $response->body());
        }

        $body = $response->json();

        return PaymentResult::success(
            message: 'Wise transfer initiated.',
            transactionId: $body['id'] ?? null,
            reference: $body['customerTransactionId'] ?? null,
            metadata: $body,
        );
    }

    public function refund(Payment $payment, ?float $amount = null): PaymentResult
    {
        $key = $this->apiKey();
        if (!$key) {
            return PaymentResult::failed('Wise gateway not configured.');
        }

        $response = Http::withToken($key)
            ->post("{$this->baseUrl()}/v1/transfers/{$payment->transaction_id}/cancel");

        if (!$response->successful()) {
            return PaymentResult::failed('Wise refund/cancel failed: ' . $response->body());
        }

        return PaymentResult::success(
            message: 'Transfer canceled/refunded via Wise.',
            transactionId: $payment->transaction_id,
            metadata: $response->json(),
        );
    }

    public function verify(Payment $payment): PaymentResult
    {
        $key = $this->apiKey();
        if (!$key) {
            return PaymentResult::failed('Wise gateway not configured.');
        }

        $response = Http::withToken($key)
            ->get("{$this->baseUrl()}/v1/transfers/{$payment->transaction_id}");

        if (!$response->successful()) {
            return PaymentResult::failed('Unable to verify payment with Wise.');
        }

        $body = $response->json();
        $status = $body['status'] ?? 'unknown';

        return PaymentResult::success(
            message: "Wise verification: {$status}",
            transactionId: $payment->transaction_id,
            metadata: $body,
        );
    }

    public function isOnline(): bool
    {
        return true;
    }

    public function isOffline(): bool
    {
        return false;
    }

    public function supportedCurrencies(): array
    {
        return ['USD', 'EUR', 'GBP', 'DZD', 'AED', 'SAR'];
    }
}
