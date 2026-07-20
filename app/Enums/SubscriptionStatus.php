<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Active = 'active';
    case PastDue = 'past_due';
    case Expired = 'expired';
    case Canceled = 'canceled';
    case Trialing = 'trialing';

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Expired,
            self::Canceled,
        ]);
    }

    public function isActive(): bool
    {
        return in_array($this, [
            self::Active,
            self::Trialing,
        ]);
    }

    public function isBillable(): bool
    {
        return in_array($this, [
            self::Active,
            self::Trialing,
            self::PastDue,
        ]);
    }

    public function label(): string
    {
        return match ($this) {
            self::Active => __('enums.subscription_status.active'),
            self::PastDue => __('enums.subscription_status.past_due'),
            self::Expired => __('enums.subscription_status.expired'),
            self::Canceled => __('enums.subscription_status.canceled'),
            self::Trialing => __('enums.subscription_status.trialing'),
        };
    }
}
