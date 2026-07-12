<?php

namespace App\Enums;

enum DebtStatus: string
{
    case Active = 'active';
    case Partial = 'partial';
    case Paid = 'paid';
    case Overdue = 'overdue';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Active => __('debt.active'),
            self::Partial => __('debt.partial'),
            self::Paid => __('debt.paid'),
            self::Overdue => __('debt.overdue'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => '#3B82F6',
            self::Partial => '#F59E0B',
            self::Paid => '#22C55E',
            self::Overdue => '#EF4444',
        };
    }
}
