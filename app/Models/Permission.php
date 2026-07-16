<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'guard_name', 'module'];

    protected static function booted(): void
    {
        static::saved(fn () => User::flushAllPermissionCaches());
        static::deleted(fn () => User::flushAllPermissionCaches());
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function scopeModule($query, string $module)
    {
        return $query->where('module', $module);
    }
}
