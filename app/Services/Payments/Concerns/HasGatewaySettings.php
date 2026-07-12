<?php

namespace App\Services\Payments\Concerns;

use App\Models\PaymentMethod;

trait HasGatewaySettings
{
    protected ?PaymentMethod $_cachedMethod = null;

    protected function gatewaySetting(string $key, mixed $fallback = null): mixed
    {
        if ($this->_cachedMethod === null) {
            $this->_cachedMethod = PaymentMethod::where('key', $this->name())->first();
        }

        if ($this->_cachedMethod === null) {
            return config("payment.gateways.{$this->name()}.{$key}", $fallback);
        }

        return $this->_cachedMethod->credential($key) ?? config("payment.gateways.{$this->name()}.{$key}", $fallback);
    }
}
