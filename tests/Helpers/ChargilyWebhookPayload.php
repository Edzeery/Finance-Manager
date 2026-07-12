<?php

namespace Tests\Helpers;

trait ChargilyWebhookPayload
{
    private function chargilyPayload(array $override = []): string
    {
        $defaults = [
            'id' => 'evt_' . uniqid(),
            'type' => 'checkout.paid',
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
            'data' => [
                'id' => 'ch_' . uniqid(),
                'payment_link_id' => null,
                'customer_id' => null,
                'invoice_id' => null,
                'payment_method' => null,
                'currency' => 'DZD',
                'amount' => 1000,
                'status' => 'paid',
                'fees' => 25,
                'pass_fees_to_customer' => false,
                'description' => 'Test subscription',
                'success_url' => 'https://example.com/success',
                'failure_url' => 'https://example.com/failure',
                'webhook_endpoint' => 'https://example.com/webhook',
                'locale' => 'ar',
                'metadata' => ['payment_id' => null, 'user_id' => null, 'workspace_id' => null],
                'checkout_url' => 'https://chargily.com/checkout/test',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ],
        ];

        return json_encode(array_replace_recursive($defaults, $override));
    }
}
