<?php

namespace App\Models;

use App\Enums\InvitationStatus;
use App\Models\Concerns\BelongsToWorkspace;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use BelongsToWorkspace, HasFactory;

    protected $table = 'workspace_invitations';

    protected $fillable = [
        'workspace_id',
        'inviter_id',
        'email',
        'role',
        'token',
        'status',
        'expires_at',
        'accepted_at',
        'declined_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
            'declined_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'status' => InvitationStatus::class,
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }

    public function isPending(): bool
    {
        return $this->status === InvitationStatus::Pending;
    }

    public function isAcceptable(): bool
    {
        return $this->isPending() && ! $this->isExpired();
    }

    public function markAsAccepted(): void
    {
        $this->update([
            'status' => InvitationStatus::Accepted,
            'accepted_at' => Carbon::now(),
        ]);
    }

    public function markAsDeclined(): void
    {
        $this->update([
            'status' => InvitationStatus::Declined,
            'declined_at' => Carbon::now(),
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => InvitationStatus::Cancelled,
            'cancelled_at' => Carbon::now(),
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update([
            'status' => InvitationStatus::Expired,
        ]);
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public static function defaultExpiry(): Carbon
    {
        return Carbon::now()->addDays(config('invitation.expiry_days', 7));
    }

    public function scopePending($query)
    {
        return $query->where('status', InvitationStatus::Pending);
    }

    public function scopeForEmail($query, string $email)
    {
        return $query->whereRaw('LOWER(email) = ?', [strtolower($email)]);
    }
}
