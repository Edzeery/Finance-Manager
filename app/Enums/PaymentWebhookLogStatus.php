<?php

namespace App\Enums;

enum PaymentWebhookLogStatus: string
{
    case Received = 'received';
    case Processed = 'processed';

    public function label(): string
    {
        return match ($this) {
            self::Received => __('super-admin.webhook_received'),
            self::Processed => __('super-admin.webhook_processed'),
        };
    }
}
