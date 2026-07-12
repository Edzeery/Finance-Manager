<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestChecklistItem extends Model
{
    protected $fillable = [
        'category',
        'item_key',
        'description',
        'status',
        'notes',
        'sort_order',
        'tested_by',
        'tested_at',
    ];

    protected function casts(): array
    {
        return [
            'tested_at' => 'datetime',
        ];
    }

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tested_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePassed($query)
    {
        return $query->where('status', 'passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSkipped($query)
    {
        return $query->where('status', 'skipped');
    }

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
