<?php

namespace App\Enums;

use Edzeery\MyStatusKit\Facades\Status;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';
    case Suspended = 'suspended';
    case Banned = 'banned';

    public function label(): string
    {
        return __('status-kit::statuses.user.'.$this->value);
    }

    public function color(): string
    {
        return Status::for('user', $this->value)->color();
    }

    public function badge(?string $set = null): string
    {
        return Status::for('user', $this->value)->badge($set);
    }

    public function icon(?string $set = null): string
    {
        return Status::for('user', $this->value)->icon($set);
    }

    public function isAccessible(): bool
    {
        return $this === self::Active;
    }

    public function isBlocked(): bool
    {
        return in_array($this, [self::Suspended, self::Banned]);
    }

    public function requiresAttention(): bool
    {
        return in_array($this, [self::Inactive, self::Pending]);
    }

    public static function accessible(): array
    {
        return [self::Active];
    }

    public static function nonAccessible(): array
    {
        return [self::Inactive, self::Pending, self::Suspended, self::Banned];
    }
}
