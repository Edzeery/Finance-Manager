<?php

namespace App\Models;

use App\Enums\AssetType;
use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes, BelongsToWorkspace;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }

    protected $fillable = [
        'user_id', 'workspace_id', 'type', 'name', 'description', 'quantity', 'unit_price',
        'total_value', 'currency', 'bank_name', 'account_number', 'is_liquid', 'is_zakatable', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'total_value' => 'decimal:2',
            'type' => AssetType::class,
            'is_liquid' => 'boolean',
            'is_zakatable' => 'boolean',
            'account_number' => 'encrypted',
            'bank_name' => 'encrypted',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function zakatAssets(): HasMany
    {
        return $this->hasMany(ZakatAsset::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeLiquid($query)
    {
        return $query->where('is_liquid', true);
    }

    public function scopeZakatable($query)
    {
        return $query->where('is_zakatable', true);
    }
}
