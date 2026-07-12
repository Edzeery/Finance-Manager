<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.users') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.users') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.users_desc') }}</x-slot>

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.users.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('general.search') }}..." value="{{ request('search') }}" min-width="200px" />
                    <x-select-filter name="status" :options="[
                        'active' => __('general.active'),
                        'inactive' => __('general.inactive'),
                    ]" placeholder="{{ __('general.all_status') }}" min-width="110px" />
                    <x-select-filter name="super_admin" :options="[
                        'yes' => __('super-admin.super_admin'),
                        'no' => __('super-admin.users'),
                    ]" placeholder="{{ __('super-admin.super_admin_status') }}" min-width="130px" />
                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
                    <x-clear-filters :filters="['search','status','super_admin']" :route="route('super.admin.users.index')" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <x-per-page :current="(int) request('per_page', 15)" :route="route('super.admin.users.index')" :preserve="['search','status','super_admin']" :options="[10, 15, 25, 50]" />
            </div>
        </div>

        <div class="data-grid-body">
            @if($users->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-checkbox"><input type="checkbox" class="select-all" style="accent-color:var(--accent)"></th>
                            <th>{{ __('general.name') }}</th>
                            <th>{{ __('general.email') }}</th>
                            <th>{{ __('super-admin.workspaces') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th>{{ __('super-admin.super_admin') }}</th>
                            <th>{{ __('super-admin.roles') }}</th>
                            <th>{{ __('general.member_since') }}</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="col-checkbox"><input type="checkbox" class="select-item" value="{{ $user->id }}" style="accent-color:var(--accent)"></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:30px;height:30px;border-radius:50%;background:var(--accent);color:#0F172A;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span style="font-weight:500">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="cell-muted">{{ $user->email }}</td>
                                <td><span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:2px 10px;border-radius:6px">{{ $user->workspaces->count() }}</span></td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.active') }}</span>
                                    @else
                                        <span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->hasRole('super_admin'))
                                        <span class="badge" style="font-size:10px;background:var(--sa-indigo-light);color:var(--sa-indigo);padding:3px 10px;border-radius:6px;font-weight:600">
                                            <i class="bi bi-shield-fill-check me-1"></i>{{ __('general.yes') }}
                                        </span>
                                    @else
                                        <span class="cell-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($user->roles->take(2) as $role)
                                            <span class="badge" style="font-size:10px;background:var(--sa-indigo-light);color:var(--sa-indigo);padding:2px 8px;border-radius:4px;font-weight:500">{{ $role->name }}</span>
                                        @empty
                                            <span class="cell-muted">—</span>
                                        @endforelse
                                        @if($user->roles->count() > 2)
                                            <span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:2px 8px;border-radius:4px">+{{ $user->roles->count() - 2 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="cell-muted">{{ $user->created_at->format('Y/m/d') }}</td>
                                <td class="col-actions">
                                    <div class="cell-actions">
                                        <a href="{{ route('super.admin.users.edit', $user) }}" class="btn" style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('super-admin.edit_user') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;transition:all 0.15s" title="{{ $user->is_active ? __('general.disable') : __('general.enable') }}" @click="confirmToggleStatus({{ $user->id }}, {{ $user->is_active ? 'true' : 'false' }})">
                                            <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                        </button>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __('general.delete') }}" @click="confirmDeleteUser({{ $user->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <form id="toggle-status-{{ $user->id }}" action="{{ route('super.admin.users.toggle-status', $user) }}" method="POST" class="d-none">@csrf</form>
                                        <form id="delete-user-{{ $user->id }}" action="{{ route('super.admin.users.destroy', $user) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)">
                        <i class="bi bi-people"></i>
                    </div>
                    <h4>{{ __('general.no_data') }}</h4>
                    <p>{{ __('messages.no_results') }}</p>
                </div>
            @endif
        </div>

        @if($users->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$users" />
                <div>{{ $users->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
    function confirmToggleStatus(userId, isActive) {
        const form = document.getElementById('toggle-status-' + userId);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            isActive ? '{{ __('messages.confirm_disable_user') }}' : '{{ __('messages.confirm_enable_user') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            isActive ? '{{ __('general.disable') }}' : '{{ __('general.enable') }}',
            isActive ? 'btn-warning' : 'btn-success'
        );
    }
    function confirmDeleteUser(userId) {
        const form = document.getElementById('delete-user-' + userId);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('messages.confirm_delete_user') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}', 'btn-danger'
        );
    }
    function initUsersPage() {
        var selectAll = document.querySelector('.select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                var checked = this.checked;
                document.querySelectorAll('.select-item').forEach(function(cb) { cb.checked = checked; });
            });
        }
    }
    initUsersPage();
    </script>
    @endpush
</x-super-admin-layout>
