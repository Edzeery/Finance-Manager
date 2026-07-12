<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.workspaces') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.workspaces') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.workspaces_desc') }}</x-slot>

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.workspaces.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('general.search') }}..." value="{{ request('search') }}" min-width="200px" />
                    @php $typeOptions = collect($types)->mapWithKeys(fn($t) => [$t => ucfirst($t)])->toArray(); @endphp
                    <x-select-filter name="type" :options="$typeOptions" placeholder="{{ __('general.all_types') }}" min-width="120px" />
                    <x-select-filter name="status" :options="[
                        'active' => __('general.active'),
                        'inactive' => __('general.inactive'),
                    ]" placeholder="{{ __('general.all_status') }}" min-width="110px" />
                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
                    <x-clear-filters :filters="['search','type','status']" :route="route('super.admin.workspaces.index')" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <x-per-page :current="(int) request('per_page', 15)" :route="route('super.admin.workspaces.index')" :preserve="['search','type','status']" :options="[10, 15, 25, 50]" />
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
                            <th>{{ __('general.created') }}</th>
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
                                    @if($ws->is_active)
                                        <span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.active') }}</span>
                                    @else
                                        <span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.inactive') }}</span>
                                    @endif
                                </td>
                                <td class="cell-muted">{{ $ws->created_at->format('Y/m/d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)">
                        <i class="bi bi-building"></i>
                    </div>
                    <h4>{{ __('general.no_data') }}</h4>
                    <p>{{ __('messages.no_results') }}</p>
                </div>
            @endif
        </div>

        @if($workspaces->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$workspaces" />
                <div>{{ $workspaces->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>
</x-super-admin-layout>
