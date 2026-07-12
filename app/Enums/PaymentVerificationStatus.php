<?php

namespace App\Enums;

enum PaymentVerificationStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('super-admin.pending'),
            self::Approved => __('super-admin.approved'),
            self::Rejected => __('super-admin.rejected'),
        };
    }
}
