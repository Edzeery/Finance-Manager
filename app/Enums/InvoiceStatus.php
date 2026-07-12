<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('general.draft'),
            self::Paid => __('general.paid'),
            self::Overdue => __('general.overdue'),
            self::Cancelled => __('general.cancelled'),
        };
    }
}
