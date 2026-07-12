<?php

namespace App\Enums;

enum RecurringFrequency: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Daily => __('general.daily'),
            self::Weekly => __('general.weekly'),
            self::Monthly => __('general.monthly'),
            self::Yearly => __('general.yearly'),
        };
    }
}
