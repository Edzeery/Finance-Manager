<?php

namespace App\Enums;

enum PaymentWebhookLogStatus: string
{
    case Received = 'received';
    case Processed = 'processed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Received => __('super-admin.webhook_received'),
            self::Processed => __('super-admin.webhook_processed'),
            self::Failed => __('super-admin.webhook_failed'),
        };
    }
}
