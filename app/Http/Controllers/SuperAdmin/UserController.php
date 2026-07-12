<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\Role;
use App\Models\User;
use App\Rules\PasswordRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use HasBreadcrumbs;

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.users'));

        $query = User::with('workspaces', 'roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('super_admin')) {
            $superAdminId = Role::where('slug', 'super_admin')->value('id');
            if ($request->super_admin === 'yes') {
                $query->whereHas('roles', fn($q) => $q->where('role_id', $superAdminId));
            } else {
                $query->whereDoesntHave('roles', fn($q) => $q->where('role_id', $superAdminId));
            }
        }

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
        $users = $query->latest()->paginate($perPage);

        return view('super-admin.users', $this->withBreadcrumbs(compact('users')));
    }

    public function create()
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.users'), route('super.admin.users.index'))
            ->addBreadcrumb(__('admin.add_user'));

        $roles = Role::platform()->get();

        return view('super-admin.user-create', $this->withBreadcrumbs(compact('roles')));
    }

    public function store(Request $request)
    {
        $platformRoleIds = Role::platform()->pluck('id')->toArray();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', new PasswordRule],
            'locale' => ['nullable', Rule::in(['ar', 'fr', 'en'])],
            'theme' => ['nullable', Rule::in(['light', 'dark'])],
            'currency' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id', Rule::in($platformRoleIds)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'locale' => $validated['locale'] ?? config('app.locale'),
            'theme' => $validated['theme'] ?? 'light',
            'currency' => $validated['currency'] ?? 'DZD',
            'timezone' => $validated['timezone'] ?? 'Africa/Algiers',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()->route('super.admin.users.index')
            ->with('success', __('messages.user_created'));
    }

    public function edit(User $user)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.users'), route('super.admin.users.index'))
            ->addBreadcrumb($user->name);
        $roles = Role::platform()->get();

        $userRoles = $user->platformRoles->pluck('id')->toArray();

        return view('super-admin.user-edit', $this->withBreadcrumbs(compact('user', 'roles', 'userRoles')));
    }

    public function update(Request $request, User $user)
    {
        $platformRoleIds = Role::platform()->pluck('id')->toArray();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'string', new PasswordRule],
            'locale' => ['nullable', Rule::in(['ar', 'fr', 'en'])],
            'theme' => ['nullable', Rule::in(['light', 'dark'])],
            'currency' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id', Rule::in($platformRoleIds)],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'locale' => $validated['locale'] ?? $user->locale,
            'theme' => $validated['theme'] ?? $user->theme,
            'currency' => $validated['currency'] ?? $user->currency,
            'timezone' => $validated['timezone'] ?? $user->timezone,
            'is_active' => $validated['is_active'] ?? $user->is_active,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user->update($data);

        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()->route('super.admin.users.index')
            ->with('success', __('messages.user_updated'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('super.admin.users.index')
                ->with('error', __('messages.cannot_delete_self'));
        }

        if ($user->hasRole('super_admin') && User::whereHas('roles', fn($q) => $q->where('slug', 'super_admin'))->count() <= 1) {
            return redirect()->route('super.admin.users.index')
                ->with('error', __('messages.cannot_delete_last_super_admin'));
        }

        $user->delete();

        return redirect()->route('super.admin.users.index')
            ->with('success', __('messages.user_deleted'));
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('super.admin.users.index')
                ->with('error', __('messages.cannot_disable_self'));
        }

        if ($user->hasRole('super_admin') && User::whereHas('roles', fn($q) => $q->where('slug', 'super_admin'))->count() <= 1) {
            return redirect()->route('super.admin.users.index')
                ->with('error', __('messages.cannot_disable_last_super_admin'));
        }

        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('super.admin.users.index')
            ->with('success', __('messages.user_status_updated'));
    }
}
