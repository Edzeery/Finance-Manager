<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\WorkspaceInvitationService;

class SettingsController extends Controller
{
    use HasBreadcrumbs;

    public function __construct(
        private readonly WorkspaceInvitationService $invitationService,
    ) {}

    public function index()
    {
        $this->resetBreadcrumbs()->resourceBreadcrumbs('general.settings', 'settings.index', 'bi-gear-fill');

        $user = auth()->user();
        $workspace = $user->currentWorkspace;
        $subscription = $workspace?->owner()?->first()?->activeSubscription();
        $members = $workspace?->users()->get() ?? collect();
        $isOwner = $workspace && $user->isWorkspaceOwner($workspace);
        $nonAdminMembers = $isOwner ? $members->reject(fn ($m) => $m->workspaceRole($workspace) === 'workspace_admin') : collect();
        $roles = $workspace ? Role::where('level', 'workspace')->pluck('name', 'slug')->toArray() : [];
        $workspaceOwner = $workspace?->owner()->first();
        $userLimit = $workspace?->userLimit();
        $userCount = $workspace?->userCount();
        $pendingInvitations = $workspace ? $this->invitationService->getPendingForWorkspace($workspace) : collect();

        return view('settings.index', $this->withBreadcrumbs(compact(
            'workspace', 'subscription', 'members', 'isOwner', 'nonAdminMembers',
            'roles', 'workspaceOwner', 'userLimit', 'userCount', 'pendingInvitations',
        )));
    }
}
