<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Services\Payments\Concerns\HasGatewaySettings;
use Illuminate\Support\Facades\Http;

class PayoneerGateway implements PaymentGateway
{
    use HasGatewaySettings;

    public function name(): string
    {
        return 'payoneer';
    }

    private function clientId(): ?string
    {
        return $this->gatewaySetting('client_id', config('payment.gateways.payoneer.client_id'));
    }

    private function clientSecret(): ?string
    {
        return $this->gatewaySetting('client_secret', config('payment.gateways.payoneer.client_secret'));
    }

    private function baseUrl(): string
    {
        $isSandbox = $this->gatewaySetting('sandbox', config('payment.gateways.payoneer.sandbox', true));
        return $isSandbox === true || $isSandbox === '1' || $isSandbox === 'true'
            ? 'https://api.sandbox.payoneer.com/v2'
            : 'https://api.payoneer.com/v2';
    }

    private function getAccessToken(): ?string
    {
        $clientId = $this->clientId();
        $secret = $this->clientSecret();

        if (!$clientId || !$secret) {
            return null;
        }

        $response = Http::asForm()
            ->post("{$this->baseUrl()}/oauth/token", [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $secret,
            ]);

        if (!$response->successful()) {
            return null;
        }

        return $response->json('access_token');
    }

    public function charge(array $data): PaymentResult
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return PaymentResult::failed('Payoneer gateway not configured.');
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl()}/programs/" . $this->gatewaySetting('program_id', config('payment.gateways.payoneer.program_id')) . "/payouts", [
                'client_reference' => $data['reference'] ?? uniqid('pay_', true),
                'amount' => [
                    'value' => (string) $data['amount'],
                    'currency' => $data['currency'] ?? 'USD',
                ],
                'recipient' => [
                    'email' => $data['recipient_email'] ?? '',
                    'external_id' => $data['customer_id'] ?? '',
                ],
                'description' => $data['description'] ?? 'Finance Manager Subscription',
            ]);

        if (!$response->successful()) {
            return PaymentResult::failed('Payoneer payout failed: ' . $response->body());
        }

        $body = $response->json();

        return PaymentResult::success(
            message: 'Payoneer payout initiated.',
            transactionId: $body['id'] ?? null,
            metadata: $body,
        );
    }

    public function refund(Payment $payment, ?float $amount = null): PaymentResult
    {
        return PaymentResult::failed('Payoneer does not support direct refunds. Please process manually.');
    }

    public function verify(Payment $payment): PaymentResult
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return PaymentResult::failed('Payoneer gateway not configured.');
        }

        $response = Http::withToken($token)
            ->get("{$this->baseUrl()}/programs/" . $this->gatewaySetting('program_id', config('payment.gateways.payoneer.program_id')) . "/payouts/{$payment->transaction_id}");

        if (!$response->successful()) {
            return PaymentResult::failed('Unable to verify payment with Payoneer.');
        }

        $body = $response->json();
        $status = $body['status'] ?? 'unknown';

        return PaymentResult::success(
            message: "Payoneer verification: {$status}",
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
        return ['USD', 'EUR', 'GBP'];
    }
}
