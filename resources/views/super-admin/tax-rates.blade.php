<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.tax_rates') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.tax_rates') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.tax_rates_desc') }}</x-slot>

    <div class="data-grid" x-data>
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.tax-rates.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('super-admin.search_tax_rate') }}..." value="{{ request('search') }}" min-width="180px" />
                    <x-select-filter name="status" :options="[
                        'active' => __('general.active'),
                        'inactive' => __('general.inactive'),
                    ]" placeholder="{{ __('general.all_status') }}" min-width="110px" />
                    <x-clear-filters :filters="['search','status']" :route="route('super.admin.tax-rates.index')" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <div class="d-flex align-items-center gap-2">
                    <x-per-page :current="(int) request('per_page', 20)" :route="route('super.admin.tax-rates.index')" :preserve="['search','status']" :options="[10, 20, 30, 50]" />
                    <a href="{{ route('super.admin.tax-rates.create') }}" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                        <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_tax_rate') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="data-grid-body">
            @if($taxRates->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('super-admin.tax_rate_name') }}</th>
                            <th>{{ __('super-admin.tax_rate_slug') }}</th>
                            <th>{{ __('super-admin.tax_rate_value') }}</th>
                            <th>{{ __('super-admin.tax_rate_type') }}</th>
                            <th>{{ __('super-admin.tax_rate_country') }}</th>
                            <th>{{ __('super-admin.tax_rate_region') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($taxRates as $taxRate)
                            <tr>
                                <td>{{ $taxRate->name }}</td>
                                <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px">{{ $taxRate->slug ?? '—' }}</code></td>
                                <td><strong>{{ $taxRate->type === 'percentage' ? $taxRate->rate . '%' : config('finance.currency_symbol') . ' ' . number_format($taxRate->rate, 2) }}</strong></td>
                                <td><span style="font-size:12px;color:var(--text-secondary)">{{ $taxRate->type === 'percentage' ? __('general.percentage') : __('general.fixed') }}</span></td>
                                <td class="cell-muted">{{ $taxRate->country ? strtoupper($taxRate->country) : '—' }}</td>
                                <td class="cell-muted">{{ $taxRate->region ?? '—' }}</td>
                                <td>
                                    <x-status-badge domain="general" :status="$taxRate->is_active ? 'active' : 'inactive'" set="bi" />
                                </td>
                                <td class="col-actions">
                                    <div class="cell-actions">
                                        <a href="{{ route('super.admin.tax-rates.edit', $taxRate) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('super.admin.tax-rates.destroy', $taxRate) }}" id="delete-tax-rate-{{ $taxRate->id }}" style="display:none">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __('general.delete') }}" @click="confirmDeleteTaxRate({{ $taxRate->id }})">
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
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-percent"></i></div>
                    <h4>{{ __('general.no_data') }}</h4>
                    <p>{{ __('super-admin.no_tax_rates') }}</p>
                </div>
            @endif
        </div>

        @if($taxRates->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$taxRates" />
                <div>{{ $taxRates->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
    function confirmDeleteTaxRate(id) {
        const form = document.getElementById('delete-tax-rate-' + id);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('super-admin.confirm_delete_tax_rate') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }
    </script>
    @endpush
</x-super-admin-layout>