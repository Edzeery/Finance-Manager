<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $table = 'user_notification_preferences';

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
            // Financial
            'budget_exceeded',
            'budget_nearing_limit',
            'debt_reminder',
            // Goals
            'goal_achieved',
            'goal_milestone',
            'goal_deadline',
            // Zakat
            'zakat_reminder',
            'zakat_approaching',
            // Security
            'login_new_device',
            'login_suspicious',
            'password_changed',
            'two_factor_enabled',
            'two_factor_disabled',
            // Account
            'session_revoked',
            'email_changed',
            // Workspace
            'workspace_member_login',
        ];
    }
}
