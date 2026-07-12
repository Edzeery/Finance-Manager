<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'guard_name', 'level', 'is_system', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn() => User::flushAllPermissionCaches());
        static::deleted(fn() => User::flushAllPermissionCaches());
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function scopePlatform($query)
    {
        return $query->where('level', 'platform');
    }

    public function scopeWorkspace($query)
    {
        return $query->where('level', 'workspace');
    }

    public function isSystem(): bool
    {
        return $this->is_system;
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains('slug', $slug);
        }

        return $this->permissions()->where('slug', $slug)->exists();
    }
}
