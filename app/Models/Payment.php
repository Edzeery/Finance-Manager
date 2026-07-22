<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
    use BelongsToWorkspace, HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'workspace_id', 'subscription_id', 'user_id', 'coupon_id','method_id',
        'refunded_by', 'refunded_at', 'refund_reason', 'refund_amount',
        'amount', 'currency', 'status',
        'original_amount', 'discount_amount',
        'gateway_fee', 'tax_added', 'tax_disclosed',
        'reference', 'transaction_id', 'chargily_checkout_id',
        'gateway_reference', 'gateway_payload', 'webhook_payload',
        'payment_method_type',
        'notes', 'metadata', 'paid_at', 'failed_at', 'canceled_at',
        'webhook_processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'original_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'gateway_fee' => 'decimal:2',
            'tax_added' => 'decimal:2',
            'tax_disclosed' => 'decimal:2',
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'canceled_at' => 'datetime',
            'refunded_at' => 'datetime',
            'refund_amount' => 'decimal:2',
            'webhook_processed_at' => 'datetime',
            'metadata' => 'json',
            'gateway_payload' => 'json',
            'webhook_payload' => 'json',
        ];
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);

        static::creating(function (Payment $payment) {
            if (! $payment->uuid) {
                $payment->uuid = 'pay-'.Str::lower(Str::random(12));
            }
        });

        static::updating(function (Payment $payment) {
            if ($payment->isDirty('status') && $payment->isPaid()) {
                Payment::withoutWorkspace()
                    ->where('workspace_id', $payment->workspace_id)
                    ->where('id', '!=', $payment->id)
                    ->where('status', PaymentStatus::CheckoutPending->value)
                    ->update(['status' => PaymentStatus::CheckoutCanceled->value, 'canceled_at' => now()]);
            }
        });
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verification(): HasOne
    {
        return $this->hasOne(PaymentVerification::class);
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::CheckoutPending;
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::CheckoutPaid;
    }

    public function isCompleted(): bool
    {
        return $this->isPaid();
    }

    public function isFailed(): bool
    {
        return in_array($this->status, [PaymentStatus::CheckoutFailed, PaymentStatus::CheckoutCanceled]);
    }

    public function getContinueUrl(): ?string
    {
        if ($this->gateway_payload) {
            $url = $this->gateway_payload['checkout_url'] ?? $this->gateway_payload['url'] ?? null;
            if ($url) {
                return $url;
            }
        }

        $checkoutId = $this->transaction_id ?? $this->chargily_checkout_id ?? null;

        if (($this->paymentMethod?->key ?? null) === 'chargily' && $checkoutId) {
            $method = $this->paymentMethod ?? PaymentMethod::where('key', 'chargily')->first();
            $mode = $method?->credential('mode', 'test') ?? 'test';
            $prefix = $mode === 'live' ? '' : 'test/';

            return "https://pay.chargily.dz/{$prefix}checkouts/{$checkoutId}/pay";
        }

        return null;
    }


    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::CheckoutPending->value);
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->whereHas('paymentMethod', fn ($q) => $q->where('key', $method));
    }

    public function scopeByStatus($query, PaymentStatus $status)
    {
        return $query->where('status', $status->value);
    }

    public function scopeTerminal($query)
    {
        return $query->whereIn('status', array_map(fn (PaymentStatus $s) => $s->value, array_filter(
            PaymentStatus::cases(),
            fn (PaymentStatus $s) => $s->isTerminal()
        )));
    }

    public function refundedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    public function isRefunded(): bool
    {
        return $this->refunded_at !== null;
    }

    public function isRefundable(): bool
    {
        return $this->isPaid() && ! $this->isRefunded();
    }

    public function scopeRefunded($query)
    {
        return $query->whereNotNull('refunded_at');
    }

    public function scopeNotRefunded($query)
    {
        return $query->whereNull('refunded_at');
    }
}
