<?php

namespace Tests\Unit;

use App\Services\Payments\Chargily\ChargilyClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ChargilyClientTest extends TestCase
{
    use RefreshDatabase;

    private array $savedConfig = [];

    protected function setUp(): void
    {
        parent::setUp();
        ChargilyClient::forgetInstance();

        $this->savedConfig = [
            'public_key' => config('payment.gateways.chargily.public_key'),
            'secret_key' => config('payment.gateways.chargily.secret_key'),
        ];
    }

    protected function tearDown(): void
    {
        Config::set('payment.gateways.chargily.public_key', $this->savedConfig['public_key']);
        Config::set('payment.gateways.chargily.secret_key', $this->savedConfig['secret_key']);
        ChargilyClient::forgetInstance();
        parent::tearDown();
    }

    public function test_make_throws_when_missing_keys(): void
    {
        Config::set('payment.gateways.chargily.public_key', null);
        Config::set('payment.gateways.chargily.secret_key', null);

        $this->expectException(\RuntimeException::class);
        ChargilyClient::make();
    }

    public function test_setting_returns_config_default(): void
    {
        $this->assertSame('fallback', ChargilyClient::setting('nonexistent_key', 'fallback'));
    }

    public function test_setting_returns_configured_value(): void
    {
        Config::set('payment.gateways.chargily.custom_key', 'configured_value');

        $this->assertSame('configured_value', ChargilyClient::setting('custom_key'));
    }

    public function test_make_uses_config_values(): void
    {
        Config::set('payment.gateways.chargily.mode', 'test');
        Config::set('payment.gateways.chargily.public_key', 'pk_test_abcdef1234567890');
        Config::set('payment.gateways.chargily.secret_key', 'sk_test_abcdef1234567890');

        $client = ChargilyClient::make();

        $this->assertInstanceOf(\Chargily\ChargilyPay\ChargilyPay::class, $client);
    }

    public function test_forgetInstance_resets_singleton(): void
    {
        Config::set('payment.gateways.chargily.mode', 'test');
        Config::set('payment.gateways.chargily.public_key', 'pk_test_forget12345678');
        Config::set('payment.gateways.chargily.secret_key', 'sk_test_forget12345678');

        $first = ChargilyClient::make();
        $firstId = spl_object_id($first);
        ChargilyClient::forgetInstance();
        $second = ChargilyClient::make();

        $this->assertNotSame($firstId, spl_object_id($second));
    }
}
