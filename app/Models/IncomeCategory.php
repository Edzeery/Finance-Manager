<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomeCategory extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected bool $allowsNullWorkspace = true;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }
    protected $fillable = ['user_id', 'workspace_id', 'name_ar', 'name_fr', 'name_en', 'icon', 'color', 'type', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'category_id');
    }
}
