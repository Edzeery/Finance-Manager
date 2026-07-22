<?php

namespace Tests\Unit\Payments;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\Payments\BaridiMobGateway;
use App\Services\Payments\CashGateway;
use App\Services\Payments\Chargily\ChargilyCheckoutService;
use App\Services\Payments\Chargily\ChargilyGateway;
use App\Services\Payments\Chargily\DTOs\CheckoutData;
use App\Services\Payments\DeliveryGateway;
use App\Services\Payments\GatewayManager;
use App\Services\Payments\Noest\NoestGateway;
use App\Services\Payments\Noest\NoestService;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentResult;
use App\Services\Payments\PayoneerGateway;
use App\Services\Payments\PayPalGateway;
use App\Services\Payments\RedotPayGateway;
use App\Services\Payments\StripeGateway;
use App\Services\Payments\WiseGateway;
use App\Services\Payments\WiseManualGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GatewayTest extends TestCase
{
    use RefreshDatabase;

    private GatewayManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->app->make(GatewayManager::class);
    }

    public function test_all_gateways_are_registered(): void
    {
        $expected = ['chargily', 'baridimob', 'paypal', 'redotpay', 'stripe', 'wise', 'wise_manual', 'payoneer', 'cash', 'delivery', 'noest'];
        $this->assertSame($expected, array_keys($this->manager->all()));
    }

    public function test_each_gateway_implements_payment_gateway_interface(): void
    {
        foreach ($this->manager->all() as $name => $gateway) {
            $this->assertInstanceOf(PaymentGateway::class, $gateway, "Gateway '$name' must implement PaymentGateway");
        }
    }

    public function test_each_gateway_has_name(): void
    {
        foreach ($this->manager->all() as $name => $gateway) {
            $this->assertSame($name, $gateway->name(), "Gateway '$name' must return its name");
        }
    }

    public function test_each_gateway_has_supported_currencies(): void
    {
        foreach ($this->manager->all() as $name => $gateway) {
            $currencies = $gateway->supportedCurrencies();
            $this->assertIsArray($currencies, "Gateway '$name' must return array of supported currencies");
            $this->assertNotEmpty($currencies, "Gateway '$name' must support at least one currency");
        }
    }

    public function test_each_gateway_is_online_or_offline(): void
    {
        foreach ($this->manager->all() as $name => $gateway) {
            $isOnline = $gateway->isOnline();
            $isOffline = $gateway->isOffline();
            $this->assertIsBool($isOnline, "Gateway '$name' isOnline must return bool");
            $this->assertIsBool($isOffline, "Gateway '$name' isOffline must return bool");
            $this->assertNotSame($isOnline, $isOffline, "Gateway '$name' must be online XOR offline");
        }
    }

    public function test_each_gateway_verify_returns_payment_result(): void
    {
        $payment = Payment::factory()->make();
        foreach ($this->manager->all() as $name => $gateway) {
            $result = $gateway->verify(clone $payment);
            $this->assertInstanceOf(PaymentResult::class, $result, "Gateway '$name' verify must return PaymentResult");
        }
    }

    public function test_chargily_charge_success(): void
    {
        $checkoutService = $this->createMock(ChargilyCheckoutService::class);
        $checkoutService->method('create')->willReturn(new CheckoutData(
            id: 'ch_test_123',
            url: 'https://checkout.chargily.com/test/abc',
            status: 'pending',
            amount: 1000,
            currency: 'DZD',
        ));

        $gateway = new ChargilyGateway($checkoutService);
        $result = $gateway->charge(['amount' => 1000, 'currency' => 'DZD']);

        $this->assertTrue($result->success);
        $this->assertNotNull($result->redirectUrl);
    }

    public function test_chargily_charge_exception_returns_failure(): void
    {
        $checkoutService = $this->createMock(ChargilyCheckoutService::class);
        $checkoutService->method('create')->willThrowException(new \RuntimeException('API error'));

        $gateway = new ChargilyGateway($checkoutService);
        $result = $gateway->charge(['amount' => 1000]);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Chargily charge failed', $result->message);
    }

    public function test_chargily_charge_empty_data(): void
    {
        $checkoutService = $this->createMock(ChargilyCheckoutService::class);
        $checkoutService->method('create')->willThrowException(new \RuntimeException('Validation error'));

        $gateway = new ChargilyGateway($checkoutService);
        $result = $gateway->charge([]);

        $this->assertFalse($result->success);
    }

    public function test_baridimob_charge_success(): void
    {
        PaymentMethod::updateOrCreate(['key' => 'baridimob'], [
            'key' => 'baridimob',
            'name' => 'BaridiMob',
            'type' => 'manual',
            'is_active' => true,
            'credentials' => [
                'rip_number' => '00799999',
                'account_holder_name' => 'Test Account',
            ],
        ]);

        $gateway = new BaridiMobGateway;
        $result = $gateway->charge([
            'amount' => 5000,
            'currency' => 'DZD',
            'reference' => 'ref_123',
        ]);

        $this->assertTrue($result->success);
    }

    public function test_baridimob_charge_not_configured(): void
    {
        Config::set('payment.gateways.baridimob.rip_number', null);

        $gateway = new BaridiMobGateway;
        $result = $gateway->charge([]);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('not configured', $result->message);
    }

    public function test_baridimob_refund_returns_failure(): void
    {
        $gateway = new BaridiMobGateway;
        $payment = Payment::factory()->make();
        $result = $gateway->refund($payment);

        $this->assertFalse($result->success);
    }

    public function test_cash_charge_success(): void
    {
        $gateway = new CashGateway;
        $result = $gateway->charge([
            'amount' => 1000,
            'reference' => 'cash_ref_1',
        ]);

        $this->assertTrue($result->success);
        $this->assertStringContainsString('Cash payment recorded', $result->message);
    }

    public function test_cash_charge_empty_data(): void
    {
        $gateway = new CashGateway;
        $result = $gateway->charge([]);

        $this->assertTrue($result->success);
    }

    public function test_cash_refund_returns_failure(): void
    {
        $gateway = new CashGateway;
        $result = $gateway->refund(Payment::factory()->make());

        $this->assertFalse($result->success);
    }

    public function test_delivery_charge_success(): void
    {
        $gateway = new DeliveryGateway;
        $result = $gateway->charge([
            'amount' => 2000,
            'address' => '123 Main St, Algiers',
        ]);

        $this->assertTrue($result->success);
    }

    public function test_delivery_charge_missing_address(): void
    {
        $gateway = new DeliveryGateway;
        $result = $gateway->charge(['amount' => 2000]);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('address is required', $result->message);
    }

    public function test_delivery_refund_returns_failure(): void
    {
        $gateway = new DeliveryGateway;
        $payment = Payment::factory()->make();
        $result = $gateway->refund($payment);

        $this->assertFalse($result->success);
    }

    public function test_redotpay_charge_success(): void
    {
        Config::set('payment.gateways.redotpay.wallet_address', '0x1234567890abcdef');

        $gateway = new RedotPayGateway;
        $result = $gateway->charge([
            'amount' => 0.05,
            'currency' => 'USDT',
        ]);

        $this->assertTrue($result->success);
    }

    public function test_redotpay_charge_not_configured(): void
    {
        Config::set('payment.gateways.redotpay.wallet_address', null);

        $gateway = new RedotPayGateway;
        $result = $gateway->charge(['amount' => 0.05]);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('not configured', $result->message);
    }

    public function test_redotpay_refund_returns_failure(): void
    {
        $gateway = new RedotPayGateway;
        $payment = Payment::factory()->make();
        $result = $gateway->refund($payment);

        $this->assertFalse($result->success);
    }

    public function test_wise_manual_charge_success(): void
    {
        PaymentMethod::updateOrCreate(['key' => 'wise_manual'], [
            'key' => 'wise_manual',
            'name' => 'Wise (Manual)',
            'type' => 'manual',
            'is_active' => true,
            'credentials' => [
                'account_email' => 'test@wise.com',
                'account_holder_name' => 'Test User',
            ],
        ]);

        $gateway = new WiseManualGateway;
        $result = $gateway->charge([
            'amount' => 50000,
            'currency' => 'DZD',
        ]);

        $this->assertTrue($result->success);
    }

    public function test_wise_manual_charge_not_configured(): void
    {
        Config::set('payment.gateways.wise_manual.account_email', null);

        $gateway = new WiseManualGateway;
        $result = $gateway->charge(['amount' => 50000]);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('not configured', $result->message);
    }

    public function test_wise_manual_refund_returns_failure(): void
    {
        $gateway = new WiseManualGateway;
        $payment = Payment::factory()->make();
        $result = $gateway->refund($payment);

        $this->assertFalse($result->success);
    }

    public function test_paypal_charge_failure_when_not_configured(): void
    {
        Config::set('payment.gateways.paypal.client_id', null);
        Config::set('payment.gateways.paypal.secret', null);

        $gateway = new PayPalGateway;
        $result = $gateway->charge(['amount' => 100, 'currency' => 'USD']);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('not configured', $result->message);
    }

    public function test_paypal_charge_success(): void
    {
        Config::set('payment.gateways.paypal.client_id', 'test_client');
        Config::set('payment.gateways.paypal.secret', 'test_secret');
        Config::set('payment.gateways.paypal.sandbox', true);

        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'test_token']),
            'https://api-m.sandbox.paypal.com/v1/payments/payment' => Http::response(['id' => 'PAY_test', 'state' => 'approved']),
        ]);

        $gateway = new PayPalGateway;
        $result = $gateway->charge(['amount' => 100, 'currency' => 'USD']);

        $this->assertTrue($result->success);
    }

    public function test_paypal_charge_http_error(): void
    {
        Config::set('payment.gateways.paypal.client_id', 'test_client');
        Config::set('payment.gateways.paypal.secret', 'test_secret');

        Http::fake([
            '*' => Http::response('Unauthorized', 401),
        ]);

        $gateway = new PayPalGateway;
        $result = $gateway->charge(['amount' => 100, 'currency' => 'USD']);

        $this->assertFalse($result->success);
    }

    public function test_stripe_charge_failure_when_not_configured(): void
    {
        Config::set('payment.gateways.stripe.secret_key', null);

        $gateway = new StripeGateway;
        $result = $gateway->charge(['amount' => 50, 'currency' => 'USD']);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('not configured', $result->message);
    }

    public function test_stripe_charge_success(): void
    {
        Config::set('payment.gateways.stripe.secret_key', 'sk_test_123');
        Config::set('payment.gateways.stripe.sandbox', true);

        Http::fake([
            'https://api.stripe.com/v1/payment_intents' => Http::response(['id' => 'pi_test', 'status' => 'requires_payment_method', 'next_action' => ['redirect_to_url' => ['url' => 'https://stripe.com/redirect']]]),
        ]);

        $gateway = new StripeGateway;
        $result = $gateway->charge(['amount' => 50, 'currency' => 'usd']);

        $this->assertTrue($result->success);
    }

    public function test_stripe_charge_http_error(): void
    {
        Config::set('payment.gateways.stripe.secret_key', 'sk_test_123');

        Http::fake([
            '*' => Http::response('Server Error', 500),
        ]);

        $gateway = new StripeGateway;
        $result = $gateway->charge(['amount' => 50, 'currency' => 'usd']);

        $this->assertFalse($result->success);
    }

    public function test_wise_charge_failure_when_not_configured(): void
    {
        Config::set('payment.gateways.wise.api_key', null);

        $gateway = new WiseGateway;
        $result = $gateway->charge(['amount' => 200, 'currency' => 'EUR']);

        $this->assertFalse($result->success);
    }

    public function test_wise_charge_success(): void
    {
        Config::set('payment.gateways.wise.api_key', 'wise_test_key');
        Config::set('payment.gateways.wise.sandbox', true);
        Config::set('payment.gateways.wise.recipient_account_id', 'acc_123');

        Http::fake([
            'https://api.sandbox.transferwise.tech/v1/transfers' => Http::response(['id' => 'tr_123', 'customerTransactionId' => 'ctr_123']),
        ]);

        $gateway = new WiseGateway;
        $result = $gateway->charge(['amount' => 200, 'currency' => 'EUR']);

        $this->assertTrue($result->success);
    }

    public function test_wise_charge_http_error(): void
    {
        Config::set('payment.gateways.wise.api_key', 'wise_test_key');
        Config::set('payment.gateways.wise.recipient_account_id', 'acc_123');

        Http::fake([
            '*' => Http::response('Bad Request', 400),
        ]);

        $gateway = new WiseGateway;
        $result = $gateway->charge(['amount' => 200, 'currency' => 'EUR']);

        $this->assertFalse($result->success);
    }

    public function test_payoneer_charge_failure_when_not_configured(): void
    {
        Config::set('payment.gateways.payoneer.client_id', null);

        $gateway = new PayoneerGateway;
        $result = $gateway->charge(['amount' => 300, 'currency' => 'USD']);

        $this->assertFalse($result->success);
    }

    public function test_payoneer_charge_success(): void
    {
        Config::set('payment.gateways.payoneer.client_id', 'po_client');
        Config::set('payment.gateways.payoneer.client_secret', 'po_secret');
        Config::set('payment.gateways.payoneer.program_id', 'po_prog');
        Config::set('payment.gateways.payoneer.sandbox', true);

        Http::fake([
            'https://api.sandbox.payoneer.com/v2/oauth/token' => Http::response(['access_token' => 'po_token']),
            'https://api.sandbox.payoneer.com/v2/programs/po_prog/payouts' => Http::response(['id' => 'po_pay_123', 'status' => 'success']),
        ]);

        $gateway = new PayoneerGateway;
        $result = $gateway->charge(['amount' => 300, 'currency' => 'USD']);

        $this->assertTrue($result->success);
    }

    public function test_payoneer_charge_http_error(): void
    {
        Config::set('payment.gateways.payoneer.client_id', 'po_client');
        Config::set('payment.gateways.payoneer.client_secret', 'po_secret');

        Http::fake([
            '*' => Http::response('Forbidden', 403),
        ]);

        $gateway = new PayoneerGateway;
        $result = $gateway->charge(['amount' => 300, 'currency' => 'USD']);

        $this->assertFalse($result->success);
    }

    public function test_noest_charge_success(): void
    {
        $payment = Payment::factory()->create(['reference' => 'noest_ref']);

        $noestService = $this->createMock(NoestService::class);
        $noestService->method('createOrder')->willReturn([
            'data' => ['tracking' => 'TRACK123'],
        ]);

        $this->app->instance(NoestService::class, $noestService);

        $gateway = new NoestGateway;
        $result = $gateway->charge([
            'payment_id' => $payment->id,
            'amount' => 1500,
            'noest_client' => 'Test Client',
            'noest_phone' => '0555000000',
        ]);

        $this->assertTrue($result->success);
    }

    public function test_noest_charge_payment_not_found(): void
    {
        $gateway = new NoestGateway;
        $result = $gateway->charge(['payment_id' => 99999]);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Payment not found', $result->message);
    }

    public function test_noest_charge_service_exception(): void
    {
        $payment = Payment::factory()->create(['reference' => 'noest_ref2']);

        $noestService = $this->createMock(NoestService::class);
        $noestService->method('createOrder')->willThrowException(new \RuntimeException('Noest API error'));

        $this->app->instance(NoestService::class, $noestService);

        $gateway = new NoestGateway;
        $result = $gateway->charge([
            'payment_id' => $payment->id,
            'amount' => 1500,
        ]);

        $this->assertFalse($result->success);
    }
}
