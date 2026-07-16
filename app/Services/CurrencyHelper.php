<?php

namespace App\Services;

use App\Models\Setting;

class CurrencyHelper
{
    public static function rates(): array
    {
        $stored = Setting::get('exchange_rates');
        $default = config('finance.exchange_rates', ['USD' => 1, 'DZD' => 250, 'EUR' => 0.877]);

        if ($stored) {
            $decoded = json_decode($stored, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $default;
    }

    public static function baseCurrency(): string
    {
        return Setting::get('base_currency', config('finance.base_currency', 'USD'));
    }

    public static function symbol(string $currency): string
    {
        if ($currency === 'DZD') {
            return app()->getLocale() === 'ar' ? 'د.ج' : 'DZD';
        }

        return config('finance.currencies.'.$currency.'.symbol', $currency);
    }

    public static function convert(float $amount, string $from, string $to): float
    {
        $rates = self::rates();
        $base = self::baseCurrency();

        $inBase = isset($rates[$from]) ? $amount / $rates[$from] : $amount;
        $converted = isset($rates[$to]) ? $inBase * $rates[$to] : $inBase;

        return round($converted, 2);
    }

    public static function fromUsd(float $usdAmount, string $to): float
    {
        return self::convert($usdAmount, 'USD', $to);
    }
}
