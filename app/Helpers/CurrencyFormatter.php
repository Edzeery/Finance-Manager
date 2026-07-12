<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class CurrencyFormatter
{
    public static function format(float $value, ?string $currency = null, ?int $decimalPlaces = null): string
    {
        $currency ??= config('finance.currency', 'DZD');
        $decimalPlaces ??= config('finance.currencies.' . $currency . '.decimal_places', config('finance.decimal_places', 2));
        $symbol = config('finance.currencies.' . $currency . '.symbol', config('finance.currency_symbol', 'د.ج'));

        $formatted = number_format(
            $value,
            $decimalPlaces,
            config('finance.decimal_separator', '.'),
            config('finance.thousands_separator', ',')
        );

        $locale = App::getLocale();
        return match ($locale) {
            'ar' => $formatted . ' ' . $symbol,
            'fr' => $formatted . ' ' . $symbol,
            default => $symbol . $formatted,
        };
    }

    public static function localeName(object $model, string $prefix = 'name'): string
    {
        $locale = App::getLocale();
        $field = $prefix . '_' . $locale;

        if (isset($model->{$field}) && !empty($model->{$field})) {
            return $model->{$field};
        }

        $fallbacks = ['en', 'ar', 'fr'];
        foreach ($fallbacks as $fb) {
            if ($fb === $locale) continue;
            $fbField = $prefix . '_' . $fb;
            if (isset($model->{$fbField}) && !empty($model->{$fbField})) {
                return $model->{$fbField};
            }
        }

        foreach (['en', 'ar', 'fr'] as $fb) {
            $fbField = $prefix . '_' . $fb;
            if (isset($model->{$fbField})) {
                return $model->{$fbField};
            }
        }

        return '—';
    }
}
