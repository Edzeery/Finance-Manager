<?php

namespace App\Services\Payments;

use InvalidArgumentException;

class GatewayManager
{
    private array $gateways = [];

    public function register(string $name, PaymentGateway $gateway): void
    {
        $this->gateways[$name] = $gateway;
    }

    public function driver(?string $name = null): PaymentGateway
    {
        $name ??= config('payment.default', 'cash');

        if (!isset($this->gateways[$name])) {
            throw new InvalidArgumentException("Payment gateway '{$name}' is not registered.");
        }

        return $this->gateways[$name];
    }

    public function all(): array
    {
        return $this->gateways;
    }

    public function online(): array
    {
        return array_filter($this->gateways, fn(PaymentGateway $g) => $g->isOnline());
    }

    public function offline(): array
    {
        return array_filter($this->gateways, fn(PaymentGateway $g) => $g->isOffline());
    }
}
