@php
    $perm = fn($p) => auth()->user()->hasPermission("debt.$p");
    $canCreate = $perm('create');
    $canUpdate = $perm('update');
    $canDelete = $perm('delete');
    $canRestore = $perm('restore');
    $canForceDelete = $perm('force-delete');
    $canExport = $perm('export');
    $hasActions = $canUpdate || $canDelete || $canRestore || $canForceDelete;
@endphp

<x-app-layout>
    <x-slot:title>{{ __('debt.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('debt.title') }}</x-slot>
    <x-slot:page-description>
        <span style="color:var(--danger)">{{ __('debt.total_debts_owed') }}: <strong>{{ number_format($totalOwed - $paidOwed, 2) }} {{ config('finance.currency_symbol') }}</strong></span>
        &nbsp;|&nbsp;
        <span style="color:var(--success)">{{ __('debt.total_debts_owing') }}: <strong>{{ number_format($totalOwing - $paidOwing, 2) }} {{ config('finance.currency_symbol') }}</strong></span>
    </x-slot>

    @php $showSubTabs = $tab !== 'trashed'; @endphp

    <x-filter-tabs :tabs="$tabs" current="{{ $tab }}" defaultKey="all"
        subParam="type"
        subCurrent="{{ request('type', '') }}"
        :subTabs="$showSubTabs ? $typeSubTabs : []"
        :preserve="['status','search','per_page']" />

    @if($tab !== 'trashed')
        @php
            $statusTabs = [
                '' => ['label' => __('general.all')],
                \App\Enums\DebtStatus::Active->value => ['label' => __('debt.active')],
                \App\Enums\DebtStatus::Partial->value => ['label' => __('debt.partial')],
                \App\Enums\DebtStatus::Paid->value => ['label' => __('debt.paid')],
                \App\Enums\DebtStatus::Overdue->value => ['label' => __('debt.overdue')],
            ];
        @endphp
        <x-filter-tabs :tabs="$statusTabs" current="{{ request('status', '') }}" keyParam="status" defaultKey="" :preserve="['type','search','per_page','tab']" />
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form method="GET" action="{{ route('debt.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            @if (request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif

            <x-search-filter name="search" :value="request('search')" size="sm" />

            <x-clear-filters :filters="['type','status','search']" :route="route('debt.index')" />
        </form>

        <div class="d-flex gap-2 align-items-center">
            <x-data-toolbar entity="debt" :show-export="$canExport" />
            <x-per-page :current="request('per_page', 15)" :route="route('debt.index')" :preserve="['type','status','search','tab']" />
            @if($tab !== 'trashed' && $canCreate)
                <x-button href="{{ route('debt.create') }}" icon="bi bi-plus-lg">{{ __('debt.add') }}</x-button>
            @endif
        </div>
    </div>

    <form id="bulkForm" method="POST"
          data-bulk-delete-route="{{ route('debt.bulk-delete') }}"
          data-bulk-force-delete-route="{{ route('debt.bulk-force-delete') }}">
        @csrf
    </form>

    @foreach($debts as $debt)
        <form id="delete-form-debt-{{ $debt->id }}" action="{{ route('debt.destroy', $debt) }}" method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
        <form id="force-delete-form-debt-{{ $debt->id }}" action="{{ route('debt.force-delete', $debt->id) }}" method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
    @endforeach

    <div class="card-custom">
        <div class="card-body p-0">
            <div id="bulkBar" class="bulk-bar" style="display:none; margin:12px">
                <span id="selectedCount">0</span> <span>{{ __('general.selected') }}</span>
                @if($tab === 'trashed')
                    @if($canRestore)
                        <x-button submit form="bulkForm" formaction="{{ route('debt.bulk-restore') }}" size="sm" variant="outline-success" icon="bi bi-arrow-counterclockwise">{{ __('general.restore') }}</x-button>
                    @endif
                    @if($canForceDelete)
                        <x-button size="sm" variant="outline-danger" @click="confirmBulkForceDelete()" icon="bi bi-trash3">{{ __('general.force_delete') }}</x-button>
                    @endif
                @else
                    @if($canDelete)
                        <x-button size="sm" variant="outline-danger" @click="confirmBulkDelete('debt')" icon="bi bi-trash">{{ __('general.delete') }}</x-button>
                    @endif
                @endif
            </div>
            @if($debts->count())
                <div class="table-responsive">
                    <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width:40px"><input type="checkbox" id="selectAll" @change="toggleSelectAll($el)"></th>
                            <th>{{ __('debt.type') }}</th>
                            <th>{{ __('debt.counterparty') }}</th>
                            <th class="text-end">{{ __('debt.total_amount') }}</th>
                            <th class="text-end">{{ __('debt.remaining_amount') }}</th>
                            <th>{{ __('debt.due_date') }}</th>
                            <th>{{ __('general.status') }}</th>
                            @if($hasActions)
                            <th class="text-center" style="width:80px">{{ __('general.actions') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($debts as $debt)
                            <tr @if($tab === 'trashed') style="opacity:0.7" @else class="{{ $debt->is_overdue ? 'bg-overdue' : '' }}" @endif>
                                <td><input type="checkbox" name="ids[]" value="{{ $debt->id }}" class="select-item" form="bulkForm"></td>
                                <td>
                                    <x-status-badge domain="debt_type" :status="$debt->type->value" set="bi" />
                                </td>
                                <td>
                                    <a href="{{ route('debt.show', $debt) }}" class="text-decoration-none" style="color:var(--text); font-weight:500">
                                        {{ $debt->counterparty_name }}
                                    </a>
                                </td>
                                <td class="text-start">{{ number_format($debt->total_amount, 2) }}</td>
                                <td class="class="text-start fw-bold" {{ $debt->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($debt->remaining_amount, 2) }}
                                </td>
                                <td>
                                    @if($debt->due_date)
                                        <span style="color:{{ $debt->is_overdue ? 'var(--danger)' : 'var(--text)' }}">
                                            {{ $debt->due_date->format('Y/m/d') }}
                                            @if($debt->is_overdue)
                                                <x-status-icon domain="general" status="danger" set="bi" class="ms-1" title="{{ __('debt.overdue') }}" />
                                            @endif
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <x-status-badge domain="debt" :status="$debt->status->value" set="bi" class="text-sm" />
                                </td>
                                @if($hasActions)
                                <td class="text-center">
                                    @if($tab === 'trashed')
                                        @if($canRestore)
                                            <form action="{{ route('debt.restore', $debt) }}" method="POST" style="display:inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="action-btn" title="{{ __('general.restore') }}" style="color:var(--success)">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($canForceDelete)
                                            <button type="button" class="action-btn" title="{{ __('general.force_delete') }}" style="color:var(--danger)" @click="confirmForceDelete('debt', {{ $debt->id }})">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        @endif
                                    @else
                                        <div class="action-group justify-content-center">
                                            <a href="{{ route('debt.show', $debt) }}" class="action-btn" title="{{ __('general.view') }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($canUpdate)
                                                <a href="{{ route('debt.edit', $debt) }}" class="action-btn" title="{{ __('general.edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                            @if($canDelete)
                                                <button type="button" class="action-btn" title="{{ __('general.delete') }}" @click="confirmDelete('debt', {{ $debt->id }})">
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
                    <x-pagination-info :items="$debts" />
                    <div>
                        {{ $debts->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            @else
                <x-empty-state
                    icon="bi-credit-card-2-front"
                    :title="$tab === 'trashed' ? __('general.no_data') : __('debt.no_debts')"
                    :message="$tab === 'trashed' ? __('messages.no_trashed') : __('debt.create_first_debt')"
                    :action="$tab === 'trashed' ? route('debt.index') : ($canCreate ? route('debt.create') : '#')"
                    :actionText="$tab === 'trashed' ? __('general.back') : __('debt.add')"
                />
            @endif
        </div>
    </div>

</x-app-layout>
