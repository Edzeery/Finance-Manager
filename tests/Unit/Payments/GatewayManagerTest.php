<?php

namespace Tests\Unit\Payments;

use App\Services\Payments\GatewayManager;
use App\Services\Payments\PaymentGateway;
use InvalidArgumentException;
use Tests\TestCase;

class GatewayManagerTest extends TestCase
{
    private GatewayManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new GatewayManager;
    }

    public function test_register_and_driver(): void
    {
        $gateway = $this->createMock(PaymentGateway::class);
        $gateway->method('name')->willReturn('test_gateway');

        $this->manager->register('test_gateway', $gateway);

        $this->assertSame($gateway, $this->manager->driver('test_gateway'));
    }

    public function test_driver_returns_default_when_no_name_given(): void
    {
        $default = $this->createMock(PaymentGateway::class);
        $default->method('name')->willReturn('cash');
        $other = $this->createMock(PaymentGateway::class);
        $other->method('name')->willReturn('other');

        $this->manager->register('cash', $default);
        $this->manager->register('other', $other);

        $this->assertSame($default, $this->manager->driver());
    }

    public function test_driver_throws_for_unknown_gateway(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->manager->driver('nonexistent');
    }

    public function test_all_returns_all_registered_gateways(): void
    {
        $g1 = $this->createMock(PaymentGateway::class);
        $g2 = $this->createMock(PaymentGateway::class);

        $this->manager->register('g1', $g1);
        $this->manager->register('g2', $g2);

        $this->assertCount(2, $this->manager->all());
        $this->assertSame(['g1', 'g2'], array_keys($this->manager->all()));
    }

    public function test_online_filters_online_gateways(): void
    {
        $online = $this->createMock(PaymentGateway::class);
        $online->method('isOnline')->willReturn(true);
        $offline = $this->createMock(PaymentGateway::class);
        $offline->method('isOnline')->willReturn(false);

        $this->manager->register('online_gw', $online);
        $this->manager->register('offline_gw', $offline);

        $result = $this->manager->online();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('online_gw', $result);
    }

    public function test_offline_filters_offline_gateways(): void
    {
        $offline = $this->createMock(PaymentGateway::class);
        $offline->method('isOffline')->willReturn(true);
        $online = $this->createMock(PaymentGateway::class);
        $online->method('isOffline')->willReturn(false);

        $this->manager->register('offline_gw', $offline);
        $this->manager->register('online_gw', $online);

        $result = $this->manager->offline();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('offline_gw', $result);
    }

    public function test_throws_when_no_gateway_registered_and_no_default_config(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->manager->driver();
    }

    public function test_register_overwrites_existing_gateway(): void
    {
        $first = $this->createMock(PaymentGateway::class);
        $second = $this->createMock(PaymentGateway::class);

        $this->manager->register('gw', $first);
        $this->manager->register('gw', $second);

        $this->assertSame($second, $this->manager->driver('gw'));
    }
}
