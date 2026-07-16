<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use HasBreadcrumbs;

    private const PLATFORM_MODULES = [
        'tenant', 'platform-user', 'platform-role', 'subscription', 'plan',
        'platform-setting', 'backup', 'audit', 'ticket', 'monitor', 'api', 'system',
        'platform-dashboard', 'coupon',
    ];

    private const WORKSPACE_MODULES = [
        'income', 'expense', 'debt', 'asset', 'budget', 'goal', 'zakat',
        'category', 'dashboard', 'report', 'transaction', 'export',
        'workspace-setting', 'workspace-user', 'workspace-role', 'billing',
        'activity-log',
    ];

    private const SHARED_MODULES = ['payment', 'invoice', 'notification'];

    // ---- Platform Roles ----

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.platform_roles'));

        $roles = Role::platform()->with('permissions')->withCount('users')->get();

        return view('super-admin.roles', $this->withBreadcrumbs(compact('roles')));
    }

    public function edit(Role $role)
    {
        abort_if($role->level !== 'platform', 404);

        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.platform_roles'), route('super.admin.roles.index'))
            ->addBreadcrumb($role->name);

        $permissions = Permission::whereIn('module', array_merge(self::PLATFORM_MODULES, self::SHARED_MODULES))
            ->get()
            ->groupBy(fn ($perm) => explode('.', $perm->slug)[0] ?? $perm->slug);

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('super-admin.role-edit', $this->withBreadcrumbs(compact('role', 'permissions', 'rolePermissions')));
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->level !== 'platform', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('super.admin.roles.index')
            ->with('success', __('messages.role_updated'));
    }

    // ---- Workspace Roles ----

    public function workspaceIndex(Request $request)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.workspace_roles'));

        $roles = Role::workspace()->with('permissions')->withCount('users')->get();

        return view('super-admin.workspace-roles', $this->withBreadcrumbs(compact('roles')));
    }

    public function workspaceEdit(Role $role)
    {
        abort_if($role->level !== 'workspace', 404);

        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.workspace_roles'), route('super.admin.workspace-roles.index'))
            ->addBreadcrumb($role->name);

        $permissions = Permission::whereIn('module', array_merge(self::WORKSPACE_MODULES, self::SHARED_MODULES, ['general']))
            ->get()
            ->groupBy(fn ($perm) => explode('.', $perm->slug)[0] ?? $perm->slug);

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('super-admin.workspace-role-edit', $this->withBreadcrumbs(compact('role', 'permissions', 'rolePermissions')));
    }

    public function workspaceUpdate(Request $request, Role $role)
    {
        abort_if($role->level !== 'workspace', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('super.admin.workspace-roles.index')
            ->with('success', __('messages.role_updated'));
    }
}
