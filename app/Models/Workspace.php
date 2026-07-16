<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Workspace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'type', 'description', 'currency',
        'timezone', 'is_active', 'trial_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Workspace $workspace) {
            if (empty($workspace->slug)) {
                $workspace->slug = Str::slug($workspace->name).'-'.Str::random(6);
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function owner(): BelongsToMany
    {
        $adminRoleId = Role::where('slug', 'workspace_admin')->value('id');

        return $this->belongsToMany(User::class, 'workspace_role_user', 'workspace_id', 'user_id')
            ->wherePivot('role_id', $adminRoleId);
    }

    public function allSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->latest();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function activePlan(): ?SubscriptionPlan
    {
        $sub = $this->owner()?->first()?->activeSubscription();
        if (! $sub || ! $sub->isActive()) {
            return null;
        }

        return $sub->plan;
    }

    public function userCount(): int
    {
        return $this->users()->count();
    }

    public function userLimit(): int
    {
        return $this->activePlan()?->max_users ?? 1;
    }

    public function canAddUser(): bool
    {
        return $this->userCount() < $this->userLimit();
    }

    public function isOwner(User $user): bool
    {
        return $this->owner()->where('user_id', $user->id)->exists();
    }

    public function userRole(User $user): ?string
    {
        $role = $user->workspaceRoleUsers()
            ->wherePivot('workspace_id', $this->id)
            ->first();

        return $role?->slug;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function createForUser(User $user, array $data = []): self
    {
        $workspace = static::create(array_merge([
            'name' => $data['name'] ?? $user->name."'s Workspace",
            'type' => $data['type'] ?? 'personal',
            'currency' => $user->currency ?? 'DZD',
            'timezone' => $user->timezone ?? 'Africa/Algiers',
        ], $data));

        $workspace->users()->attach($user->id, []);

        $adminRole = Role::where('slug', 'workspace_admin')->first();
        if ($adminRole) {
            $user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $workspace->id]);
        }

        return $workspace;
    }
}
