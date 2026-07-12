<?php

namespace App\Services\Payments\Chargily\DTOs;

class CheckoutData
{
    public function __construct(
        public readonly string $id,
        public readonly string $url,
        public readonly string $status,
        public readonly float $amount,
        public readonly string $currency,
        public readonly ?array $metadata = null,
    ) {}

    public static function fromChargilyResponse(object $checkout): self
    {
        return new self(
            id: $checkout->getId() ?? throw new \InvalidArgumentException('Missing checkout ID'),
            url: $checkout->getUrl() ?? throw new \InvalidArgumentException('Missing checkout URL'),
            status: $checkout->getStatus() ?? 'pending',
            amount: (float) ($checkout->getAmount() ?? 0),
            currency: strtoupper($checkout->getCurrency() ?? 'DZD'),
            metadata: (array) ($checkout->getMetadata() ?? []),
        );
    }
}
