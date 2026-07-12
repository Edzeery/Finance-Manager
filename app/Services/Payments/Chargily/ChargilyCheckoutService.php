<?php

namespace App\Services\Payments\Chargily;

use App\Services\Payments\Chargily\DTOs\CheckoutData;
use App\Services\Payments\Chargily\Exceptions\ChargilyException;

class ChargilyCheckoutService
{
    private ?\App\Models\PaymentMethod $_method = null;

    public function setMethod(?\App\Models\PaymentMethod $method): static
    {
        $this->_method = $method;
        return $this;
    }

    public function name(): string
    {
        return 'chargily';
    }
    public function create(array $data): CheckoutData
    {
        $client = $this->client();

        try {
            $checkout = $client->checkouts()->create([
                'amount' => (float) ($data['amount'] ?? 0),
                'currency' => strtolower($data['currency'] ?? 'dzd'),
                'locale' => in_array(app()->getLocale(), ['ar', 'fr', 'en']) ? app()->getLocale() : 'ar',
                'description' => $data['description'] ?? 'Finance Manager Subscription',
                'success_url' => $data['success_url'] ?? route('chargily.back'),
                'failure_url' => $data['failure_url'] ?? route('chargily.back'),
                'webhook_endpoint' => $data['webhook_url'] ?? (ChargilyClient::setting('webhook_url', config('payment.gateways.chargily.webhook_url')) ?? route('payment.webhook.chargily')),
                'metadata' => [
                    'payment_id' => $data['payment_id'] ?? null,
                    'user_id' => $data['user_id'] ?? null,
                    'workspace_id' => $data['workspace_id'] ?? null,
                ],
            ]);
        } catch (\Throwable $e) {
            throw ChargilyException::checkoutCreationFailed($e->getMessage());
        }

        if (!$checkout || !$checkout->getUrl()) {
            throw ChargilyException::checkoutCreationFailed('No checkout URL returned.');
        }

        return CheckoutData::fromChargilyResponse($checkout);
    }

    public function get(string $checkoutId): ?object
    {
        try {
            return $this->client()->checkouts()->get($checkoutId);
        } catch (\Throwable) {
            return null;
        }
    }

    public function getPaymentMethod(): ?\App\Models\PaymentMethod
    {
        return $this->_method;
    }

    private function client(): \Chargily\ChargilyPay\ChargilyPay
    {
        return ChargilyClient::make($this->_method);
    }
}
