<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            'chargily' => [
                'name' => 'Chargily',
                'category' => 'online',
                'description' => 'Chargily e-payment gateway for Algerian cards',
                'icon' => 'bi-credit-card-2-front',
                'sandbox' => true,
                'webhook' => true,
                'sort_order' => 1,
                'supported_currencies' => ['DZD'],
                'fields' => [
                    ['key' => 'mode', 'type' => 'select', 'label' => 'Mode', 'required' => true,
                     'options' => [['label' => 'Test', 'value' => 'test'], ['label' => 'Live', 'value' => 'live']],
                     'default' => 'test'],
                    ['key' => 'public_key', 'type' => 'password', 'label' => 'Public Key', 'required' => true,
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                    ['key' => 'secret_key', 'type' => 'password', 'label' => 'Secret Key', 'required' => true,
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                    ['key' => 'webhook_url', 'type' => 'url', 'label' => 'Webhook URL',
                     'placeholder' => 'https://example.com/payment/webhook/chargily', 'maxLength' => 255],
                ],
            ],
            'paypal' => [
                'name' => 'PayPal',
                'category' => 'online',
                'description' => 'PayPal international payments',
                'icon' => 'bi-paypal',
                'sandbox' => true,
                'webhook' => true,
                'sort_order' => 2,
                'supported_currencies' => ['USD', 'EUR', 'GBP'],
                'fields' => [
                    ['key' => 'client_id', 'type' => 'text', 'label' => 'Client ID', 'required' => true, 'maxLength' => 255],
                    ['key' => 'secret', 'type' => 'password', 'label' => 'Client Secret', 'required' => true,
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                    ['key' => 'sandbox', 'type' => 'boolean', 'label' => 'Sandbox Mode', 'default' => true],
                    ['key' => 'webhook_secret', 'type' => 'password', 'label' => 'Webhook Secret',
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                ],
            ],
            'stripe' => [
                'name' => 'Stripe',
                'category' => 'online',
                'description' => 'Stripe credit/debit card payments',
                'icon' => 'bi-stripe',
                'sandbox' => true,
                'webhook' => true,
                'sort_order' => 3,
                'supported_currencies' => ['USD', 'EUR', 'GBP', 'AED', 'SAR'],
                'fields' => [
                    ['key' => 'secret_key', 'type' => 'password', 'label' => 'Secret Key', 'required' => true,
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                    ['key' => 'sandbox', 'type' => 'boolean', 'label' => 'Sandbox Mode', 'default' => true],
                    ['key' => 'webhook_secret', 'type' => 'password', 'label' => 'Webhook Secret',
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                ],
            ],
            'wise' => [
                'name' => 'Wise',
                'category' => 'online',
                'description' => 'Wise online transfers',
                'icon' => 'bi-bank',
                'sandbox' => true,
                'webhook' => true,
                'sort_order' => 4,
                'supported_currencies' => ['USD', 'EUR', 'GBP'],
                'fields' => [
                    ['key' => 'api_key', 'type' => 'password', 'label' => 'API Key', 'required' => true,
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                    ['key' => 'recipient_account_id', 'type' => 'text', 'label' => 'Recipient Account ID', 'maxLength' => 255],
                    ['key' => 'sandbox', 'type' => 'boolean', 'label' => 'Sandbox Mode', 'default' => true],
                    ['key' => 'webhook_secret', 'type' => 'password', 'label' => 'Webhook Secret',
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                ],
            ],
            'payoneer' => [
                'name' => 'Payoneer',
                'category' => 'online',
                'description' => 'Payoneer international payments',
                'icon' => 'bi-currency-dollar',
                'sandbox' => true,
                'webhook' => true,
                'sort_order' => 5,
                'supported_currencies' => ['USD', 'EUR', 'GBP'],
                'fields' => [
                    ['key' => 'client_id', 'type' => 'text', 'label' => 'Client ID', 'required' => true, 'maxLength' => 255],
                    ['key' => 'client_secret', 'type' => 'password', 'label' => 'Client Secret', 'required' => true,
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                    ['key' => 'program_id', 'type' => 'text', 'label' => 'Program ID', 'maxLength' => 255],
                    ['key' => 'sandbox', 'type' => 'boolean', 'label' => 'Sandbox Mode', 'default' => true],
                    ['key' => 'webhook_secret', 'type' => 'password', 'label' => 'Webhook Secret',
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                ],
            ],
            'baridimob' => [
                'name' => 'BaridiMob',
                'category' => 'bank_transfer',
                'description' => 'Algerian postal payment BaridiMob',
                'icon' => 'bi-building-bank',
                'sandbox' => false,
                'webhook' => false,
                'sort_order' => 6,
                'supported_currencies' => ['DZD'],
                'fields' => [
                    ['key' => 'rip_number', 'type' => 'text', 'label' => 'RIP Number', 'required' => true, 'maxLength' => 50],
                    ['key' => 'account_holder_name', 'type' => 'text', 'label' => 'Account Holder Name', 'required' => true, 'maxLength' => 255],
                ],
            ],
            'redotpay' => [
                'name' => 'RedotPay',
                'category' => 'wallet',
                'description' => 'RedotPay prepaid card',
                'icon' => 'bi-wallet2',
                'sandbox' => false,
                'webhook' => false,
                'sort_order' => 7,
                'supported_currencies' => ['USDT', 'USD'],
                'fields' => [
                    ['key' => 'account_id', 'type' => 'text', 'label' => 'Account ID', 'required' => true, 'maxLength' => 255],
                    ['key' => 'account_holder_name', 'type' => 'text', 'label' => 'Account Holder Name', 'maxLength' => 255],
                ],
            ],
            'wise_manual' => [
                'name' => 'Wise Transfer',
                'category' => 'bank_transfer',
                'description' => 'Manual Wise transfer',
                'icon' => 'bi-bank',
                'sandbox' => false,
                'webhook' => false,
                'sort_order' => 8,
                'supported_currencies' => ['USD', 'EUR', 'GBP', 'DZD'],
                'fields' => [
                    ['key' => 'account_email', 'type' => 'email', 'label' => 'Account Email', 'required' => true, 'maxLength' => 255],
                    ['key' => 'account_holder_name', 'type' => 'text', 'label' => 'Account Holder Name', 'required' => true, 'maxLength' => 255],
                ],
            ],
            'noest' => [
                'name' => 'Noest',
                'category' => 'delivery',
                'description' => 'Noest delivery payment gateway',
                'icon' => 'bi-truck',
                'sandbox' => false,
                'webhook' => true,
                'sort_order' => 9,
                'supported_currencies' => ['DZD'],
                'fields' => [
                    ['key' => 'base_url', 'type' => 'url', 'label' => 'Base URL', 'required' => true,
                     'default' => 'https://app.noest-dz.com/api/public', 'maxLength' => 255],
                    ['key' => 'api_token', 'type' => 'password', 'label' => 'API Token', 'required' => true,
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 500],
                    ['key' => 'user_guid', 'type' => 'password', 'label' => 'User GUID', 'required' => true,
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                    ['key' => 'webhook_secret', 'type' => 'password', 'label' => 'Webhook Secret',
                     'encrypted' => true, 'sensitive' => true, 'maxLength' => 255],
                ],
            ],
            'cash' => [
                'name' => 'Cash',
                'category' => 'cash',
                'description' => 'Cash on delivery / in-person',
                'icon' => 'bi-cash-stack',
                'sandbox' => false,
                'webhook' => false,
                'sort_order' => 10,
                'supported_currencies' => ['DZD', 'USD', 'EUR'],
                'fields' => [
                    ['key' => 'enabled', 'type' => 'boolean', 'label' => 'Enabled', 'default' => true],
                ],
            ],
            'delivery' => [
                'name' => 'Delivery',
                'category' => 'delivery',
                'description' => 'Delivery service payment',
                'icon' => 'bi-box-seam',
                'sandbox' => false,
                'webhook' => false,
                'sort_order' => 12,
                'supported_currencies' => ['DZD', 'USD', 'EUR'],
                'fields' => [
                    ['key' => 'enabled', 'type' => 'boolean', 'label' => 'Enabled', 'default' => true],
                ],
            ],
        ];

        foreach ($gateways as $key => $data) {
            PaymentGateway::updateOrCreate(
                ['key' => $key],
                $data
            );
        }
    }
}
