<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;
use App\Services\SubscriptionService;

class WorkspacePolicy
{
    public function view(User $user, Workspace $workspace): bool
    {
        return $workspace->users()->where('user_id', $user->id)->exists()
            || $user->hasPermission('workspace.view', 'platform');
    }

    public function create(User $user): bool
    {
        return app(SubscriptionService::class)->canCreateWorkspace($user);
    }

    public function update(User $user, Workspace $workspace): bool
    {
        return $workspace->isOwner($user)
            || $user->hasPermission('workspace-setting.update', 'workspace');
    }

    public function delete(User $user, Workspace $workspace): bool
    {
        return $workspace->isOwner($user)
            || $user->hasPermission('workspace.delete', 'platform');
    }

    public function manageMembers(User $user, Workspace $workspace): bool
    {
        return $workspace->isOwner($user)
            || $user->hasPermission('workspace-user.remove', 'workspace');
    }

    public function manageRoles(User $user, Workspace $workspace): bool
    {
        return $workspace->isOwner($user)
            || $user->hasPermission('workspace-user.role', 'workspace');
    }

    public function transfer(User $user, Workspace $workspace): bool
    {
        return $workspace->isOwner($user)
            || $user->hasPermission('workspace.transfer', 'platform');
    }
}
