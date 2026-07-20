<?php

namespace App\Helpers;

use App\Models\Setting;

class CurrencyHelper
{
    private static array $defaults = [
        ['code' => 'DZD', 'name' => 'Algerian Dinar', 'symbol' => 'د.ج'],
        ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
        ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
        ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
        ['code' => 'USDT', 'name' => 'Tether', 'symbol' => '₮'],
        ['code' => 'BTC', 'name' => 'Bitcoin', 'symbol' => '₿'],
        ['code' => 'ETH', 'name' => 'Ethereum', 'symbol' => 'Ξ'],
    ];

    public static function availableCurrencies(): array
    {
        $stored = json_decode(Setting::get('currencies', '[]'), true);

        return is_array($stored) && count($stored) > 0 ? $stored : self::$defaults;
    }

    public static function availableCurrencyCodes(): array
    {
        return array_column(self::availableCurrencies(), 'code');
    }
}
