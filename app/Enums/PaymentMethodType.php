<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case Online = 'online';
    case Manual = 'manual';
    case AutoComplete = 'auto_complete';

    public function label(): string
    {
        return match ($this) {
            self::Online => __('payment_method.online'),
            self::Manual => __('payment_method.manual'),
            self::AutoComplete => __('payment_method.auto_complete'),
        };
    }
}
