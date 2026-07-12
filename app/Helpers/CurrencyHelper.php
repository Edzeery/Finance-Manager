<?php

namespace App\Helpers;

use App\Models\Setting;

class CurrencyHelper
{
    public static function availableCurrencies(): array
    {
        return json_decode(Setting::get('currencies', '[]'), true) ?: [];
    }

    public static function availableCurrencyCodes(): array
    {
        return array_column(self::availableCurrencies(), 'code');
    }
}
