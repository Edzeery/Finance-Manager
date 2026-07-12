<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Services\Payments\Concerns\HasGatewaySettings;
use Illuminate\Support\Facades\Http;

class StripeGateway implements PaymentGateway
{
    use HasGatewaySettings;

    public function name(): string
    {
        return 'stripe';
    }

    private function secretKey(): ?string
    {
        return $this->gatewaySetting('secret_key', config('payment.gateways.stripe.secret_key'));
    }

    private function baseUrl(): string
    {
        return 'https://api.stripe.com/v1';
    }

    public function charge(array $data): PaymentResult
    {
        $secret = $this->secretKey();
        if (!$secret) {
            return PaymentResult::failed('Stripe gateway not configured.');
        }

        $response = Http::withToken($secret)
            ->asForm()
            ->post("{$this->baseUrl()}/payment_intents", [
                'amount' => (int) round($data['amount'] * 100),
                'currency' => strtolower($data['currency'] ?? 'usd'),
                'description' => $data['description'] ?? 'Finance Manager Subscription',
                'metadata' => [
                    'reference' => $data['reference'] ?? '',
                    'customer_id' => $data['customer_id'] ?? '',
                ],
                'confirm' => false,
            ]);

        if (!$response->successful()) {
            return PaymentResult::failed('Stripe charge failed: ' . $response->body());
        }

        $body = $response->json();

        return PaymentResult::success(
            message: 'Stripe payment intent created.',
            transactionId: $body['id'],
            metadata: $body,
            redirectUrl: $body['next_action']['redirect_to_url']['url'] ?? null,
        );
    }

    public function refund(Payment $payment, ?float $amount = null): PaymentResult
    {
        $secret = $this->secretKey();
        if (!$secret) {
            return PaymentResult::failed('Stripe gateway not configured.');
        }

        $response = Http::withToken($secret)
            ->asForm()
            ->post("{$this->baseUrl()}/refunds", [
                'payment_intent' => $payment->transaction_id,
                'amount' => $amount ? (int) round($amount * 100) : null,
            ]);

        if (!$response->successful()) {
            return PaymentResult::failed('Stripe refund failed: ' . $response->body());
        }

        return PaymentResult::success(
            message: 'Refund processed via Stripe.',
            transactionId: $response->json('id'),
            metadata: $response->json(),
        );
    }

    public function verify(Payment $payment): PaymentResult
    {
        $secret = $this->secretKey();
        if (!$secret) {
            return PaymentResult::failed('Stripe gateway not configured.');
        }

        $response = Http::withToken($secret)
            ->get("{$this->baseUrl()}/payment_intents/{$payment->transaction_id}");

        if (!$response->successful()) {
            return PaymentResult::failed('Unable to verify payment with Stripe.');
        }

        $body = $response->json();
        $status = $body['status'] ?? 'unknown';

        return PaymentResult::success(
            message: "Stripe verification: {$status}",
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
