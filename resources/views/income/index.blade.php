@php
    $perm = fn($p) => auth()->user()->hasPermission("income.$p");
    $canCreate = $perm('create');
    $canUpdate = $perm('update');
    $canDelete = $perm('delete');
    $canRestore = $perm('restore');
    $canForceDelete = $perm('force-delete');
    $canExport = $perm('export');
    $canImport = $perm('import');
    $hasActions = $canUpdate || $canDelete || $canRestore || $canForceDelete;
@endphp

<x-app-layout>
    <x-slot:title>{{ __('income.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('income.title') }}</x-slot>
    <x-slot:page-description>{{ __('income.total_income') }}: <strong>{{ number_format($totalIncome, 2) }} {{ config('finance.currency_symbol') }}</strong></x-slot>

    <x-filter-tabs :tabs="$tabs" current="{{ $tab }}" />

    @php
        $catOptions = $categories->mapWithKeys(fn($cat) => [$cat->id => locale_name($cat)])->toArray();
    @endphp
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form method="GET" action="{{ route('income.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <x-search-filter name="search" :value="request('search')" size="sm" />
            <x-select-filter name="category" :options="$catOptions" placeholder="{{ __('general.all') }} {{ __('income.categories') }}" min-width="140px" onchange="this.form.submit()" class="form-custom" style="padding:6px 12px" />
            <x-select-filter name="type" :options="[
                'fixed' => __('income.fixed'),
                'variable' => __('income.variable'),
                'recurring' => __('income.recurring'),
            ]" placeholder="{{ __('general.all') }}" min-width="100px" onchange="this.form.submit()" class="form-custom" style="padding:6px 12px" />

            <input type="date" name="date_from" class="form-custom" style="width:auto;padding:6px 12px;font-size:13px" value="{{ request('date_from') }}" onchange="this.form.submit()">
            <input type="date" name="date_to" class="form-custom" style="width:auto;padding:6px 12px;font-size:13px" value="{{ request('date_to') }}" onchange="this.form.submit()">

            <x-clear-filters :filters="['category','type','date_from','date_to','search']" :route="route('income.index')" />
        </form>

        <div class="d-flex gap-2 align-items-center">
            <x-data-toolbar entity="income" :show-export="$canExport" :show-import="$canImport" />
            <x-per-page :current="request('per_page', 15)" :route="route('income.index')" :preserve="['category','type','date_from','date_to','search','tab']" />
            @if($tab !== 'trashed' && $canCreate)
                <a href="{{ route('income.create') }}" class="btn btn-accent btn-custom">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('income.add') }}
                </a>
            @endif
        </div>
    </div>

    <form id="bulkForm" method="POST"
          data-bulk-delete-route="{{ route('income.bulk-delete') }}"
          data-bulk-force-delete-route="{{ route('income.bulk-force-delete') }}">
        @csrf
    </form>

    @foreach($incomes as $income)
        <form id="delete-form-income-{{ $income->id }}" action="{{ route('income.destroy', $income) }}" method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
        <form id="force-delete-form-income-{{ $income->id }}" action="{{ route('income.force-delete', $income->id) }}" method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
    @endforeach

    <div class="card-custom">
        <div class="card-body p-0">
            @if($incomes->count())
                <div id="bulkBar" class="bulk-bar" style="display:none; padding:12px 16px; border-bottom:1px solid var(--border); align-items:center; gap:12px; font-size:13px">
                    <span id="selectedCount">0</span> <span>{{ __('general.selected') }}</span>
                    @if($tab === 'trashed')
                        @if($canRestore)
                            <button type="submit" form="bulkForm" formaction="{{ route('income.bulk-restore') }}" class="btn btn-sm btn-outline-success btn-custom">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('general.restore') }}
                            </button>
                        @endif
                        @if($canForceDelete)
                            <button type="button" class="btn btn-sm btn-outline-danger btn-custom" @click="confirmBulkForceDelete()">
                                <i class="bi bi-trash3 me-1"></i>{{ __('general.force_delete') }}
                            </button>
                        @endif
                    @else
                        @if($canDelete)
                            <button type="button" class="btn btn-sm btn-outline-danger btn-custom" @click="confirmBulkDelete('income')">
                                <i class="bi bi-trash me-1"></i>{{ __('general.delete') }}
                            </button>
                        @endif
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width:40px"><input type="checkbox" id="selectAll" @change="toggleSelectAll($el)"></th>
                            <th>{{ __('general.date') }}</th>
                            <th>{{ __('general.description') }}</th>
                            <th>{{ __('general.category') }}</th>
                            <th>{{ __('income.type') }}</th>
                            <th class="text-end">{{ __('general.amount') }}</th>
                            @if($hasActions)
                                <th class="text-center" style="width:80px">{{ __('general.actions') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incomes as $income)
                            <tr @if($tab === 'trashed') style="opacity:0.7" @endif>
                                <td><input type="checkbox" name="ids[]" value="{{ $income->id }}" class="select-item" form="bulkForm"></td>
                                <td style="white-space:nowrap">{{ $income->date->format('Y/m/d') }}</td>
                                <td>
                                    <span>{{ $income->description ?: '—' }}</span>
                                    @if($income->is_recurring)
                                        <span class="badge badge-custom badge-income ms-1">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span style="display:inline-flex; align-items:center; gap:6px">
                                        <i class="{{ $income->category?->icon ?? 'bi-tag' }}" style="color:{{ $income->category?->color ?? '#64748B' }}"></i>
                                        {{ locale_name($income->category ?? new stdClass) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $type = $income->is_recurring ? 'recurring' : ($income->category?->type ?? 'variable');
                                        $typeLabels = ['fixed' => __('income.fixed'), 'variable' => __('income.variable'), 'recurring' => __('income.recurring')];
                                    @endphp
                                    <span class="badge badge-custom badge-income">{{ $typeLabels[$type] ?? $type }}</span>
                                </td>
                                <td class="text-end fw-bold" style="color:var(--success)">+{{ number_format($income->amount, 2) }}</td>
                                @if($hasActions)
                                <td class="text-center">
                                    @if($tab === 'trashed')
                                        @if($canRestore)
                                            <form action="{{ route('income.restore', $income) }}" method="POST" style="display:inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="action-btn" title="{{ __('general.restore') }}" style="color:var(--success)">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($canForceDelete)
                                            <button type="button" class="action-btn" title="{{ __('general.force_delete') }}" style="color:var(--danger)" @click="confirmForceDelete('income', {{ $income->id }})">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        @endif
                                    @else
                                        <div class="action-group justify-content-center">
                                            @if($canUpdate)
                                                <a href="{{ route('income.edit', $income) }}" class="action-btn" title="{{ __('general.edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                            @if($canDelete)
                                                <button type="button" class="action-btn" title="{{ __('general.delete') }}" @click="confirmDelete('income', {{ $income->id }})">
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
                    <x-pagination-info :items="$incomes" />
                    <div>
                        {{ $incomes->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            @else
                <x-empty-state
                    icon="bi-cash-stack"
                    :title="__('general.no_data')"
                    :message="$tab === 'trashed' ? __('messages.no_trashed') : __('messages.no_results')"
                    :action="$tab === 'trashed' ? route('income.index') : ($canCreate ? route('income.create') : '#')"
                    :actionText="$tab === 'trashed' ? __('general.back') : __('income.add')"
                />
            @endif
        </div>
    </div>

    @if($canImport)
        <x-import-modal entity="income" :entity-label="__('income.title')" />
    @endif
</x-app-layout>
