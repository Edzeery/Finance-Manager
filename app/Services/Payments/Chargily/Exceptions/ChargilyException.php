<?php

namespace App\Services\Payments\Chargily\Exceptions;

class ChargilyException extends \RuntimeException
{
    public static function configuration(?string $detail = null): self
    {
        return new self('Chargily gateway is not configured.'.($detail ? " {$detail}" : ''));
    }

    public static function checkoutCreationFailed(string $reason): self
    {
        return new self("Chargily checkout creation failed: {$reason}");
    }

    public static function checkoutNotFound(string $checkoutId): self
    {
        return new self("Chargily checkout not found: {$checkoutId}");
    }

    public static function invalidSignature(): self
    {
        return new self('Chargily webhook: invalid signature.');
    }

    public static function missingSignature(): self
    {
        return new self('Chargily webhook: missing signature header.');
    }

    public static function unhandledEvent(string $eventType): self
    {
        return new self("Chargily webhook: unhandled event type '{$eventType}'.");
    }
}
