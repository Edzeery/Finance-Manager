@php
    $perm = fn($p) => auth()->user()->hasPermission("expense.$p");
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
    <x-slot:title>{{ __('expense.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('expense.title') }}</x-slot>
    <x-slot:page-description>{{ __('expense.total_expense') }}: <strong>{{ number_format($totalExpense, 2) }} {{ config('finance.currency_symbol') }}</strong></x-slot>

    @php $showSubTabs = $tab !== 'trashed'; @endphp

    <x-filter-tabs :tabs="$tabs" current="{{ $tab }}" defaultKey="all"
        subParam="category"
        subCurrent="{{ request('category', '') }}"
        :subTabs="$showSubTabs ? $catSubTabs : []"
        :preserve="['type','date_from','date_to','search','per_page']" />

    @php
        $typeTabs = [
            '' => ['label' => __('general.all')],
            'fixed' => ['label' => __('expense.fixed')],
            'variable' => ['label' => __('expense.variable')],
            'periodic' => ['label' => __('expense.periodic')],
        ];
    @endphp
    <x-filter-tabs :tabs="$typeTabs" current="{{ request('type', '') }}" keyParam="type" defaultKey="" :preserve="['category','date_from','date_to','search','per_page','tab']" />

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form method="GET" action="{{ route('expense.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <x-search-filter name="search" :value="request('search')" size="sm" />
            @if (request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif

            <input type="date" name="date_from" class="form-custom" style="width:auto;padding:6px 12px;font-size:13px" value="{{ request('date_from') }}" onchange="this.form.submit()">
            <input type="date" name="date_to" class="form-custom" style="width:auto;padding:6px 12px;font-size:13px" value="{{ request('date_to') }}" onchange="this.form.submit()">

            <x-clear-filters :filters="['category','type','date_from','date_to','search']" :route="route('expense.index')" />
        </form>

        <div class="d-flex gap-2 align-items-center">
            <x-data-toolbar entity="expense" :show-export="$canExport" :show-import="$canImport" />
            <x-per-page :current="request('per_page', 15)" :route="route('expense.index')" :preserve="['category','type','date_from','date_to','search','tab']" />
            @if($tab !== 'trashed' && $canCreate)
                <a href="{{ route('expense.create') }}" class="btn btn-accent btn-custom">
                    <i class="bi bi-plus-lg"></i>{{ __('expense.add') }}
                </a>
            @endif
        </div>
    </div>

    <form id="bulkForm" method="POST"
          data-bulk-delete-route="{{ route('expense.bulk-delete') }}"
          data-bulk-force-delete-route="{{ route('expense.bulk-force-delete') }}">
        @csrf
    </form>

    @foreach($expenses as $expense)
        <form id="delete-form-expense-{{ $expense->id }}" action="{{ route('expense.destroy', $expense) }}" method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
        <form id="force-delete-form-expense-{{ $expense->id }}" action="{{ route('expense.force-delete', $expense->id) }}" method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
    @endforeach

    <div class="card-custom">
        <div class="card-body p-0">
            @if($expenses->count())
                <div id="bulkBar" class="bulk-bar" style="display:none; padding:12px 16px; border-bottom:1px solid var(--border); align-items:center; gap:12px; font-size:13px">
                    <span id="selectedCount">0</span> <span>{{ __('general.selected') }}</span>
                    @if($tab === 'trashed')
                        @if($canRestore)
                            <button type="submit" form="bulkForm" formaction="{{ route('expense.bulk-restore') }}" class="btn btn-sm btn-outline-success btn-custom">
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
                            <button type="button" class="btn btn-sm btn-outline-danger btn-custom" @click="confirmBulkDelete('expense')">
                                <i class="bi bi-trashms-1"></i>{{ __('general.delete') }}
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
                                <th>{{ __('expense.type') }}</th>
                                <th class="text-end">{{ __('general.amount') }}</th>
                                @if($hasActions)
                                <th class="text-center" style="width:80px">{{ __('general.actions') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $expense)
                                <tr @if($tab === 'trashed') style="opacity:0.7" @endif>
                                    <td><input type="checkbox" name="ids[]" value="{{ $expense->id }}" class="select-item" form="bulkForm"></td>
                                    <td style="white-space:nowrap">{{ $expense->date->format('Y/m/d') }}</td>
                                    <td>
                                        <span>{{ $expense->description ?: '—' }}</span>
                                        @if($expense->is_recurring)
                                            <span class="badge badge-custom badge-expense ms-1">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="display:inline-flex; align-items:center; gap:6px">
                                            <i class="{{ $expense->category?->icon ?? 'bi-tag' }}" style="color:{{ $expense->category?->color ?? '#64748B' }}"></i>
                                            {{ locale_name($expense->category ?? new stdClass) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $type = $expense->is_recurring ? 'periodic' : ($expense->category?->type ?? 'variable');
                                            $typeLabels = ['fixed' => __('expense.fixed'), 'variable' => __('expense.variable'), 'periodic' => __('expense.periodic')];
                                        @endphp
                                        <span class="badge badge-custom badge-expense">{{ $typeLabels[$type] ?? $type }}</span>
                                    </td>
                                    <td text-start fw-bold style="color:var(--danger)">-{{ number_format($expense->amount, 2) }}</td>
                                    @if($hasActions)
                                    <td class="text-center">
                                        @if($tab === 'trashed')
                                            @if($canRestore)
                                                <form action="{{ route('expense.restore', $expense) }}" method="POST" style="display:inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="action-btn" title="{{ __('general.restore') }}" style="color:var(--success)">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($canForceDelete)
                                                <button type="button" class="action-btn" title="{{ __('general.force_delete') }}" style="color:var(--danger)" @click="confirmForceDelete('expense', {{ $expense->id }})">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            @endif
                                        @else
                                            <div class="action-group justify-content-center">
                                                @if($canUpdate)
                                                    <a href="{{ route('expense.edit', $expense) }}" class="action-btn" title="{{ __('general.edit') }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif
                                                @if($canDelete)
                                                    <button type="button" class="action-btn" title="{{ __('general.delete') }}" @click="confirmDelete('expense', {{ $expense->id }})">
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
                    <x-pagination-info :items="$expenses" />
                    <div>
                        {{ $expenses->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            @else
                <x-empty-state
                    icon="bi-cart"
                    :title="__('general.no_data')"
                    :message="$tab === 'trashed' ? __('messages.no_trashed') : __('messages.no_results')"
                    :action="$tab === 'trashed' ? route('expense.index') : ($canCreate ? route('expense.create') : '#')"
                    :actionText="$tab === 'trashed' ? __('general.back') : __('expense.add')"
                />
            @endif
        </div>
    </div>

    @if($canImport)
        <x-import-modal entity="expense" :entity-label="__('expense.title')" />
    @endif
</x-app-layout>
