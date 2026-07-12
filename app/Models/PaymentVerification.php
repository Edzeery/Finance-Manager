<?php

namespace App\Models;

use App\Enums\PaymentVerificationStatus;
use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentVerification extends Model
{
    use BelongsToWorkspace;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }

    protected $fillable = [
        'payment_id', 'workspace_id', 'verified_by', 'status',
        'transaction_reference', 'admin_notes', 'receipt_path', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentVerificationStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
