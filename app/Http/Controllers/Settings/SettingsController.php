<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\WorkspaceInvitationService;

class SettingsController extends Controller
{
    use HasBreadcrumbs;

    private const TABS = ['general', 'team', 'roles', 'integrations'];

    public function __construct(
        private readonly WorkspaceInvitationService $invitationService,
    ) {}

    public function index()
    {
        $this->resetBreadcrumbs()->resourceBreadcrumbs('general.settings', 'settings.workspace.index', 'bi-gear-fill');

        $user = auth()->user();
        $workspace = $user->currentWorkspace;
        $workspaceOwner = $workspace?->owner()->first();

        $tab = $this->resolveTab();

        $data = match ($tab) {
            'team' => $this->teamData($user, $workspace),
            'roles' => $this->rolesData($workspace),
            default => [],
        };

        return view('settings.index', $this->withBreadcrumbs(array_merge(
            compact('workspace', 'workspaceOwner', 'tab'),
            $data,
        )));
    }

    private function resolveTab(): string
    {
        $tab = request()->query('tab', 'general');

        return in_array($tab, self::TABS) ? $tab : 'general';
    }

    private function teamData($user, $workspace): array
    {
        $members = $workspace?->users()->get() ?? collect();
        $isOwner = $workspace && $user->isWorkspaceOwner($workspace);
        $nonAdminMembers = $isOwner ? $members->reject(fn ($m) => $m->workspaceRole($workspace) === 'workspace_admin') : collect();
        $roles = $workspace ? Role::where('level', 'workspace')->pluck('name', 'slug')->toArray() : [];
        $userLimit = $workspace?->userLimit();
        $userCount = $workspace?->userCount();
        $pendingInvitations = $workspace ? $this->invitationService->getPendingForWorkspace($workspace) : collect();

        return compact('user', 'members', 'isOwner', 'nonAdminMembers', 'roles', 'userLimit', 'userCount', 'pendingInvitations');
    }

    private function rolesData($workspace): array
    {
        $roles = $workspace
            ? Role::workspace()->with('permissions')->withCount('users')->orderBy('sort_order')->get()
            : collect();

        return compact('roles');
    }
}
