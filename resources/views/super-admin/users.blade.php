<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.users') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.users') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.users_desc') }}</x-slot>

    @php $showSubTabs = request('status') !== 'trashed'; @endphp


    <x-filter-tabs :tabs="[
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-people'],
        'active' => ['label' => __('general.active'), 'count' => $countActive, 'icon' => 'bi-check-circle'],
        'online' => [
            'label' => __('general.online'),
            'count' => $countOnline,
            'icon' => 'bi-wifi',
            'color' => '#16a34a',
        ],
        'inactive' => ['label' => __('general.inactive'), 'count' => $countInactive, 'icon' => 'bi-x-circle'],
        'suspended' => ['label' => __('general.suspended'), 'count' => $countSuspended, 'icon' => 'bi-pause-circle'],
        'banned' => ['label' => __('general.banned'), 'count' => $countBanned, 'icon' => 'bi-slash-circle'],
        'trashed' => ['label' => __('general.trash'), 'count' => $countTrashed, 'icon' => 'bi-trash'],
    ]" current="{{ request('status', 'all') }}" keyParam="status" defaultKey="all"
        :preserve="['search', 'per_page']" subParam="{{ $showSubTabs ? 'super_admin' : '' }}"
        subCurrent="{{ $showSubTabs ? request('super_admin', '') : '' }}" :subTabs="$showSubTabs
            ? [
                '' => ['label' => __('general.all')],
                'yes' => ['label' => __('super-admin.super_admin')],
                'no' => ['label' => __('super-admin.users')],
            ]
            : []" />

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.users.index') }}"
                    class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('general.search') }}..."
                        value="{{ request('search') }}" min-width="200px" />
                    @if (request('status') && request('status') !== 'all')
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if ($showSubTabs && request('super_admin'))
                        <input type="hidden" name="super_admin" value="{{ request('super_admin') }}">
                    @endif
                    <button type="submit" class="btn"
                        style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
                    <x-clear-filters :filters="['search', 'status', 'super_admin']" :route="route('super.admin.users.index')" />
                </form>
                @if (request('status') === 'trashed')
                    <button id="bulk-restore-btn" class="btn bulk-btn" style="display:none"
                        onclick="confirmBulkRestore()">
                        <i class="bi bi-arrow-counterclockwise"></i> {{ __('general.restore') }}
                    </button>
                @else
                    <button id="bulk-delete-btn" class="btn bulk-btn" style="display:none"
                        onclick="confirmBulkDelete()">
                        <i class="bi bi-trash"></i> {{ __('general.delete') }}
                    </button>
                @endif
            </div>
            <div class="data-grid-toolbar-right">
                <span id="bulk-count" class="bulk-count" style="display:none"></span>
                <x-per-page :current="(int) request('per_page', 15)" :route="route('super.admin.users.index')" :preserve="['search', 'status', 'super_admin']" :options="[10, 15, 25, 50]" />
            </div>
        </div>

        <div class="data-grid-body">
            @if ($users->count())
                <form id="bulk-delete-form" action="{{ route('super.admin.users.bulk-delete') }}" method="POST"
                    class="d-none">@csrf<input type="hidden" name="user_ids" id="bulk-delete-ids"></form>
                <form id="bulk-restore-form" action="{{ route('super.admin.users.bulk-restore') }}" method="POST"
                    class="d-none">@csrf<input type="hidden" name="user_ids" id="bulk-restore-ids"></form>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-checkbox"><input type="checkbox" class="select-all"
                                    style="accent-color:var(--accent)"></th>
                            <th>{{ __('general.name') }}</th>
                            <th>{{ __('general.email') }}</th>
                            @if (request('status') !== 'trashed')
                                <th>{{ __('super-admin.workspaces') }}</th>
                                <th>{{ __('general.status') }}</th>
                                <th>{{ __('super-admin.super_admin') }}</th>
                                <th>{{ __('super-admin.roles') }}</th>
                            @endif
                            <th>{{ request('status') === 'trashed' ? __('general.deleted') : __('general.member_since') }}
                            </th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            @php $sr = $user->statusRecord; @endphp
                            <tr>
                                <td class="col-checkbox"><input type="checkbox" class="select-item"
                                        value="{{ $user->id }}" style="accent-color:var(--accent)"></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar" style="position:relative">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                            <span class="online-dot"
                                                style="background:{{ $sr && $sr->online_status->value === 'online' ? '#16a34a' : '#9ca3af' }}"></span>
                                        </div>
                                        <span class="fw-500">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="cell-muted">{{ $user->email }}
                                    <x-status-icon domain="email_verification" :status="$user->email_verified_at ? 'email_verified' : 'email_unverified'" set="bi" />
                                </td>

                                @if (request('status') !== 'trashed')
                                    <td><span class="badge-count">{{ $user->workspaces->count() }}</span></td>

                                    <td>
                                        <x-status-select domain="user" name="status" :selected="$user->status" set="bi"
                                            size="sm" data-user-id="{{ $user->id }}"
                                            onchange="confirmChangeStatus(this)" />
                                        <form id="set-status-form-{{ $user->id }}"
                                            action="{{ route('super.admin.users.set-status', $user) }}" method="POST"
                                            class="d-none">@csrf
                                            <input type="hidden" name="status"
                                                id="set-status-value-{{ $user->id }}">
                                        </form>
                                    </td>
                                    <td>
                                        @if ($user->hasRole('super_admin'))
                                            <x-status-badge domain="general" status="yes" set="fa"
                                                class="text-lg fw-bold" />
                                        @else
                                            <span class="cell-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse($user->roles->take(2) as $role)
                                                <x-status-badge domain="role" :status="$role->slug" set="fa"
                                                    class="text-lg fw-bold" />
                                            @empty
                                                <span class="cell-muted">&mdash;</span>
                                            @endforelse
                                            @if ($user->roles->count() > 2)
                                                <span class="badge-more">+{{ $user->roles->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                @endif

                                <td class="cell-muted">
                                    {{ request('status') === 'trashed' ? $user->deleted_at->format('Y/m/d') : $user->created_at->format('Y/m/d') }}
                                </td>

                                <td class="col-actions">
                                    @if (request('status') === 'trashed')
                                        <div class="cell-actions">
                                            <button type="button" class="btn btn-icon"
                                                title="{{ __('general.restore') }}"
                                                onclick="confirmRestoreUser({{ $user->id }})">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                            <button type="button" class="btn btn-icon btn-icon-danger"
                                                title="{{ __('general.force_delete') }}"
                                                onclick="confirmForceDeleteUser({{ $user->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="restore-user-{{ $user->id }}"
                                                action="{{ route('super.admin.users.restore', $user->id) }}"
                                                method="POST" class="d-none">@csrf</form>
                                            <form id="force-delete-user-{{ $user->id }}"
                                                action="{{ route('super.admin.users.force-destroy', $user->id) }}"
                                                method="POST" class="d-none">@csrf @method('DELETE')</form>
                                        </div>
                                    @else
                                        <div class="cell-actions">
                                            <a href="{{ route('super.admin.users.edit', $user) }}"
                                                class="btn btn-icon" title="{{ __('super-admin.edit_user') }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-icon btn-icon-danger"
                                                title="{{ __('general.delete') }}"
                                                onclick="confirmDeleteUser({{ $user->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-user-{{ $user->id }}"
                                                action="{{ route('super.admin.users.destroy', $user) }}"
                                                method="POST" class="d-none">@csrf @method('DELETE')</form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <x-empty-state icon="bi bi-{{ request('status') === 'trashed' ? 'trash' : 'people' }}" :title="__('general.no_data')" :description="request('status') === 'trashed' ? __('messages.no_trashed') : __('messages.no_results')" />
            @endif
        </div>

        @if ($users->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$users" />
                <div>{{ $users->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function confirmChangeStatus(component) {
                var userId = component.dataset.userId;
                var hiddenInput = component.querySelector('input[type="hidden"]');
                var value = hiddenInput ? hiddenInput.value : null;
                if (!value) return;
                var statusNames = {
                    'active': '{{ __('general.active') }}',
                    'inactive': '{{ __('general.inactive') }}',
                    'pending': '{{ __('general.pending') }}',
                    'suspended': '{{ __('general.suspended') }}',
                    'banned': '{{ __('general.banned') }}'
                };
                showConfirmModal(
                    '{{ __('general.confirm') }}',
                    '{{ __('messages.confirm_change_user_status') }}'.replace(':status', statusNames[value] || value),
                    function(confirmed) {
                        if (confirmed) {
                            document.getElementById('set-status-value-' + userId).value = value;
                            document.getElementById('set-status-form-' + userId).submit();
                        } else {
                            location.reload();
                        }
                    },
                    statusNames[value] || value,
                    value === 'active' ? 'btn-success' : value === 'inactive' ? 'btn-secondary' : value === 'suspended' ?
                    'btn-warning' :
                    'btn-danger'
                );
            }

            function confirmDeleteUser(userId) {
                const form = document.getElementById('delete-user-' + userId);
                if (!form) return;
                showConfirmModal(
                    '{{ __('general.confirm') }}',
                    '{{ __('messages.confirm_delete_user') }}',
                    (confirmed) => {
                        if (confirmed) form.submit();
                    },
                    '{{ __('general.delete') }}', 'btn-danger'
                );
            }

            function confirmRestoreUser(userId) {
                const form = document.getElementById('restore-user-' + userId);
                if (!form) return;
                showConfirmModal(
                    '{{ __('general.confirm') }}',
                    '{{ __('messages.confirm_restore_user') }}',
                    (confirmed) => {
                        if (confirmed) form.submit();
                    },
                    '{{ __('general.restore') }}', 'btn-success'
                );
            }

            function confirmForceDeleteUser(userId) {
                const form = document.getElementById('force-delete-user-' + userId);
                if (!form) return;
                showConfirmModal(
                    '{{ __('general.confirm') }}',
                    '{{ __('messages.confirm_force_delete_user') }}',
                    (confirmed) => {
                        if (confirmed) form.submit();
                    },
                    '{{ __('general.force_delete') }}', 'btn-danger'
                );
            }

            function updateBulkButton() {
                const checked = document.querySelectorAll('.select-item:checked');
                const count = checked.length;
                const btn = document.getElementById('bulk-restore-btn') || document.getElementById('bulk-delete-btn');
                const countEl = document.getElementById('bulk-count');
                if (count > 0) {
                    if (btn) btn.style.display = 'inline-flex';
                    if (countEl) {
                        countEl.style.display = 'inline';
                        countEl.textContent = count + ' {{ __('general.selected') }}';
                    }
                } else {
                    if (btn) btn.style.display = 'none';
                    if (countEl) countEl.style.display = 'none';
                }
            }

            function clearSelection() {
                document.querySelectorAll('.select-item, .select-all').forEach(function(cb) {
                    cb.checked = false;
                });
                updateBulkButton();
            }

            function confirmBulkDelete() {
                const checked = document.querySelectorAll('.select-item:checked');
                if (!checked.length) return;
                document.getElementById('bulk-delete-ids').value = Array.from(checked).map(function(cb) {
                    return cb.value;
                }).join(',');
                showConfirmModal(
                    '{{ __('general.confirm') }}',
                    '{{ __('messages.confirm_bulk_delete') }}',
                    function(confirmed) {
                        if (confirmed) document.getElementById('bulk-delete-form').submit();
                    },
                    '{{ __('general.delete') }}', 'btn-danger'
                );
            }

            function confirmBulkRestore() {
                const checked = document.querySelectorAll('.select-item:checked');
                if (!checked.length) return;
                document.getElementById('bulk-restore-ids').value = Array.from(checked).map(function(cb) {
                    return cb.value;
                }).join(',');
                showConfirmModal(
                    '{{ __('general.confirm') }}',
                    '{{ __('messages.confirm_bulk_restore') }}',
                    function(confirmed) {
                        if (confirmed) document.getElementById('bulk-restore-form').submit();
                    },
                    '{{ __('general.restore') }}', 'btn-success'
                );
            }

            (function() {
                const selectAll = document.querySelector('.select-all');
                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        document.querySelectorAll('.select-item').forEach(function(cb) {
                            cb.checked = this.checked;
                        }, this);
                        updateBulkButton();
                    });
                }
                document.querySelectorAll('.select-item').forEach(function(cb) {
                    cb.addEventListener('change', updateBulkButton);
                });
            })();
        </script>
    @endpush

    @push('styles')
        <style>
            .user-avatar {
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background: var(--accent);
                color: #0F172A;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 700;
                flex-shrink: 0;
            }

            .online-dot {
                position: absolute;
                bottom: -1px;
                right: -1px;
                width: 9px;
                height: 9px;
                border-radius: 50%;
                border: 2px solid var(--bg);
            }

            .fw-500 {
                font-weight: 500;
            }

            .badge-count {
                display: inline-block;
                font-size: 11px;
                background: var(--bg-subtle);
                color: var(--text);
                padding: 2px 10px;
                border-radius: 6px;
            }

            .badge-more {
                display: inline-block;
                font-size: 10px;
                background: var(--border);
                color: var(--text-muted);
                padding: 2px 8px;
                border-radius: 4px;
            }

            .bulk-btn {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 7px 16px;
                font-size: 13px;
                font-weight: 600;
                border-radius: var(--radius-sm);
                border: 1px solid var(--border);
                background: var(--bg);
                color: var(--text);
                cursor: pointer;
                transition: all 0.15s;
                white-space: nowrap;
            }

            .bulk-btn:hover {
                background: var(--bg-subtle);
                border-color: var(--accent);
            }

            .bulk-count {
                font-size: 12px;
                color: var(--text-muted);
                white-space: nowrap;
                margin-right: 8px;
            }

            .btn-icon {
                width: 30px;
                height: 30px;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: var(--radius-xs);
                border: 1px solid var(--border);
                background: transparent;
                color: var(--text-muted);
                font-size: 13px;
                text-decoration: none;
                transition: all 0.15s;
                cursor: pointer;
            }

            .btn-icon:hover {
                background: var(--bg-subtle);
                color: var(--text);
                border-color: var(--accent);
            }

            .btn-icon-danger:hover {
                background: rgba(239, 68, 68, 0.08);
                color: var(--danger);
                border-color: var(--danger);
            }

            .data-table tbody tr {
                transition: background 0.15s;
            }

            .data-table tbody tr:hover {
                background: var(--bg-subtle);
            }

            .data-table th,
            .data-table td {
                padding: 10px 12px;
            }

            .data-table .col-checkbox {
                width: 40px;
                text-align: center;
            }

            .data-table .col-actions {
                width: 80px;
                text-align: right;
            }

            .status-col {
                position: relative;
            }
        </style>
    @endpush
</x-super-admin-layout>
