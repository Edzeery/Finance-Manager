<?php

namespace App\Models;

use App\Enums\OnlineStatus;
use App\Enums\UserStatus as UserStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStatus extends Model
{
    protected $table = 'user_status';

    protected $fillable = [
        'user_id',
        'status',
        'online_status',
        'status_reason',
        'status_changed_by',
        'status_changed_at',
        'last_login_at',
        'last_login_ip',
        'last_user_agent',
        'last_device',
        'last_browser',
        'last_os',
        'last_activity_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => UserStatusEnum::class,
            'online_status' => OnlineStatus::class,
            'status_changed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_changed_by');
    }

    public function changeStatus(
        UserStatusEnum $newStatus,
        ?string $reason = null,
        ?int $changedBy = null,
        ?Carbon $expiresAt = null,
    ): void {
        $oldStatus = $this->status;

        $this->update([
            'status' => $newStatus,
            'status_reason' => $reason,
            'status_changed_by' => $changedBy,
            'status_changed_at' => now(),
            'expires_at' => $expiresAt,
        ]);

        // Record history
        UserStatusHistory::create([
            'user_id' => $this->user_id,
            'old_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
            'reason' => $reason,
            'changed_by' => $changedBy,
            'ip_address' => request()?->ip(),
            'changed_at' => now(),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function trackLogin(string $ip, ?string $userAgent = null, ?string $device = null, ?string $browser = null, ?string $os = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'last_user_agent' => $userAgent,
            'last_device' => $device,
            'last_browser' => $browser,
            'last_os' => $os,
            'online_status' => OnlineStatus::Online,
        ]);
    }

    public function trackLogout(): void
    {
        $this->update([
            'online_status' => OnlineStatus::Offline,
        ]);
    }

    public function trackActivity(): void
    {
        $this->update([
            'last_activity_at' => now(),
        ]);
    }

    public function isInactive(int $minutes = 15): bool
    {
        if (! $this->last_activity_at) {
            return true;
        }

        return $this->last_activity_at->diffInMinutes(now()) >= $minutes;
    }
}
