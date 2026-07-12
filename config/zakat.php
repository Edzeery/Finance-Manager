<?php

return [
    'zakat_rate' => 0.025, // 2.5%

    'nisab' => [
        'gold_grams' => 85, // 85 grams of gold
        'silver_grams' => 595, // 595 grams of silver
    ],

    'prices' => [
        'gold_per_gram' => env('ZAKAT_GOLD_PRICE', 0),
        'silver_per_gram' => env('ZAKAT_SILVER_PRICE', 0),
    ],

    'haul_days' => 354, // Islamic lunar year

    'assets' => [
        'zakatable_types' => [
            'cash', 'bank_account', 'ccp', 'gold', 'silver', 'stocks', 'crypto',
        ],
        'zakatable_real_estate' => false, // Only investment/trade real estate
    ],
];
