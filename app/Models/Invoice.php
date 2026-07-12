<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use BelongsToWorkspace, SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }

    protected $fillable = [
        'workspace_id', 'subscription_id', 'user_id', 'coupon_id', 'payment_id',
        'number', 'status', 'subtotal', 'tax', 'discount',
        'gateway_fee', 'tax_added', 'tax_disclosed', 'proration_credit',
        'total',
        'currency', 'billing_period', 'period_start', 'period_end',
        'paid_at', 'due_at',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'gateway_fee' => 'decimal:2',
            'tax_added' => 'decimal:2',
            'tax_disclosed' => 'decimal:2',
            'proration_credit' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => InvoiceStatus::class,
            'period_start' => 'date',
            'period_end' => 'date',
            'paid_at' => 'datetime',
            'due_at' => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', InvoiceStatus::Draft);
    }

    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::Paid);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', InvoiceStatus::Overdue);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', InvoiceStatus::Cancelled);
    }

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::Paid;
    }

    public function isOverdue(): bool
    {
        return $this->status === InvoiceStatus::Overdue;
    }
}
