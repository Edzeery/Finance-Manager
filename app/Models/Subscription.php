<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use BelongsToWorkspace, SoftDeletes, HasFactory;

    protected $fillable = [
        'workspace_id', 'user_id', 'subscription_plan_id', 'status',
        'starts_at', 'ends_at', 'trial_ends_at', 'grace_ends_at', 'canceled_at',
        'payment_method', 'payment_reference', 'auto_renew', 'billing_period',
        'plan_price_amount',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'grace_ends_at' => 'datetime',
            'canceled_at' => 'datetime',
            'auto_renew' => 'boolean',
            'status' => SubscriptionStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function isActive(): bool
    {
        if ($this->status === SubscriptionStatus::Trialing) {
            return !$this->isTrialExpired();
        }
        return $this->status === SubscriptionStatus::Active
            && (!$this->ends_at || $this->ends_at->isFuture() || $this->isOnGrace());
    }

    public function isTrialExpired(): bool
    {
        return $this->status === SubscriptionStatus::Trialing
            && $this->trial_ends_at
            && $this->trial_ends_at->isPast();
    }

    public function isExpired(): bool
    {
        if ($this->isTrialExpired()) return true;

        return $this->status === SubscriptionStatus::Expired
            || ($this->ends_at && $this->ends_at->isPast() && !$this->isOnGrace())
            || ($this->status === SubscriptionStatus::Canceled && !$this->isOnGrace());
    }

    public function daysRemaining(): int
    {
        if (!$this->ends_at) return 365;
        return max(0, now()->diffInDays($this->ends_at, false));
    }

    public function isOnGrace(): bool
    {
        return $this->grace_ends_at !== null && $this->grace_ends_at->isFuture();
    }

    public function graceDaysRemaining(): int
    {
        if (!$this->grace_ends_at) return 0;
        return max(0, now()->diffInDays($this->grace_ends_at, false));
    }

    public function enterGracePeriod(): void
    {
        if (!$this->isOnGrace()) {
            $days = config('finance.grace_period_days', 3);
            $this->update([
                'grace_ends_at' => now()->addDays($days),
            ]);
        }
    }
}
