<?php

namespace App\Services;

use App\Contracts\Services\ActivityLogServiceInterface;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkspaceService
{
    private function resolveRoleSlug(string $role): string
    {
        if (str_starts_with($role, 'workspace_')) return $role;

        return match ($role) {
            'admin'            => 'workspace_admin',
            'deputy_admin'     => 'workspace_deputy_admin',
            'manager'          => 'workspace_finance_manager',
            'finance_manager'  => 'workspace_finance_manager',
            'assistant'        => 'workspace_accountant',
            'accountant'       => 'workspace_accountant',
            'editor'           => 'workspace_editor',
            'member'           => 'workspace_viewer',
            'viewer'           => 'workspace_viewer',
            default            => $role,
        };
    }

    public function syncWorkspaceRoleUser(Workspace $workspace, User $user, string $role): void
    {
        $slug = $this->resolveRoleSlug($role);
        $roleModel = Role::where('slug', $slug)->first();
        if (!$roleModel) return;

        $user->workspaceRoleUsers()
            ->wherePivot('workspace_id', $workspace->id)
            ->detach();

        $user->workspaceRoleUsers()->attach($roleModel->id, ['workspace_id' => $workspace->id]);
    }

    private function getCurrentRoleSlug(Workspace $workspace, User $user): string
    {
        return $user->workspaceRoleUsers()
            ->wherePivot('workspace_id', $workspace->id)
            ->first()?->slug ?? 'none';
    }

    public function createForUser(User $user, array $data = []): Workspace
    {
        if (!app(SubscriptionService::class)->canCreateWorkspace($user)) {
            throw new \RuntimeException(__('workspace.limit_reached'));
        }

        return DB::transaction(function () use ($user, $data) {
            $workspace = Workspace::create([
                'name' => $data['name'] ?? $user->name . "'s Workspace",
                'slug' => Str::slug($data['name'] ?? $user->name) . '-' . Str::random(6),
                'type' => $data['type'] ?? 'personal',
                'currency' => $data['currency'] ?? $user->currency ?? 'DZD',
                'timezone' => $data['timezone'] ?? $user->timezone ?? 'Africa/Algiers',
            ]);

            $workspace->users()->attach($user->id, []);
            $this->syncWorkspaceRoleUser($workspace, $user, 'workspace_admin');

            if (!$user->current_workspace_id) {
                $user->update(['current_workspace_id' => $workspace->id]);
            }

            $user->flushPermissionCache();

            return $workspace;
        });
    }

    public function inviteUser(Workspace $workspace, string $email, string $role = 'member'): ?User
    {
        if (!$workspace->canAddUser()) {
            return null;
        }

        $user = User::where('email', $email)->first();
        if (!$user) return null;

        if ($workspace->users()->where('user_id', $user->id)->exists()) {
            return null;
        }

        $workspace->users()->attach($user->id, []);
        $this->syncWorkspaceRoleUser($workspace, $user, $role);

        return $user;
    }

    public function removeUser(Workspace $workspace, User $user): bool
    {
        $isOwner = $workspace->owner()->where('user_id', $user->id)->exists();
        if ($isOwner && $workspace->owner()->count() <= 1) {
            return false;
        }
        $workspace->users()->detach($user->id);
        $user->workspaceRoleUsers()
            ->wherePivot('workspace_id', $workspace->id)
            ->detach();
        $user->flushPermissionCache();
        return true;
    }

    public function changeRole(Workspace $workspace, User $user, string $role): bool
    {
        $isOwner = $workspace->owner()->where('user_id', $user->id)->exists();
        if ($isOwner && $workspace->owner()->count() <= 1) {
            return false;
        }

        $oldRole = $this->getCurrentRoleSlug($workspace, $user);
        $this->syncWorkspaceRoleUser($workspace, $user, $role);
        $user->flushPermissionCache();

        app(ActivityLogServiceInterface::class)->log(
            auth()->id(), 'role_changed', $user,
            "Role changed in {$workspace->name}: {$oldRole} → {$role}",
            ['workspace_id' => $workspace->id, 'old_role' => $oldRole, 'new_role' => $role]
        );

        app(NotificationService::class)->create($user->id, 'role_changed', [
            'ar' => 'تم تغيير صلاحياتك',
            'fr' => 'Vos autorisations ont été modifiées',
            'en' => 'Your permissions have been changed',
        ], [
            'ar' => "تم تغيير دورك في {$workspace->name} إلى {$this->resolveRoleSlug($role)}",
            'fr' => "Votre rôle dans {$workspace->name} a été changé en {$this->resolveRoleSlug($role)}",
            'en' => "Your role in {$workspace->name} has been changed to {$this->resolveRoleSlug($role)}",
        ]);

        return true;
    }

    public function transferOwnership(Workspace $workspace, User $newOwner): bool
    {
        $currentOwner = $workspace->owner()->first();
        if (!$currentOwner || $currentOwner->id === $newOwner->id) return false;

        DB::transaction(function () use ($workspace, $currentOwner, $newOwner) {
            $this->syncWorkspaceRoleUser($workspace, $currentOwner, 'workspace_deputy_admin');
            $this->syncWorkspaceRoleUser($workspace, $newOwner, 'workspace_admin');
            $currentOwner->flushPermissionCache();
            $newOwner->flushPermissionCache();
        });

        return true;
    }
}
