<?php

namespace App\Http\Controllers\Admin;

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
        $this->resetBreadcrumbs()->resourceBreadcrumbs('admin.title', 'admin.users.index', 'bi-people');

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('slug', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
        $users = $query->latest()->paginate($perPage);

        $roles = Role::all();

        return view('admin.users.index', $this->withBreadcrumbs(compact('users', 'roles')));
    }

    public function create()
    {
        $this->resetBreadcrumbs()
            ->resourceBreadcrumbs('admin.title', 'admin.users.index', 'bi-people')
            ->addBreadcrumb(__('admin.add_user'));

        $roles = Role::all();

        return view('admin.users.create', $this->withBreadcrumbs(compact('roles')));
    }

    public function store(Request $request)
    {
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
            'roles.*' => ['exists:roles,id'],
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

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.user_created'));
    }

    public function edit(User $user)
    {
        $this->resetBreadcrumbs()
            ->resourceBreadcrumbs('admin.title', 'admin.users.index', 'bi-people')
            ->addBreadcrumb(__('admin.edit_user'));

        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('admin.users.edit', $this->withBreadcrumbs(compact('user', 'roles', 'userRoles')));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'string', new PasswordRule],
            'locale' => ['nullable', Rule::in(['ar', 'fr', 'en'])],
            'theme' => ['nullable', Rule::in(['light', 'dark'])],
            'currency' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
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

        return redirect()->route('admin.users.edit', $user)
            ->with('success', __('messages.user_updated'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.user_deleted'));
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.user_status_updated'));
    }
}
