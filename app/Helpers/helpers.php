<?php

use App\Helpers\CurrencyFormatter;

if (!function_exists('currency_format')) {
    function currency_format(float $value, ?string $currency = null, ?int $decimalPlaces = null): string
    {
        return CurrencyFormatter::format($value, $currency, $decimalPlaces);
    }
}

if (!function_exists('locale_name')) {
    function locale_name(object $model, string $prefix = 'name'): string
    {
        return CurrencyFormatter::localeName($model, $prefix);
    }
}
