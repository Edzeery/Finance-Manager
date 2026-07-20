<?php

return [
    'zakat_rate' => 0.025, // 2.5%

    'nisab' => [
        'gold_grams' => 85, // 85 grams of gold
        'silver_grams' => 595, // 595 grams of silver
    ],

    'karat_purity' => [
        24 => 1.0,
        22 => 0.9167,
        21 => 0.875,
        18 => 0.75,
        14 => 0.5833,
        10 => 0.4167,
    ],

    'prices' => [
        'gold_per_gram' => env('ZAKAT_GOLD_PRICE', 0),
        'silver_per_gram' => env('ZAKAT_SILVER_PRICE', 0),
        'manual_override' => false,
        'default_karat' => 24,
    ],

    'haul_days' => 354, // Islamic lunar year

    'assets' => [
        'zakatable_types' => [
            'cash', 'bank_account', 'ccp', 'gold', 'silver', 'stocks', 'crypto',
        ],
        'zakatable_real_estate' => false, // Only investment/trade real estate
    ],
];
