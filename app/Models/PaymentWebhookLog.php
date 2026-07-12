<?php

namespace App\Models;

use App\Enums\PaymentWebhookLogStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentWebhookLog extends Model
{
    protected $fillable = [
        'gateway', 'event_type', 'checkout_id', 'payment_id',
        'payload', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'json',
            'status' => PaymentWebhookLogStatus::class,
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
