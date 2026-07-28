<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.invoices') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.invoices') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.invoices_desc') }}</x-slot>

    <x-filter-tabs :tabs="[
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-list-ul'],
        'paid' => ['label' => __('general.paid'), 'count' => $countPaid, 'icon' => 'bi-check-circle'],
        'overdue' => ['label' => __('general.overdue'), 'count' => $countOverdue, 'icon' => 'bi-exclamation-triangle'],
        'draft' => ['label' => __('general.draft'), 'count' => $countDraft, 'icon' => 'bi-pencil'],
        'cancelled' => ['label' => __('general.cancelled'), 'count' => $countCancelled, 'icon' => 'bi-x-circle'],
    ]" current="{{ request('status', 'all') }}" keyParam="status" />

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.invoices.index') }}"
                    class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('super-admin.search_invoice') }}..."
                        value="{{ request('search') }}" />
                    <input type="date" name="date_from" class="form-control grid-filter-sm"
                        style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)"
                        value="{{ request('date_from') }}">
                    <input type="date" name="date_to" class="form-control grid-filter-sm"
                        style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)"
                        value="{{ request('date_to') }}">
                    <x-button variant="accent" submit>{{ __('general.filter') }}</x-button>
                    <x-clear-filters :filters="['search', 'status', 'date_from', 'date_to']" :route="route('super.admin.invoices.index')" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <x-per-page :current="(int) request('per_page', 15)" :route="route('super.admin.invoices.index')" :preserve="['search', 'status', 'date_from', 'date_to']" :options="[10, 15, 25, 50]" />
            </div>
        </div>

        <div class="data-grid-body">
            @if ($invoices->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('super-admin.invoice_number') }}</th>
                            <th>{{ __('super-admin.invoice_workspace') }}</th>
                            <th>{{ __('super-admin.invoice_plan') }}</th>
                            <th>{{ __('super-admin.invoice_amount') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th>{{ __('general.date') }}</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td><code
                                        style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px">{{ $invoice->number }}</code>
                                </td>
                                <td>{{ $invoice->workspace?->name ?? '—' }}</td>
                                <td>{{ $invoice->subscription?->plan?->name ?? '—' }}</td>
                                <td><strong>{{ number_format($invoice->total, 2) }}
                                        {{ $invoice->currency ?? config('finance.currency_symbol') }}</strong></td>
                                <td>
                                        <x-status-badge domain="invoice" :status="$invoice->status->value" set="bi"
                                            class="text-lg " />

                                </td>
                                <td class="cell-muted">{{ $invoice->created_at->format('Y/m/d') }}</td>
                                <td class="col-actions">
                                    <x-button href="{{ route('super.admin.invoices.show', $invoice) }}" icon="bi bi-eye" title="{{ __('general.view') }}" style="width:30px;height:30px;padding:0;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <x-empty-state icon="bi bi-receipt" :title="__('general.no_data')" :description="__('super-admin.no_invoices')" />
            @endif
        </div>

        @if ($invoices->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$invoices" />
                <div>{{ $invoices->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>
</x-super-admin-layout>
