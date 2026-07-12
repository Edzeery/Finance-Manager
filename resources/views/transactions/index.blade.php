<x-app-layout>
    <x-slot:title>{{ __('transactions.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('transactions.title') }}</x-slot>
    <x-slot:page-description>{{ __('transactions.all_transactions') }}</x-slot>

    <x-filter-tabs :tabs="$tabs" current="{{ $tab }}" />

    <div class="card-custom">
        <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" id="filterForm">
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-lg-3 col-md-4 col-12">
                        <x-search-filter name="search" :value="$search ?? request('search')" placeholder="{{ __('transactions.search_placeholder') }}" />
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <input type="date" name="date_from" class="form-custom" style="width:100%;padding:7px 12px;font-size:13px"
                               value="{{ $dateFrom ?? request('date_from') }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <input type="date" name="date_to" class="form-custom" style="width:100%;padding:7px 12px;font-size:13px"
                               value="{{ $dateTo ?? request('date_to') }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-lg-2 col-md-2 col-6">
                        <x-per-page :current="(int) ($perPage ?? request('per_page', 15))" :options="[15, 25, 50, 100]" :preserve="['search','date_from','date_to','sort','direction','tab']" />
                    </div>
                    <div class="col-lg-3 col-md-4 col-12 d-flex gap-2 justify-content-end">
                        @php $canExportTxn = auth()->user()->hasPermission('transaction.export'); @endphp
                        <x-data-toolbar entity="transactions" :show-import="false" :show-export="$canExportTxn" />
                        <x-clear-filters :filters="['search','type','date_from','date_to']" :route="route('transactions.index')" label="{{ __('transactions.clear_filters') }}" />
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ route('transactions.index', array_merge(request()->query(), ['sort' => 'date', 'direction' => $sortField === 'date' && $sortDir === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none" style="color:inherit">
                                    {{ __('transactions.date') }}
                                    @if ($sortField === 'date') <i class="bi bi-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('transactions.index', array_merge(request()->query(), ['sort' => 'type', 'direction' => $sortField === 'type' && $sortDir === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none" style="color:inherit">
                                    {{ __('transactions.type') }}
                                    @if ($sortField === 'type') <i class="bi bi-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('transactions.index', array_merge(request()->query(), ['sort' => 'category', 'direction' => $sortField === 'category' && $sortDir === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none" style="color:inherit">
                                    {{ __('transactions.category') }}
                                    @if ($sortField === 'category') <i class="bi bi-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </a>
                            </th>
                            <th>{{ __('transactions.description') }}</th>
                            <th class="text-end">
                                <a href="{{ route('transactions.index', array_merge(request()->query(), ['sort' => 'amount', 'direction' => $sortField === 'amount' && $sortDir === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none" style="color:inherit">
                                    {{ __('transactions.amount') }}
                                    @if ($sortField === 'amount') <i class="bi bi-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </a>
                            </th>
                            <th class="text-center">{{ __('transactions.status') }}</th>
                            <th class="text-end">{{ __('transactions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                            <tr>
                                <td style="white-space:nowrap">{{ $txn['date']->format('Y/m/d') }}</td>
                                <td>
                                    <span class="badge" style="background:{{ $txn['type'] === 'income' ? 'rgba(34,197,94,0.12)' : 'rgba(239,68,68,0.12)' }}; color:{{ $txn['type'] === 'income' ? 'var(--success)' : 'var(--danger)' }}; padding:4px 10px; border-radius:6px; font-weight:500; font-size:12px">
                                        {{ $txn['type'] === 'income' ? __('transactions.income') : __('transactions.expense') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($txn['category_name'] !== '—')
                                        <span class="d-flex align-items-center gap-1">
                                            <span style="width:8px; height:8px; border-radius:50%; display:inline-block; background:{{ $txn['category_color'] }}; flex-shrink:0"></span>
                                            <span>{{ $txn['category_name'] }}</span>
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap">
                                    {{ $txn['description'] ?: '—' }}
                                </td>
                                <td class="text-end fw-bold" style="color:{{ $txn['type'] === 'income' ? 'var(--success)' : 'var(--danger)' }}">
                                    {{ $txn['type'] === 'income' ? '+' : '-' }}{{ number_format($txn['amount'], 2) }}
                                </td>
                                <td class="text-center">
                                    @if ($txn['is_archived'])
                                        <span class="badge" style="background:rgba(148,163,184,0.12); color:var(--text-muted); padding:4px 8px; border-radius:6px; font-size:11px">{{ __('transactions.archived') }}</span>
                                    @else
                                        <span class="badge" style="background:rgba(34,197,94,0.12); color:var(--success); padding:4px 8px; border-radius:6px; font-size:11px">{{ __('transactions.active') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @php
                                        $canEditTxn = auth()->user()->hasPermission($txn['type'] === 'income' ? 'income.update' : 'expense.update');
                                        $canDeleteTxn = auth()->user()->hasPermission($txn['type'] === 'income' ? 'income.delete' : 'expense.delete');
                                    @endphp
                                    <div class="d-flex gap-1 justify-content-end">
                                        @if($canEditTxn)
                                        <a href="{{ $txn['type'] === 'income' ? route('income.edit', $txn['id']) : route('expense.edit', $txn['id']) }}"
                                           class="action-btn" title="{{ __('transactions.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endif
                                        @if($canDeleteTxn)
                                        <form method="POST" action="{{ $txn['type'] === 'income' ? route('income.destroy', $txn['id']) : route('expense.destroy', $txn['id']) }}" id="delete-txn-{{ $txn['id'] }}" style="display:none">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button" class="action-btn" style="color:var(--danger)"
                                                title="{{ __('transactions.delete') }}" @click="confirmDeleteTxn({{ $txn['id'] }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <x-empty-state
                                        icon="bi bi-inbox"
                                        :title="__('transactions.no_transactions')"
                                        :message="__('transactions.no_transactions_desc')" />
                                    @php $canAddIncome = auth()->user()->hasPermission('income.create'); $canAddExpense = auth()->user()->hasPermission('expense.create'); @endphp
                                    <div class="d-flex gap-2 justify-content-center mt-3">
                                        @if($canAddIncome)
                                        <a href="{{ route('income.create') }}" class="btn btn-accent btn-custom btn-sm">
                                            <i class="bi bi-plus-circle"></i> {{ __('general.add') }} {{ __('transactions.income') }}
                                        </a>
                                        @endif
                                        @if($canAddExpense)
                                        <a href="{{ route('expense.create') }}" class="btn btn-custom btn-sm" style="background:var(--danger); color:#fff; border:none">
                                            <i class="bi bi-plus-circle"></i> {{ __('general.add') }} {{ __('transactions.expense') }}
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3">
                    <x-pagination-info :items="$transactions" />
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    function confirmDeleteTxn(id) {
        const form = document.getElementById('delete-txn-' + id);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('messages.confirm_delete') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }
    </script>
    @endpush
</x-app-layout>
