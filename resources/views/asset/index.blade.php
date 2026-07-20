@php
    $perm = fn($p) => auth()
        ->user()
        ->hasPermission("asset.$p");
    $canCreate = $perm('create');
    $canUpdate = $perm('update');
    $canDelete = $perm('delete');
    $canRestore = $perm('restore');
    $canForceDelete = $perm('force-delete');
    $canExport = $perm('export');
    $hasActions = $canUpdate || $canDelete || $canRestore || $canForceDelete;
@endphp

<x-app-layout>
    <x-slot:title>{{ __('asset.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('asset.title') }}</x-slot>

    {{-- KPI Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-kpi-card
                icon="bi-safe2"
                iconBg="rgba(21,183,108,0.1)"
                iconColor="var(--accent)"
                :label="__('asset.total_assets')"
                :value="number_format($totalValue, 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
        <div class="col-md-3">
            <x-kpi-card
                icon="bi-water"
                iconBg="rgba(21,183,108,0.1)"
                iconColor="var(--accent)"
                :label="__('asset.total_liquid')"
                :value="number_format($liquidValue, 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
        <div class="col-md-3">
            <x-kpi-card
                icon="bi-heart"
                iconBg="rgba(245,158,11,0.1)"
                iconColor="var(--warning)"
                :label="__('asset.total_zakatable')"
                :value="number_format($zakatableValue, 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
        <div class="col-md-3">
            <x-kpi-card
                icon="bi-boxes"
                iconBg="rgba(59,130,246,0.1)"
                iconColor="var(--info)"
                :label="__('asset.total_count')"
                :value="$assets->total()"
            />
        </div>
    </div>

    @php $showSubTabs = $tab !== 'trashed'; @endphp

    <x-filter-tabs :tabs="$tabs" current="{{ $tab }}" defaultKey="all" subParam="type"
        subCurrent="{{ request('type', '') }}" :subTabs="$showSubTabs ? $typeSubTabs : []" />

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form method="GET" action="{{ route('asset.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            @if (request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif

            <x-search-filter name="search" :value="request('search')" size="sm" />

            <x-clear-filters :filters="['type', 'search']" :route="route('asset.index')" />
        </form>

        <div class="d-flex gap-2 align-items-center">
            <x-data-toolbar entity="asset" :show-export="$canExport" />
            <x-per-page :current="request('per_page', 15)" :route="route('asset.index')" :preserve="['type', 'search', 'tab']" />
            @if ($tab !== 'trashed' && $canCreate)
                <a href="{{ route('asset.create') }}" class="btn btn-accent btn-custom">
                    <i class="bi bi-plus-lgms-1"></i>{{ __('asset.add') }}
                </a>
            @endif
        </div>
    </div>

    <form id="bulkForm" method="POST" data-bulk-delete-route="{{ route('asset.bulk-delete') }}"
        data-bulk-force-delete-route="{{ route('asset.bulk-force-delete') }}">@csrf</form>

    @foreach ($assets as $asset)
        <form id="delete-form-asset-{{ $asset->id }}" action="{{ route('asset.destroy', $asset) }}" method="POST"
            style="display:none">
            @csrf @method('DELETE')
        </form>
        <form id="force-delete-form-asset-{{ $asset->id }}" action="{{ route('asset.force-delete', $asset->id) }}"
            method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
    @endforeach

    <div class="card-custom">
        <div class="card-body p-0">
            <div id="bulkBar" class="bulk-bar" style="display:none; margin:12px">
                <span id="selectedCount">0</span> <span>{{ __('general.selected') }}</span>
                @if ($tab === 'trashed')
                    @if ($canRestore)
                        <button type="submit" form="bulkForm" formaction="{{ route('asset.bulk-restore') }}"
                            class="btn btn-sm btn-outline-success btn-custom">
                            <i class="bi bi-arrow-counterclockwisems-1"></i>{{ __('general.restore') }}
                        </button>
                    @endif
                    @if ($canForceDelete)
                        <button type="button" class="btn btn-sm btn-outline-danger btn-custom"
                            @click="confirmBulkForceDelete()">
                            <i class="bi bi-trash3ms-1"></i>{{ __('general.force_delete') }}
                        </button>
                    @endif
                @else
                    @if ($canDelete)
                        <button type="button" class="btn btn-sm btn-outline-danger btn-custom"
                            @click="confirmBulkDelete('asset')">
                            <i class="bi bi-trashms-1"></i>{{ __('general.delete') }}
                        </button>
                    @endif
                @endif
            </div>
            @if ($assets->count())
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th style="width:40px"><input type="checkbox" id="selectAll"
                                        @change="toggleSelectAll($el)"></th>
                                <th>{{ __('asset.type') }}</th>
                                <th>{{ __('asset.name') }}</th>
                                <th class="text-end">{{ __('asset.quantity') }}</th>
                                <th class="text-end">{{ __('asset.total_value') }}</th>
                                <th>{{ __('asset.is_liquid') }}</th>
                                <th>{{ __('asset.is_zakatable') }}</th>
                                @if ($hasActions)
                                    <th class="text-center" style="width:80px">{{ __('general.actions') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assets as $asset)
                                @php $assetType = $asset->type; @endphp
                                <tr @if ($tab === 'trashed') style="opacity:0.7" @endif>
                                    <td><input type="checkbox" name="ids[]" value="{{ $asset->id }}"
                                            class="select-item" form="bulkForm"></td>
                                    <td>
                                        <x-status-badge domain="asset" :status="$asset->type->value" set="bi" />

                                    </td>
                                    <td>
                                        <span style="font-weight:500">{{ $asset->name }}</span>
                                        @if ($asset->type->value === 'gold' && $asset->karat)
                                            <span class="badge ms-1" style="font-size:10px;background:rgba(245,158,11,0.12);color:var(--warning);padding:2px 6px;border-radius:4px;font-weight:500">{{ $asset->karat }}K</span>
                                        @endif
                                        @if ($asset->description)
                                            <br><small
                                                style="color:var(--text-muted)">{{ $asset->description }}</small>
                                        @endif
                                    </td>
                                    <td class="text-start">
                                        @if (($asset->type->value === 'gold' || $asset->type->value === 'silver') && $asset->weight_grams)
                                            {{ number_format($asset->weight_grams, 4) }}g
                                        @else
                                            {{ $asset->quantity ? number_format($asset->quantity, 4) : '—' }}
                                        @endif
                                    </td>
                                    <td class="text-start fw-bold">{{ number_format($asset->total_value, 2) }} <small
                                            style="font-weight:400; color:var(--text-muted)">{{ config('finance.currency_symbol') }}</small>
                                    </td>
                                    <td>
                                        @if ($asset->is_liquid)
                                            <x-status-badge domain="general" status="yes" set="bi" />
                                        @else
                                            <x-status-badge domain="general" status="no" set="bi" />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($asset->is_zakatable)
                                            <x-status-badge domain="general" status="yes" set="bi" />
                                        @else
                                            <x-status-badge domain="general" status="no" set="bi" />
                                        @endif
                                    </td>
                                    @if ($hasActions)
                                        <td class="text-center">
                                            @if ($tab === 'trashed')
                                                @if ($canRestore)
                                                    <form action="{{ route('asset.restore', $asset) }}"
                                                        method="POST" style="display:inline">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="action-btn"
                                                            title="{{ __('general.restore') }}"
                                                            style="color:var(--success)">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if ($canForceDelete)
                                                    <button type="button" class="action-btn"
                                                        title="{{ __('general.force_delete') }}"
                                                        style="color:var(--danger)"
                                                        @click="confirmForceDelete('asset', {{ $asset->id }})">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <div class="action-group justify-content-center">
                                                    @if ($canUpdate)
                                                        <a href="{{ route('asset.edit', $asset) }}"
                                                            class="action-btn" title="{{ __('general.edit') }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endif
                                                    @if ($canDelete)
                                                        <button type="button" class="action-btn"
                                                            title="{{ __('general.delete') }}"
                                                            @click="confirmDelete('asset', {{ $asset->id }})">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <x-pagination-info :items="$assets" />
                    <div>
                        {{ $assets->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            @else
                <x-empty-state icon="bi-safe" :title="__('general.no_data')" :message="$tab === 'trashed' ? __('messages.no_trashed') : __('messages.no_results')" :action="$tab === 'trashed' ? route('asset.index') : ($canCreate ? route('asset.create') : '#')"
                    :actionText="$tab === 'trashed' ? __('general.back') : __('asset.add')" />
            @endif
        </div>
    </div>

</x-app-layout>
