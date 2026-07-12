<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Services\Payments\Concerns\HasGatewaySettings;
use Illuminate\Support\Facades\Http;

class PayPalGateway implements PaymentGateway
{
    use HasGatewaySettings;

    private ?string $accessToken = null;

    public function name(): string
    {
        return 'paypal';
    }

    private function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $clientId = $this->gatewaySetting('client_id', config('payment.gateways.paypal.client_id'));
        $secret = $this->gatewaySetting('secret', config('payment.gateways.paypal.secret'));

        if (!$clientId || !$secret) {
            return null;
        }

        $isSandbox = $this->gatewaySetting('sandbox', config('payment.gateways.paypal.sandbox', true));
        $base = $isSandbox === true || $isSandbox === '1' || $isSandbox === 'true'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        $response = Http::withBasicAuth($clientId, $secret)
            ->asForm()
            ->post("{$base}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->successful()) {
            return null;
        }

        $this->accessToken = $response->json('access_token');

        return $this->accessToken;
    }

    private function baseUrl(): string
    {
        $isSandbox = $this->gatewaySetting('sandbox', config('payment.gateways.paypal.sandbox', true));
        return $isSandbox === true || $isSandbox === '1' || $isSandbox === 'true'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    public function charge(array $data): PaymentResult
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return PaymentResult::failed('PayPal gateway not configured.');
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl()}/v1/payments/payment", [
                'intent' => 'sale',
                'payer' => ['payment_method' => 'paypal'],
                'transactions' => [[
                    'amount' => [
                        'total' => (string) $data['amount'],
                        'currency' => $data['currency'] ?? 'USD',
                    ],
                    'description' => $data['description'] ?? 'Finance Manager Subscription',
                    'invoice_number' => $data['reference'] ?? null,
                ]],
                'redirect_urls' => [
                    'return_url' => $data['success_url'] ?? route('paypal.back'),
                    'cancel_url' => $data['failure_url'] ?? route('paypal.back'),
                ],
            ]);

        if (!$response->successful()) {
            return PaymentResult::failed('PayPal charge failed: ' . $response->body());
        }

        $body = $response->json();
        $approvalUrl = collect($body['links'] ?? [])
            ->firstWhere('rel', 'approval_url')['href'] ?? null;

        return PaymentResult::success(
            message: 'Redirecting to PayPal...',
            transactionId: $body['id'] ?? null,
            metadata: $body,
            redirectUrl: $approvalUrl,
        );
    }

    public function refund(Payment $payment, ?float $amount = null): PaymentResult
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return PaymentResult::failed('PayPal gateway not configured.');
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl()}/v1/payments/sale/{$payment->transaction_id}/refund", [
                'amount' => [
                    'total' => (string) ($amount ?? $payment->amount),
                    'currency' => $payment->currency,
                ],
            ]);

        if (!$response->successful()) {
            return PaymentResult::failed('PayPal refund failed: ' . $response->body());
        }

        return PaymentResult::success(
            message: 'Refund processed via PayPal.',
            transactionId: $response->json('id'),
            metadata: $response->json(),
        );
    }

    public function verify(Payment $payment): PaymentResult
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return PaymentResult::failed('PayPal gateway not configured.');
        }

        $response = Http::withToken($token)
            ->get("{$this->baseUrl()}/v1/payments/payment/{$payment->transaction_id}");

        if (!$response->successful()) {
            return PaymentResult::failed('Unable to verify payment with PayPal.');
        }

        $body = $response->json();
        $state = $body['state'] ?? 'unknown';

        return PaymentResult::success(
            message: "PayPal verification: {$state}",
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
        return ['USD', 'EUR', 'GBP', 'DZD'];
    }
}
