<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('currencies', json_encode([
            ['code' => 'DZD', 'name' => 'Algerian Dinar', 'symbol' => 'د.ج'],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['code' => 'USDT', 'name' => 'Tether', 'symbol' => '₮'],
            ['code' => 'BTC', 'name' => 'Bitcoin', 'symbol' => '₿'],
            ['code' => 'ETH', 'name' => 'Ethereum', 'symbol' => 'Ξ'],
        ]));

        if (! Setting::where('key', 'default_locale')->exists()) {
            Setting::set('default_locale', config('app.locale', 'ar'));
        }
        if (! Setting::where('key', 'app_name')->exists()) {
            Setting::set('app_name', config('app.name', 'Finance Manager'));
        }
        if (! Setting::where('key', 'registration_enabled')->exists()) {
            Setting::set('registration_enabled', '1');
        }
        Setting::set('exchange_rates', json_encode([
            'USD' => 1,
            'DZD' => 250,
            'EUR' => 0.877,
            'GBP' => 0.75,
            'USDT' => 1,
            'BTC' => 0.0000147,
            'ETH' => 0.00041,
        ]));
    }
}
