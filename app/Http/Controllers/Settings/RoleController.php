<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    use HasBreadcrumbs;

    public function index()
    {
        return redirect()->route('settings.workspace.index', ['tab' => 'roles']);
    }

    public function show(Role $role)
    {
        abort_if($role->level !== 'workspace', 404);

        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('settings.title'), route('settings.workspace.index', ['tab' => 'roles']), 'bi-gear')
            ->addBreadcrumb(__('workspace.roles'), route('settings.workspace.index', ['tab' => 'roles']))
            ->addBreadcrumb($role->name);

        $permissions = Permission::whereIn('module', [
            'income', 'expense', 'debt', 'asset', 'budget', 'goal', 'zakat',
            'category', 'dashboard', 'report', 'transaction', 'export',
            'workspace-setting', 'workspace-user', 'workspace-role', 'billing',
            'activity-log', 'payment', 'invoice', 'notification',
        ])->get()->groupBy(fn ($perm) => explode('.', $perm->slug)[0] ?? $perm->slug);

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('settings.role-show', $this->withBreadcrumbs(compact('role', 'permissions', 'rolePermissions')));
    }
}
