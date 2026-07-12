<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['key' => 'chargily',    'name' => 'Chargily',     'description' => 'Edahabia / CIB cards via Chargily',           'icon' => 'bi-credit-card-2-front',
             'is_active' => true,  'is_public' => true,  'type' => 'online',        'sort_order' => 1],
            ['key' => 'baridimob',   'name' => 'BaridiMob',   'description' => 'Algerian postal payment BaridiMob',            'icon' => 'bi-phone',
             'is_active' => true,  'is_public' => true,  'type' => 'manual',        'sort_order' => 2],
            ['key' => 'redotpay',    'name' => 'RedotPay',    'description' => 'RedotPay prepaid card',                        'icon' => 'bi-wallet2',
             'is_active' => true,  'is_public' => true,  'type' => 'manual',        'sort_order' => 3],
            ['key' => 'wise_manual', 'name' => 'Wise Transfer','description' => 'Manual Wise transfer',                         'icon' => 'bi-bank',
             'is_active' => false, 'is_public' => false, 'type' => 'manual',        'sort_order' => 4],
            ['key' => 'cash',        'name' => 'Cash',        'description' => 'Cash payment in person',                       'icon' => 'bi-cash',
             'is_active' => false, 'is_public' => false, 'type' => 'manual',        'sort_order' => 5],
            ['key' => 'paypal',      'name' => 'PayPal',      'description' => 'PayPal online payment',                        'icon' => 'bi-paypal',
             'is_active' => false, 'is_public' => false, 'type' => 'online',        'sort_order' => 6],
            ['key' => 'stripe',      'name' => 'Stripe',      'description' => 'Stripe credit/debit card payment',             'icon' => 'bi-credit-card-2-front',
             'is_active' => false, 'is_public' => false, 'type' => 'online',        'sort_order' => 7],
            ['key' => 'wise',        'name' => 'Wise',        'description' => 'Wise online transfer',                         'icon' => 'bi-bank',
             'is_active' => false, 'is_public' => false, 'type' => 'online',        'sort_order' => 8],
            ['key' => 'noest',       'name' => 'Noest (نواست)','description' => 'Noest delivery with auto-complete',            'icon' => 'bi-truck',
             'is_active' => false, 'is_public' => false, 'type' => 'auto_complete', 'sort_order' => 9],
            ['key' => 'payoneer',    'name' => 'Payoneer',    'description' => 'Payoneer online payment',                      'icon' => 'bi-currency-dollar',
             'is_active' => false, 'is_public' => false, 'type' => 'online',        'sort_order' => 10],
            ['key' => 'delivery',    'name' => 'Delivery',    'description' => 'Payment on delivery',                          'icon' => 'bi-box-seam',
             'is_active' => false, 'is_public' => false, 'type' => 'auto_complete', 'sort_order' => 11],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
