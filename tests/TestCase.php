<?php

namespace Tests;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Livewire\Component;
use Livewire\Mechanisms\ComponentRegistry;
use PHPUnit\Framework\Assert;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(PreventRequestForgery::class);

        $this->registerLivewireTestingMacros();

        $this->seedPaymentMethods();
    }

    protected function seedPaymentMethods(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('payment_methods')) {
            return;
        }

        $methods = [
            ['key' => 'chargily', 'name' => 'Chargily', 'type' => 'online'],
            ['key' => 'paypal', 'name' => 'PayPal', 'type' => 'online'],
            ['key' => 'stripe', 'name' => 'Stripe', 'type' => 'online'],
            ['key' => 'wise', 'name' => 'Wise', 'type' => 'online'],
            ['key' => 'payoneer', 'name' => 'Payoneer', 'type' => 'online'],
            ['key' => 'baridimob', 'name' => 'BaridiMob', 'type' => 'manual'],
            ['key' => 'redotpay', 'name' => 'RedotPay', 'type' => 'manual'],
            ['key' => 'wise_manual', 'name' => 'Wise Transfer', 'type' => 'manual'],
            ['key' => 'cash', 'name' => 'Cash', 'type' => 'manual'],
            ['key' => 'delivery', 'name' => 'Delivery', 'type' => 'manual'],
            ['key' => 'noest', 'name' => 'Noest', 'type' => 'auto_complete'],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(['key' => $method['key']], $method);
        }
    }

    private function registerLivewireTestingMacros(): void
    {
        if (TestResponse::hasMacro('assertSeeLivewire')) {
            return;
        }

        TestResponse::macro('assertSeeLivewire', function ($component) {
            if (is_subclass_of($component, Component::class)) {
                $component = app(ComponentRegistry::class)->getName($component);
            }
            $escapedComponentName = trim(htmlspecialchars(json_encode(['name' => $component])), '{}');

            Assert::assertStringContainsString(
                $escapedComponentName,
                $this->getContent(),
                'Cannot find Livewire component ['.$component.'] rendered on page.'
            );

            return $this;
        });

        TestResponse::macro('assertDontSeeLivewire', function ($component) {
            if (is_subclass_of($component, Component::class)) {
                $component = app(ComponentRegistry::class)->getName($component);
            }
            $escapedComponentName = trim(htmlspecialchars(json_encode(['name' => $component])), '{}');

            Assert::assertStringNotContainsString(
                $escapedComponentName,
                $this->getContent(),
                'Found Livewire component ['.$component.'] rendered on page.'
            );

            return $this;
        });
    }
}
