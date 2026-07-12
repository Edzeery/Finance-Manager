<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.features') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.features') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.features_desc') }}</x-slot>

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.features.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('general.search') }}..." value="{{ request('search') }}" />
                    <x-select-filter name="type" :options="[
                        'boolean' => 'Boolean',
                        'value' => 'Value',
                        'text' => 'Text',
                    ]" placeholder="{{ __('super-admin.all_types') }}" min-width="110px" />
                    <x-select-filter name="is_core" :options="[
                        'true' => __('super-admin.core'),
                        'false' => __('super-admin.addon'),
                    ]" placeholder="{{ __('super-admin.all_features') }}" min-width="90px" />
                    <x-clear-filters :filters="['search','type','is_core']" :route="route('super.admin.features.index')" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <div class="d-flex align-items-center gap-2">
                    <x-per-page :current="(int) request('per_page', 20)" :route="route('super.admin.features.index')" :preserve="['search','type','is_core']" :options="[10, 20, 30, 50]" />
                    <a href="{{ route('super.admin.features.create') }}" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                        <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_feature') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="data-grid-body">
            @if($features->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>{{ __('super-admin.feature_name_en') }}</th>
                            <th>{{ __('super-admin.feature_name_ar') }}</th>
                            <th>{{ __('super-admin.feature_slug') }}</th>
                            <th>{{ __('super-admin.feature_type') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($features as $feature)
                            <tr>
                                <td style="color:var(--text-muted);font-size:12px">{{ $feature->sort_order }}</td>
                                <td>
                                    <strong>{{ $feature->name_en }}</strong>
                                    @if($feature->is_core)
                                        <span class="badge" style="font-size:9px;background:var(--info-light);color:var(--info);padding:2px 8px;border-radius:4px;font-weight:600;margin-inline-start:6px">{{ __('super-admin.core') }}</span>
                                    @endif
                                </td>
                                <td style="font-size:13px;color:var(--text-secondary)">{{ $feature->name_ar ?: '—' }}</td>
                                <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px">{{ $feature->slug }}</code></td>
                                <td><span style="font-size:12px;color:var(--text-secondary)">{{ $feature->type }}</span></td>
                                <td>
                                    @if($feature->is_core)
                                        <span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('super-admin.core') }}</span>
                                    @else
                                        <span class="badge" style="font-size:10px;background:var(--bg-subtle);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('super-admin.addon') }}</span>
                                    @endif
                                </td>
                                <td class="col-actions">
                                    <div class="cell-actions">
                                        <a href="{{ route('super.admin.features.edit', $feature) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('super.admin.features.destroy', $feature) }}" id="delete-feature-{{ $feature->id }}" style="display:none">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __('general.delete') }}" @click="confirmDeleteFeature({{ $feature->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-list-check"></i></div>
                    <h4>{{ __('general.no_data') }}</h4>
                    <p>{{ __('super-admin.no_features') }}</p>
                </div>
            @endif
        </div>

        @if($features->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$features" />
                <div>{{ $features->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
    function confirmDeleteFeature(id) {
        const form = document.getElementById('delete-feature-' + id);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('super-admin.confirm_delete_feature') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }
    </script>
    @endpush
</x-super-admin-layout>
