<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.prices') }} - {{ $plan->name }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.prices') }}: {{ $plan->name }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.prices_desc') }}</x-slot>

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <a href="{{ route('super.admin.plans.edit', $plan) }}" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                    <i class="bi bi-arrow-left"></i>{{ __('super-admin.back_to_plan') }}
                </a>
            </div>
            <div class="data-grid-toolbar-right">
                <div class="d-flex align-items-center gap-2">
                    <x-per-page :current="(int) request('per_page', 50)" :route="route('super.admin.plans.prices.index', $plan)" :options="[10, 20, 50, 100]" />
                    <a href="{{ route('super.admin.plans.prices.create', $plan) }}" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                        <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_price') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="data-grid-body">
            @if($prices->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('super-admin.price_period') }}</th>
                            <th>{{ __('super-admin.price_currency') }}</th>
                            <th>{{ __('super-admin.price_amount') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th>{{ __('general.created_at') }}</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prices as $price)
                            <tr>
                                <td>
                                    <span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:3px 10px;border-radius:6px;font-weight:600">
                                        {{ $price->billing_period === 'monthly' ? __('super-admin.monthly') : __('super-admin.yearly') }}
                                    </span>
                                </td>
                                <td><strong>{{ $price->currency }}</strong></td>
                                <td><strong>{{ number_format($price->price, 2) }}</strong></td>
                                <td>
                                    <x-status-badge domain="general" :status="$price->is_active ? 'active' : 'inactive'" set="bi" />
                                </td>
                                <td class="cell-muted">{{ $price->created_at->format('Y/m/d') }}</td>
                                <td class="col-actions">
                                    <div class="cell-actions">
                                        <a href="{{ route('super.admin.plans.prices.edit', [$plan, $price]) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('super.admin.plans.prices.destroy', [$plan, $price]) }}" id="delete-price-{{ $price->id }}" style="display:none">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __('general.delete') }}" @click="confirmDeletePrice({{ $price->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <x-empty-state icon="bi bi-currency-dollar" :title="__('general.no_data')" :description="__('super-admin.no_prices')" />
            @endif
        </div>

        @if($prices->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$prices" />
                <div>{{ $prices->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
    function confirmDeletePrice(id) {
        const form = document.getElementById('delete-price-' + id);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('super-admin.confirm_delete_price') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }
    </script>
    @endpush
</x-super-admin-layout>
