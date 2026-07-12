<?php

namespace App\Services\Payments;

class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?string $transactionId = null,
        public readonly ?string $reference = null,
        public readonly ?array $metadata = null,
        public readonly ?string $redirectUrl = null,
        public readonly bool $pending = false,
    ) {}

    public static function success(
        string $message,
        ?string $transactionId = null,
        ?string $reference = null,
        ?array $metadata = null,
        ?string $redirectUrl = null,
    ): self {
        return new self(true, $message, $transactionId, $reference, $metadata, $redirectUrl);
    }

    public static function pending(
        string $message,
        ?string $transactionId = null,
        ?string $reference = null,
        ?array $metadata = null,
        ?string $redirectUrl = null,
    ): self {
        return new self(true, $message, $transactionId, $reference, $metadata, $redirectUrl, pending: true);
    }

    public static function failed(string $message, ?array $metadata = null): self
    {
        return new self(false, $message, null, null, $metadata);
    }

    public function isPending(): bool
    {
        return $this->pending;
    }
}
