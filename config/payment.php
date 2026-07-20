<?php

return [
    'default' => env('PAYMENT_GATEWAY', 'cash'),

    'gateways' => [
        'chargily' => [
            'mode' => env('CHARGILY_MODE', 'test'), // 'test' أو 'live'
            'public_key' => env('CHARGILY_PUBLIC_KEY'),
            'secret_key' => env('CHARGILY_SECRET_KEY'),
            'webhook_url' => env('CHARGILY_WEBHOOK_URL'),
        ],
        'baridimob' => [
            'enabled' => env('BARIDIMOB_ENABLED', true),
        ],
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'sandbox' => env('PAYPAL_SANDBOX', true),
            'webhook_secret' => env('PAYPAL_WEBHOOK_SECRET'),
        ],
        'redotpay' => [
            'wallet_address' => env('REDOTPAY_WALLET_ADDRESS'),
            'enabled' => false,
        ],
        'stripe' => [
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'sandbox' => env('STRIPE_SANDBOX', true),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'enabled' => false,
        ],
        'wise' => [
            'api_key' => env('WISE_API_KEY'),
            'sandbox' => env('WISE_SANDBOX', true),
            'recipient_account_id' => env('WISE_RECIPIENT_ACCOUNT_ID'),
            'webhook_secret' => env('WISE_WEBHOOK_SECRET'),
            'enabled' => false,
        ],
        'payoneer' => [
            'client_id' => env('PAYONEER_CLIENT_ID'),
            'client_secret' => env('PAYONEER_CLIENT_SECRET'),
            'program_id' => env('PAYONEER_PROGRAM_ID'),
            'sandbox' => env('PAYONEER_SANDBOX', true),
            'webhook_secret' => env('PAYONEER_WEBHOOK_SECRET'),
            'enabled' => false,
        ],
        'cash' => [
            'enabled' => false,
        ],

        'noest' => [
            'base_url' => env('NOEST_BASE_URL', 'https://app.noest-dz.com/api/public'),
            'api_token' => env('NOEST_API_TOKEN'),
            'user_guid' => env('NOEST_USER_GUID'),
            'webhook_secret' => env('NOEST_WEBHOOK_SECRET'),
        ],
        'wise_manual' => [
            'account_email' => env('WISE_MANUAL_ACCOUNT_EMAIL'),
            'account_holder_name' => env('WISE_MANUAL_ACCOUNT_HOLDER'),
        ],
        'delivery' => [
            'enabled' => true,
        ],
    ],

    'webhook_secret' => env('WEBHOOK_SECRET'),

    'currencies' => ['DZD', 'USD', 'EUR', 'GBP', 'USDT', 'BTC', 'ETH'],

    'default_currency' => env('DEFAULT_CURRENCY', 'DZD'),
];
