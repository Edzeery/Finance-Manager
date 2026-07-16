<?php

return [
    'currency' => env('FINANCE_CURRENCY', 'DZD'),
    'currency_symbol' => env('FINANCE_CURRENCY_SYMBOL', 'د.ج'),
    'locale' => env('FINANCE_LOCALE', 'ar'),
    'date_format' => env('FINANCE_DATE_FORMAT', 'Y/m/d'),
    'decimal_places' => (int) env('FINANCE_DECIMAL_PLACES', 2),
    'thousands_separator' => env('FINANCE_THOUSANDS_SEPARATOR', ','),
    'decimal_separator' => env('FINANCE_DECIMAL_SEPARATOR', '.'),

    'currencies' => [
        'DZD' => ['symbol' => 'د.ج', 'name' => 'Dinar Algérien', 'decimal_places' => 2],
        'USD' => ['symbol' => '$', 'name' => 'US Dollar', 'decimal_places' => 2],
        'EUR' => ['symbol' => '€', 'name' => 'Euro', 'decimal_places' => 2],
    ],

    'base_currency' => 'DZD',
    'exchange_rates' => [
        'USD' => 1.0,
        'DZD' => 250.0,   // 1 USD = 250 DZD
        'EUR' => 0.877,    // 1 USD ≈ 0.877 EUR
        'GBP' => 0.75,     // 1 USD ≈ 0.75 GBP
        'USDT' => 1.0,     // 1 USD = 1 USDT
    ],

    'per_page_max' => 100,
    'per_page_default' => 15,

    'expiry_reminder_days' => (int) env('EXPIRY_REMINDER_DAYS', 3),
    'grace_period_days' => (int) env('GRACE_PERIOD_DAYS', 3),
    'trial_days' => (int) env('TRIAL_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Rate Limits
    |--------------------------------------------------------------------------
    | These limits can be overridden from the super-admin settings panel.
    | Values are requests per minute unless otherwise noted.
    */
    'rate_limits' => [
        // Named rate limiters (used in RateLimiter facade)
        'api' => (int) env('RATE_LIMIT_API', 120),
        'api-workspace' => (int) env('RATE_LIMIT_API_WORKSPACE', 200),
        'api-sensitive' => (int) env('RATE_LIMIT_API_SENSITIVE', 10),
        'api-auth' => (int) env('RATE_LIMIT_API_AUTH', 5),
        'web' => (int) env('RATE_LIMIT_WEB', 300),
        'webhook' => (int) env('RATE_LIMIT_WEBHOOK', 30),
        'super-admin-settings' => (int) env('RATE_LIMIT_SUPER_ADMIN_SETTINGS', 10),
        'login' => (int) env('RATE_LIMIT_LOGIN', 5),
        'register' => (int) env('RATE_LIMIT_REGISTER', 3),

        // Inline throttle names (used in route middleware)
        'web-list' => (int) env('RATE_LIMIT_WEB_LIST', 300),
        'web-search' => (int) env('RATE_LIMIT_WEB_SEARCH', 120),
        'web-crud' => (int) env('RATE_LIMIT_WEB_CRUD', 60),
        'web-delete' => (int) env('RATE_LIMIT_WEB_DELETE', 30),
        'web-sensitive' => (int) env('RATE_LIMIT_WEB_SENSITIVE', 10),
        'web-proof' => (int) env('RATE_LIMIT_WEB_PROOF', 6),
        'web-invite-resend' => (int) env('RATE_LIMIT_WEB_INVITE_RESEND', 5),
    ],
];
