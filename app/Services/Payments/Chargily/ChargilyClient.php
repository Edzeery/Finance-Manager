<?php

namespace App\Services\Payments\Chargily;

use App\Models\PaymentMethod;
use Chargily\ChargilyPay\Auth\Credentials;
use Chargily\ChargilyPay\ChargilyPay;

class ChargilyClient
{
    private static ?ChargilyPay $instance = null;
    private static ?PaymentMethod $_cachedMethod = null;

    public static function make(?PaymentMethod $method = null): ChargilyPay
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $mode = self::setting('mode', 'test', $method);
        $publicKey = self::setting('public_key', null, $method);
        $secretKey = self::setting('secret_key', null, $method);

        if (!$publicKey || !$secretKey) {
            throw new \RuntimeException('Chargily gateway is not configured.');
        }

        $credentials = new Credentials([
            'mode' => $mode === 'live' ? 'live' : 'test',
            'public' => $publicKey,
            'secret' => $secretKey,
        ]);

        return self::$instance = new ChargilyPay($credentials);
    }

    public static function setting(string $key, mixed $default = null, ?PaymentMethod $method = null): mixed
    {
        if ($method !== null) {
            return $method->credential($key) ?? config("payment.gateways.chargily.{$key}", $default);
        }

        if (self::$_cachedMethod === null) {
            self::$_cachedMethod = PaymentMethod::where('key', 'chargily')->first();
        }

        return self::$_cachedMethod?->credential($key) ?? config("payment.gateways.chargily.{$key}", $default);
    }

    public static function forgetInstance(): void
    {
        self::$instance = null;
        self::$_cachedMethod = null;
    }
}
