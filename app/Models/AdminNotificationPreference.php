<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotificationPreference extends Model
{
    protected $table = 'admin_notification_preferences';

    protected $fillable = [
        'user_id',
        'type',
        'in_app_enabled',
        'email_enabled',
    ];

    protected function casts(): array
    {
        return [
            'in_app_enabled' => 'boolean',
            'email_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function isEnabledFor(int $userId, string $type, string $channel = 'in_app'): bool
    {
        $preference = static::where('user_id', $userId)
            ->where('type', $type)
            ->first();

        if (! $preference) {
            return true;
        }

        return $channel === 'email'
            ? $preference->email_enabled
            : $preference->in_app_enabled;
    }

    public static function getAllTypes(): array
    {
        return [
            'new_user',
            'new_payment',
            'subscription_activated',
            'backup_completed',
            'system_alert',
        ];
    }
}
