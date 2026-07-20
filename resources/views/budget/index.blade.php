@php
    $perm = fn($p) => auth()->user()->hasPermission("budget.$p");
    $canCreate = $perm('create');
    $canUpdate = $perm('update');
    $canDelete = $perm('delete');
    $canRestore = $perm('restore');
    $canForceDelete = $perm('force-delete');
    $canExport = $perm('export');
    $hasActions = $canUpdate || $canDelete || $canRestore || $canForceDelete;
@endphp

<x-app-layout>
    <x-slot:title>{{ __('budget.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('budget.title') }}</x-slot>

    <x-filter-tabs :tabs="$tabs" current="{{ $tab }}" defaultKey="all" :preserve="['search','per_page']" />

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form method="GET" action="{{ route('budget.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <x-search-filter name="search" :value="request('search')" size="sm" />
            <x-clear-filters :filters="['search']" :route="route('budget.index')" />
        </form>

        <div class="d-flex gap-2 align-items-center">
            <x-data-toolbar entity="budget" :show-export="$canExport" />
            <x-per-page :current="request('per_page', 15)" :route="route('budget.index')" :preserve="['search','tab']" />
            @if($tab !== 'trashed' && $canCreate)
                <a href="{{ route('budget.create') }}" class="btn btn-accent btn-custom">
                    <i class="bi bi-plus-lgms-1"></i>{{ __('budget.add') }}
                </a>
            @endif
        </div>
    </div>

    <form id="bulkForm" method="POST"
          data-bulk-delete-route="{{ route('budget.bulk-delete') }}"
          data-bulk-force-delete-route="{{ route('budget.bulk-force-delete') }}">
        @csrf
    </form>

    @foreach($budgets as $budget)
        <form id="delete-form-budget-{{ $budget->id }}" action="{{ route('budget.destroy', $budget) }}" method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
        <form id="force-delete-form-budget-{{ $budget->id }}" action="{{ route('budget.force-delete', $budget->id) }}" method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
    @endforeach

    <div class="bulk-bar mb-3" id="bulkBar" style="display:none">
        <div class="d-flex align-items-center gap-3">
            <input type="checkbox" id="selectAll" @change="toggleSelectAll($el)" style="cursor:pointer">
            <span style="color:var(--text-muted); font-size:13px"><span id="selectedCount">0</span> {{ __('general.selected') }}</span>
            @if($tab === 'trashed')
                @if($canRestore)
                    <button type="button" class="btn btn-sm btn-outline-success btn-custom" @click="submitBulk('{{ route('budget.bulk-restore') }}')">
                        <i class="bi bi-arrow-counterclockwisems-1"></i>{{ __('general.restore') }}
                    </button>
                @endif
                @if($canForceDelete)
                    <button type="button" class="btn btn-sm btn-outline-danger btn-custom" @click="confirmBulkForceDelete()">
                        <i class="bi bi-trash3ms-1"></i>{{ __('general.force_delete') }}
                    </button>
                @endif
            @else
                @if($canDelete)
                    <button type="button" class="btn btn-sm btn-outline-danger btn-custom" @click="confirmBulkDelete('budget')">
                        <i class="bi bi-trashms-1"></i>{{ __('general.delete') }}
                    </button>
                @endif
            @endif
        </div>
    </div>

    @if($budgets->count())
        <div class="row g-4">
            @foreach($budgets as $budget)
                @php
                    $pct = $budget->adherence_rate;
                    $isExceeded = $budget->is_exceeded;
                    $barColor = $isExceeded ? 'var(--danger)' : ($pct > 80 ? 'var(--warning)' : 'var(--success)');
                @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="card-custom budget-card" @if($tab === 'trashed') style="opacity:0.7" @endif>
                        <div class="card-body position-relative">
                            <input type="checkbox" name="ids[]" value="{{ $budget->id }}" class="select-item" form="bulkForm" style="position:absolute; top:12px; right:12px; cursor:pointer; z-index:2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1" style="font-size:16px; font-weight:600">
                                        <a href="{{ route('budget.show', $budget) }}" class="text-decoration-none" style="color:var(--text)">
                                            {{ locale_name($budget) }}
                                        </a>
                                    </h5>
                                    <span class="badge badge-custom" style="background:var(--bg); color:var(--text-muted); font-size:11px">
                                        {{ __("budget.{$budget->type}") }}
                                    </span>
                                </div>
                                @if(!$budget->is_active)
                                    <x-status-badge domain="general" status="inactive" set="bi" />
                                @endif
                            </div>

                            <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                                <span style="color:var(--text-muted)">{{ __('budget.spent') }}</span>
                                <span class="fw-bold" style="color:{{ $barColor }}">{{ number_format($budget->totalSpent, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                                <span style="color:var(--text-muted)">{{ __('budget.remaining') }}</span>
                                <span class="fw-bold">{{ number_format(max(0, $budget->total_amount - $budget->totalSpent), 2) }}</span>
                            </div>

                            <div class="progress" style="height:10px; background:var(--border); border-radius:5px; margin-top:12px">
                                <div class="progress-bar" style="width:{{ min($pct, 100) }}%; background:{{ $barColor }}; border-radius:5px; transition:width 0.4s"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1" style="font-size:12px">
                                <span style="color:var(--text-muted)">{{ __('budget.total_amount') }}: {{ number_format($budget->total_amount, 2) }}</span>
                                <span class="fw-bold" style="color:{{ $barColor }}">{{ $pct }}%</span>
                            </div>

                            @if($isExceeded)
                                <div class="mt-2" style="font-size:12px; color:var(--danger); background:rgba(239,68,68,0.08); padding:6px 10px; border-radius:6px">
                                    <x-status-icon domain="general" status="danger" set="bi" class="me-1" />
                                    {{ __('budget.exceeded') }}
                                </div>
                            @elseif($pct > 80)
                                <div class="mt-2" style="font-size:12px; color:var(--warning); background:rgba(245,158,11,0.08); padding:6px 10px; border-radius:6px">
                                    <x-status-icon domain="general" status="warning" set="bi" class="me-1" />
                                    {{ __('budget.warning', ['percent' => $pct]) }}
                                </div>
                            @endif

                            <div class="d-flex gap-2 mt-3">
                                @if($tab === 'trashed')
                                    @if($canRestore)
                                        <form action="{{ route('budget.restore', $budget) }}" method="POST" style="display:inline; flex:1">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success btn-custom" style="flex:1">
                                                <i class="bi bi-arrow-counterclockwisems-1"></i>{{ __('general.restore') }}
                                            </button>
                                        </form>
                                    @endif
                                    @if($canForceDelete)
                                        <button type="button" class="action-btn" title="{{ __('general.force_delete') }}" style="color:var(--danger)" @click="confirmForceDelete('budget', {{ $budget->id }})">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('budget.show', $budget) }}" class="btn btn-sm btn-outline-secondary btn-custom" style="flex:1">
                                        <i class="bi bi-eyems-1"></i>{{ __('general.details') }}
                                    </a>
                                    @if($canUpdate)
                                        <a href="{{ route('budget.edit', $budget) }}" class="action-btn" title="{{ __('general.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <button type="button" class="action-btn" title="{{ __('general.delete') }}" @click="confirmDelete('budget', {{ $budget->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <x-pagination-info :items="$budgets" />
            <div>
                {{ $budgets->appends(request()->except('page'))->links() }}
            </div>
        </div>
    @else
        <x-empty-state
            icon="bi-pie-chart"
            :title="__('general.no_data')"
            :message="$tab === 'trashed' ? __('messages.no_trashed') : __('budget.create_first_budget')"
            :action="$tab === 'trashed' ? route('budget.index') : ($canCreate ? route('budget.create') : '#')"
            :actionText="$tab === 'trashed' ? __('general.back') : __('budget.add')"
        />
    @endif

</x-app-layout>
