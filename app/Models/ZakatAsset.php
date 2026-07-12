<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZakatAsset extends Model
{
    use BelongsToWorkspace;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }

    protected $fillable = [
        'zakat_record_id', 'workspace_id', 'asset_id', 'type', 'name',
        'value', 'is_zakatable', 'zakatable_value', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'zakatable_value' => 'decimal:2',
            'is_zakatable' => 'boolean',
        ];
    }

    public function record(): BelongsTo
    {
        return $this->belongsTo(ZakatRecord::class, 'zakat_record_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
