<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Models\Subscription;
use App\Notifications\VerifyEmail as CustomVerifyEmail;
use App\Services\OnboardingService;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'locale', 'theme', 'currency', 'timezone', 'is_active', 'current_workspace_id', 'onboarding_completed_at', 'plan_confirmed_at', 'pending_plan_id'])]
#[Hidden(['password', 'remember_token', 'google2fa_secret', 'two_factor_recovery_codes', 'two_factor_email_code'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'onboarding_completed_at' => 'datetime',
            'plan_confirmed_at' => 'datetime',
            'google2fa_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_methods' => 'array',
            'two_factor_email_code' => 'encrypted',
            'two_factor_email_code_at' => 'datetime',
        ];
    }

    public function hasTwoFactorEnabled(): bool
    {
        return ! is_null($this->two_factor_confirmed_at);
    }

    public function hasTwoFactorMethod(string $method): bool
    {
        return $this->hasTwoFactorEnabled() && in_array($method, $this->two_factor_methods ?? []);
    }

    public function getGoogle2faSecret(): ?string
    {
        try {
            return $this->google2fa_secret;
        } catch (\Exception $e) {
            return null;
        }
    }

    // ---- Onboarding ----

    public function pendingPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'pending_plan_id');
    }

    public function hasCompletedOnboarding(): bool
    {
        return !is_null($this->onboarding_completed_at);
    }

    public function hasConfirmedPlan(): bool
    {
        return !is_null($this->plan_confirmed_at);
    }

    public function markOnboardingComplete(): void
    {
        $this->update(['onboarding_completed_at' => now()]);
    }

    public function markPlanConfirmed(): void
    {
        $this->update([
            'plan_confirmed_at' => now(),
            'pending_plan_id' => null,
        ]);
    }

    public function hasActivePaidAccess(): bool
    {
        $subscription = $this->activeSubscription();
        return $subscription && $subscription->isActive() && $subscription->plan && !$subscription->plan->is_free;
    }

    /**
     * Get the active subscription for this user.
     *
     * Conceptual note: This method checks the subscription belonging to THIS user instance.
     * It does NOT automatically resolve the workspace owner's subscription.
     *
     * When caller is in a workspace context (middleware, features tied to workspace access),
     * the caller must first try the workspace owner's subscription:
     *   $workspace?->owner()?->first()?->activeSubscription()
     *
     * This ensures that team members working inside another's workspace are governed
     * by the workspace owner's plan, not their own (potentially free/limited) plan.
     */
    public function activeSubscription(): ?Subscription
    {
        $subscription = Subscription::withoutWorkspace()
            ->where('user_id', $this->id)
            ->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Trialing->value])
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->latest()
            ->first();

        if ($subscription) {
            return $subscription;
        }

        $subscription = Subscription::withoutWorkspace()
            ->where('user_id', $this->id)
            ->where('status', SubscriptionStatus::Canceled->value)
            ->where('grace_ends_at', '>', now())
            ->latest()
            ->first();

        if ($subscription) {
            return $subscription;
        }

        $hasExpiredOrCanceled = Subscription::withoutWorkspace()
            ->where('user_id', $this->id)
            ->whereIn('status', [SubscriptionStatus::Expired->value, SubscriptionStatus::Canceled->value])
            ->where(function ($q) {
                $q->whereNull('grace_ends_at')
                    ->orWhere('grace_ends_at', '<', now());
            })
            ->exists();

        if (!$hasExpiredOrCanceled) {
            return Subscription::withoutWorkspace()
                ->where('user_id', $this->id)
                ->where('status', SubscriptionStatus::Active->value)
                ->whereNull('ends_at')
                ->latest()
                ->first();
        }

        return null;
    }

    public function pendingSubscription(): ?Subscription
    {

        $subscription = Subscription::withoutWorkspace()
            ->where('user_id', $this->id)
            ->where('status', PaymentStatus::CheckoutPending->value)
            ->latest()
            ->first();

        if ($subscription) {
            return $subscription;
        }

        $subscription = Subscription::withoutWorkspace()
            ->where('user_id', $this->id)
            ->where('status', SubscriptionStatus::Canceled->value)
            ->where('grace_ends_at', '>', now())
            ->latest()
            ->first();

        if ($subscription) {
            return $subscription;
        }

        $hasExpiredOrCanceled = Subscription::withoutWorkspace()
            ->where('user_id', $this->id)
            ->whereIn('status', [SubscriptionStatus::Expired->value, SubscriptionStatus::Canceled->value])
            ->where(function ($q) {
                $q->whereNull('grace_ends_at')
                    ->orWhere('grace_ends_at', '<', now());
            })
            ->exists();

        if (!$hasExpiredOrCanceled) {
            return Subscription::withoutWorkspace()
                ->where('user_id', $this->id)
                ->where('status', SubscriptionStatus::Active->value)
                ->whereNull('ends_at')
                ->latest()
                ->first();
        }

        return null;
    }

    public function hasPendingManualPayment(): bool
    {
        return Payment::where('user_id', $this->id)
            ->where('status', PaymentStatus::CheckoutPending->value)
            ->whereIn('method', OnboardingService::manualMethods())
            ->whereHas('verification', fn($q) => $q->where('status', \App\Enums\PaymentVerificationStatus::Pending->value))
            ->exists();
    }

    // ---- Platform-level roles ----

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function platformRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->where('level', 'platform');
    }

    public function hasRole(string|array $roles): bool
    {
        $slugs = $this->roles()->pluck('slug')->toArray();
        foreach ((array) $roles as $role) {
            if (in_array($role, $slugs)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermission(string $slug, string $context = 'any'): bool
    {
        return match ($context) {
            'platform' => $this->hasPlatformPermission($slug),
            'workspace' => $this->workspaceHasPermission($slug),
            default => $this->hasPlatformPermission($slug) || $this->workspaceHasPermission($slug),
        };
    }

    public function hasPlatformPermission(string $slug): bool
    {
        return in_array($slug, $this->cachedPlatformPermissions());
    }

    public function cachedPlatformPermissions(): array
    {
        $version = static::permissionCacheVersion();

        return cache()->remember("user.{$this->id}.permissions.v{$version}", 3600, function () {
            return $this->platformRoles()
                ->with('permissions')
                ->get()
                ->flatMap(fn($role) => $role->permissions->pluck('slug'))
                ->unique()
                ->values()
                ->toArray();
        });
    }

    public function flushPermissionCache(): void
    {
        $version = static::permissionCacheVersion();
        cache()->forget("user.{$this->id}.permissions.v{$version}");
    }

    public static function flushAllPermissionCaches(): void
    {
        cache()->increment('user_permissions_version');
    }

    public static function permissionCacheVersion(): int
    {
        return cache()->rememberForever('user_permissions_version', fn() => 1);
    }

    protected static function booted(): void {}

    public function workspaceHasPermission(string $slug): bool
    {
        $role = $this->currentWorkspaceRole();

        if (!$role) {
            return false;
        }

        return $role->hasPermission($slug);
    }

    public function currentWorkspaceRole(): ?Role
    {
        $currentWs = $this->currentWorkspace;

        if (!$currentWs) {
            return null;
        }

        return $this->workspaceRoleUsers()
            ->with('permissions')
            ->wherePivot('workspace_id', $currentWs->id)
            ->first();
    }

    // ---- Workspace relationships ----

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class)
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function workspaceRoleUsers(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'workspace_role_user')
            ->withPivot('workspace_id')
            ->withTimestamps();
    }

    public function currentWorkspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'current_workspace_id');
    }

    public function ownedWorkspaces()
    {
        $adminRoleId = Role::where('slug', 'workspace_admin')->value('id');
        $workspaceIds = $this->workspaceRoleUsers()
            ->wherePivot('workspace_id', '!=', 0)
            ->wherePivot('role_id', $adminRoleId)
            ->get()
            ->pluck('pivot.workspace_id');

        return $this->workspaces()->whereIn('workspaces.id', $workspaceIds);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function switchWorkspace(Workspace $workspace): void
    {
        $this->update(['current_workspace_id' => $workspace->id]);
    }

    public function workspaceRole(?Workspace $workspace = null): ?string
    {
        $ws = $workspace ?? $this->currentWorkspace;

        if (!$ws) {
            return null;
        }

        $pivotRole = $this->workspaceRoleUsers()
            ->wherePivot('workspace_id', $ws->id)
            ->first();

        return $pivotRole?->slug;
    }

    public function hasWorkspaceRole(string|array $roles, ?Workspace $workspace = null): bool
    {
        $role = $this->workspaceRole($workspace);
        if (!$role) return false;
        return in_array($role, (array) $roles);
    }

    public function isWorkspaceOwner(?Workspace $workspace = null): bool
    {
        return $this->hasWorkspaceRole('workspace_admin', $workspace);
    }

    public function isWorkspaceAdmin(?Workspace $workspace = null): bool
    {
        return $this->hasWorkspaceRole(['workspace_admin', 'workspace_deputy_admin'], $workspace);
    }

    public function ownedByCurrentUser(): bool
    {
        return $this->id === auth()->id();
    }

    // ---- Legacy direct user relationships ----

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
    public function financialGoals(): HasMany
    {
        return $this->hasMany(FinancialGoal::class);
    }
    public function zakatRecords(): HasMany
    {
        return $this->hasMany(ZakatRecord::class);
    }
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
    public function settings(): HasMany
    {
        return $this->hasMany(UserSetting::class);
    }
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function totalIncome($start = null, $end = null): float
    {
        return (float) $this->incomes()
            ->when($start, fn($q) => $q->whereDate('date', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('date', '<=', $end))
            ->sum('amount');
    }

    public function totalExpense($start = null, $end = null): float
    {
        return (float) $this->expenses()
            ->when($start, fn($q) => $q->whereDate('date', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('date', '<=', $end))
            ->sum('amount');
    }

    public function netBalance($start = null, $end = null): float
    {
        return $this->totalIncome($start, $end) - $this->totalExpense($start, $end);
    }

    public function totalAssetsValue(): float
    {
        return (float) $this->assets()->sum('total_value');
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify((new CustomVerifyEmail)->locale($this->locale));
    }
}
