<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'email',
        'status',
        'ip_address',
        'user_agent',
        'device',
        'browser',
        'os',
        'failure_reason',
        'suspicious',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'suspicious' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function detectSuspicious(string $email, string $ip, int $withinMinutes = 15, int $maxAttempts = 5): bool
    {
        $recentFailed = self::where('email', $email)
            ->where('status', 'failed')
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subMinutes($withinMinutes))
            ->count();

        if ($recentFailed >= $maxAttempts) {
            return true;
        }

        $differentIps = self::where('email', $email)
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subMinutes($withinMinutes))
            ->distinct('ip_address')
            ->count('ip_address');

        if ($differentIps >= 3) {
            return true;
        }

        return false;
    }

    public static function recentFailuresForIp(string $ip, int $withinMinutes = 15): int
    {
        return self::where('ip_address', $ip)
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subMinutes($withinMinutes))
            ->count();
    }
}
