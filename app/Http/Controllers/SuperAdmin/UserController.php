<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\UserStatus;
use App\Enums\OnlineStatus;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Notifications\UserStatusChanged;
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

        $base = User::withoutTrashed()->where('id', '!=', auth()->id());

        $countAll = User::withoutTrashed()->where('id', '!=', auth()->id())->count();
        $countActive = (clone $base)->whereHas('statusRecord', fn ($q) => $q->where('status', 'active'))->count();
        $countInactive = (clone $base)->whereHas('statusRecord', fn ($q) => $q->where('status', 'inactive'))->count();
        $countSuspended = (clone $base)->whereHas('statusRecord', fn ($q) => $q->where('status', 'suspended'))->count();
        $countBanned = (clone $base)->whereHas('statusRecord', fn ($q) => $q->where('status', 'banned'))->count();
        $countTrashed = User::onlyTrashed()->where('id', '!=', auth()->id())->count();
        $countOnline = \App\Models\UserStatus::where('online_status', OnlineStatus::Online)
            ->where('last_activity_at', '>=', now()->subMinutes(15))
            ->count();

        $query = User::with('workspaces', 'roles', 'statusRecord')->where('id', '!=', auth()->id());

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'trashed') {
                $query->onlyTrashed();
            } elseif ($request->status === 'online') {
                $query->withoutTrashed();
                $query->whereHas('statusRecord', fn ($q) => $q->where('online_status', OnlineStatus::Online)
                    ->where('last_activity_at', '>=', now()->subMinutes(15)));
            } else {
                $query->withoutTrashed();
                $query->whereHas('statusRecord', fn ($q) => $q->where('status', $request->status));
            }
        } else {
            $query->withoutTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('super_admin')) {
            $superAdminId = Role::where('slug', 'super_admin')->value('id');
            if ($request->super_admin === 'yes') {
                $query->whereHas('roles', fn ($q) => $q->where('role_id', $superAdminId));
            } else {
                $query->whereDoesntHave('roles', fn ($q) => $q->where('role_id', $superAdminId));
            }
        }

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
        $users = $query->latest()->paginate($perPage);

        return view('super-admin.users', $this->withBreadcrumbs(compact(
            'users', 'countAll', 'countActive', 'countInactive', 'countSuspended', 'countBanned', 'countTrashed', 'countOnline'
        )));
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
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive', 'pending', 'suspended', 'banned'])],
            'status_reason' => ['nullable', 'string', 'max:500'],
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
        ]);

        $targetStatus = $validated['status'] ?? 'active';

        if ($targetStatus !== 'active' || ! empty($validated['status_reason'])) {
            $user->statusRecord->changeStatus(
                UserStatus::from($targetStatus),
                $validated['status_reason'] ?? null,
                auth()->id(),
            );
        }

        if (! empty($validated['roles'])) {
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
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive', 'pending', 'suspended', 'banned'])],
            'status_reason' => ['nullable', 'string', 'max:500'],
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
        ];

        if (! empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user->update($data);

        if (isset($validated['status'])) {
            $oldStatus = $user->status;
            $newStatus = UserStatus::from($validated['status']);

            if ($oldStatus !== $newStatus) {
                $user->statusRecord->changeStatus(
                    $newStatus,
                    $validated['status_reason'] ?? null,
                    auth()->id(),
                );

                $user->notify(new UserStatusChanged($oldStatus, $newStatus, $validated['status_reason'] ?? null));
            } elseif (! empty($validated['status_reason'])) {
                $user->statusRecord->update(['status_reason' => $validated['status_reason']]);
            }
        }

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

        if ($user->hasRole('super_admin') && User::whereHas('roles', fn ($q) => $q->where('slug', 'super_admin'))->count() <= 1) {
            return redirect()->route('super.admin.users.index')
                ->with('error', __('messages.cannot_delete_last_super_admin'));
        }

        $user->delete();

        return redirect()->route('super.admin.users.index')
            ->with('success', __('messages.user_deleted'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('super.admin.users.index', ['status' => 'trashed'])
            ->with('success', __('messages.user_restored'));
    }

    public function forceDestroy($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()->route('super.admin.users.index', ['status' => 'trashed'])
            ->with('success', __('messages.user_force_deleted'));
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'string', 'regex:/^[\d,]+$/'],
        ]);

        $ids = array_map('intval', explode(',', $validated['user_ids']));
        $ids = array_filter($ids, fn ($id) => $id !== auth()->id());

        if (empty($ids)) {
            return redirect()->route('super.admin.users.index')
                ->with('error', __('messages.no_users_selected'));
        }

        $count = User::whereIn('id', $ids)->count();
        User::whereIn('id', $ids)->delete();

        return redirect()->route('super.admin.users.index')
            ->with('success', __('messages.bulk_deleted', ['count' => $count]));
    }

    public function bulkRestore(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'string', 'regex:/^[\d,]+$/'],
        ]);

        $ids = array_map('intval', explode(',', $validated['user_ids']));

        if (empty($ids)) {
            return redirect()->route('super.admin.users.index', ['status' => 'trashed'])
                ->with('error', __('messages.no_users_selected'));
        }

        $count = User::onlyTrashed()->whereIn('id', $ids)->count();
        User::onlyTrashed()->whereIn('id', $ids)->restore();

        return redirect()->route('super.admin.users.index', ['status' => 'trashed'])
            ->with('success', __('messages.bulk_restored', ['count' => $count]));
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('super.admin.users.index')
                ->with('error', __('messages.cannot_disable_self'));
        }

        if ($user->hasRole('super_admin') && User::whereHas('roles', fn ($q) => $q->where('slug', 'super_admin'))->count() <= 1) {
            return redirect()->route('super.admin.users.index')
                ->with('error', __('messages.cannot_disable_last_super_admin'));
        }

        $oldStatus = $user->status;
        $newStatus = $oldStatus === UserStatus::Active ? UserStatus::Inactive : UserStatus::Active;

        $user->statusRecord->changeStatus($newStatus, null, auth()->id());
        $user->notify(new UserStatusChanged($oldStatus, $newStatus));

        return redirect()->route('super.admin.users.index')
            ->with('success', __('messages.user_status_updated'));
    }

    public function setStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['active', 'inactive', 'pending', 'suspended', 'banned'])],
        ]);

        $oldStatus = $user->status;
        $newStatus = UserStatus::from($validated['status']);

        if ($oldStatus !== $newStatus) {
            $user->statusRecord->changeStatus($newStatus, null, auth()->id());
            $user->notify(new UserStatusChanged($oldStatus, $newStatus));
        }

        return redirect()->route('super.admin.users.index')
            ->with('success', __('messages.user_status_updated'));
    }
}
