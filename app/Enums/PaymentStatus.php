<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case CheckoutPending = 'checkout.pending';
    case CheckoutPaid = 'checkout.paid';
    case CheckoutFailed = 'checkout.failed';
    case CheckoutCanceled = 'checkout.canceled';
    case CheckoutExpired = 'checkout.expired';

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::CheckoutPaid,
            self::CheckoutFailed,
            self::CheckoutCanceled,
            self::CheckoutExpired,
        ]);
    }

    public function isSuccess(): bool
    {
        return $this === self::CheckoutPaid;
    }

    public function isFailure(): bool
    {
        return in_array($this, [
            self::CheckoutFailed,
            self::CheckoutCanceled,
            self::CheckoutExpired,
        ]);
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, self::allowedTransitions()[$this->value] ?? []);
    }

    public static function allowedTransitions(): array
    {
        return [
            'checkout.pending' => ['checkout.paid', 'checkout.failed', 'checkout.canceled', 'checkout.expired'],
            'checkout.paid' => [],
            'checkout.failed' => ['checkout.pending'],
            'checkout.canceled' => ['checkout.pending'],
            'checkout.expired' => ['checkout.pending'],
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::CheckoutPending => __('enums.payment_status.checkout_pending'),
            self::CheckoutPaid => __('enums.payment_status.checkout_paid'),
            self::CheckoutFailed => __('enums.payment_status.checkout_failed'),
            self::CheckoutCanceled => __('enums.payment_status.checkout_canceled'),
            self::CheckoutExpired => __('enums.payment_status.checkout_expired'),
        };
    }
}
