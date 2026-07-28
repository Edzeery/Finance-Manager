<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.workspaces') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.workspaces') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.workspaces_desc') }}</x-slot>

    @php $showTypeSubTabs = request('status') !== 'trashed'; @endphp

    <x-filter-tabs :tabs="[
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-grid-3x3-gap'],
        'active' => ['label' => __('general.active'), 'count' => $countActive, 'icon' => 'bi-check-circle'],
        'inactive' => ['label' => __('general.inactive'), 'count' => $countInactive, 'icon' => 'bi-x-circle'],
        'trashed' => ['label' => __('general.trash'), 'count' => $countTrashed, 'icon' => 'bi-trash'],
    ]" current="{{ request('status', 'all') }}" keyParam="status" defaultKey="all"
        :preserve="['search', 'per_page']"
        subParam="{{ $showTypeSubTabs ? 'type' : '' }}"
        subCurrent="{{ $showTypeSubTabs ? request('type', '') : '' }}"
        :subTabs="$showTypeSubTabs ? $typeSubTabs : []" />

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.workspaces.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('general.search') }}..." value="{{ request('search') }}" min-width="200px" />
                    @if (request('status') && request('status') !== 'all')
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if ($showTypeSubTabs && request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:var(--primary);font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
                    <x-clear-filters :filters="['search','status','type']" :route="route('super.admin.workspaces.index')" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <x-per-page :current="(int) request('per_page', 15)" :route="route('super.admin.workspaces.index')" :preserve="['search','status','type']" :options="[10, 15, 25, 50]" />
            </div>
        </div>

        <div class="data-grid-body">
            @if($workspaces->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('general.name') }}</th>
                            <th>{{ __('general.type') }}</th>
                            <th>{{ __('super-admin.owner') }}</th>
                            <th>{{ __('super-admin.users_count') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th>{{ request('status') === 'trashed' ? __('general.deleted') : __('general.created') }}</th>
                            @if (request('status') === 'trashed')
                                <th class="col-actions"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workspaces as $ws)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:30px;height:30px;border-radius:8px;background:var(--sa-indigo-light);color:var(--sa-indigo);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div>
                                            <span style="font-weight:500">{{ $ws->name }}</span>
                                            <div class="cell-muted" style="font-size:11px">{{ $ws->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:2px 10px;border-radius:6px;text-transform:capitalize">{{ $ws->type }}</span></td>
                                <td>
                                    @php $owner = $ws->users->first(fn($u) => $u->workspaceRole($ws) === 'workspace_admin') @endphp
                                    @if($owner)
                                        <span style="font-size:13px">{{ $owner->name }}</span>
                                    @else
                                        <span class="cell-muted">—</span>
                                    @endif
                                </td>
                                <td><span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:2px 10px;border-radius:6px">{{ $ws->users->count() }}</span></td>
                                <td>
                                    @if (request('status') === 'trashed')
                                        <x-status-badge domain="general" status="archived" set="bi" />
                                    @else
                                        <x-status-badge domain="general" :status="$ws->is_active ? 'active' : 'inactive'" set="bi" />
                                    @endif
                                </td>
                                <td class="cell-muted">
                                    {{ request('status') === 'trashed' ? $ws->deleted_at->format('Y/m/d') : $ws->created_at->format('Y/m/d') }}
                                </td>
                                @if (request('status') === 'trashed')
                                    <td class="col-actions">
                                        <div class="cell-actions">
                                            <button type="button" class="btn btn-icon"
                                                title="{{ __('general.restore') }}"
                                                onclick="confirmRestoreWorkspace({{ $ws->id }})">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                            <button type="button" class="btn btn-icon btn-icon-danger"
                                                title="{{ __('general.force_delete') }}"
                                                onclick="confirmForceDeleteWorkspace({{ $ws->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="restore-ws-{{ $ws->id }}"
                                                action="{{ route('super.admin.workspaces.restore', $ws->id) }}"
                                                method="POST" class="d-none">@csrf</form>
                                            <form id="force-delete-ws-{{ $ws->id }}"
                                                action="{{ route('super.admin.workspaces.force-delete', $ws->id) }}"
                                                method="POST" class="d-none">@csrf @method('DELETE')</form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <x-empty-state icon="bi bi-building" :title="__('general.no_data')" :description="request('status') === 'trashed' ? __('messages.no_trashed') : __('messages.no_results')" />
            @endif
        </div>

        @if($workspaces->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$workspaces" />
                <div>{{ $workspaces->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function confirmRestoreWorkspace(id) {
                const form = document.getElementById('restore-ws-' + id);
                if (!form) return;
                showConfirmModal(
                    '{{ __('general.confirm') }}',
                    '{{ __('messages.confirm_restore_workspace') }}',
                    (confirmed) => {
                        if (confirmed) form.submit();
                    },
                    '{{ __('general.restore') }}', 'btn-success'
                );
            }

            function confirmForceDeleteWorkspace(id) {
                const form = document.getElementById('force-delete-ws-' + id);
                if (!form) return;
                showConfirmModal(
                    '{{ __('general.confirm') }}',
                    '{{ __('messages.confirm_force_delete_workspace') }}',
                    (confirmed) => {
                        if (confirmed) form.submit();
                    },
                    '{{ __('general.force_delete') }}', 'btn-danger'
                );
            }
        </script>
    @endpush
</x-super-admin-layout>
