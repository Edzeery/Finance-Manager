<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.coupons') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.coupons') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.coupons_desc') }}</x-slot>

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.coupons.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('super-admin.search_coupon') }}..." value="{{ request('search') }}" min-width="180px" />
                    <x-select-filter name="status" :options="[
                        'active' => __('general.active'),
                        'inactive' => __('general.inactive'),
                    ]" placeholder="{{ __('general.all_status') }}" min-width="120px" />
                    <x-clear-filters :filters="['search','status']" :route="route('super.admin.coupons.index')" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <div class="d-flex align-items-center gap-2">
                    <x-per-page :current="(int) request('per_page', 15)" :route="route('super.admin.coupons.index')" :preserve="['search','status']" :options="[10, 15, 25, 50]" />
                    <x-button href="{{ route('super.admin.coupons.create') }}" variant="accent" icon="bi bi-plus-lg">{{ __('super-admin.create_coupon') }}</x-button>
                </div>
            </div>
        </div>

        <div class="data-grid-body">
            @if($coupons->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('super-admin.coupon_code') }}</th>
                            <th>{{ __('super-admin.coupon_type') }}</th>
                            <th>{{ __('super-admin.coupon_value') }}</th>
                            <th>{{ __('super-admin.coupon_uses') }}</th>
                            <th>{{ __('super-admin.coupon_expires') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                            <tr>
                                <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px;font-weight:600">{{ $coupon->code }}</code></td>
                                <td><span style="font-size:12px;color:var(--text-secondary)">{{ $coupon->type === 'percentage' ? __('general.percentage') : __('general.fixed') }}</span></td>
                                <td><strong>{{ $coupon->type === 'percentage' ? $coupon->value . '%' : config('finance.currency_symbol') . ' ' . number_format($coupon->value, 2) }}</strong></td>
                                <td><span style="font-size:13px">{{ $coupon->used_count }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : '' }}</span></td>
                                <td class="cell-muted">{{ $coupon->expires_at ? $coupon->expires_at->format('Y/m/d') : '—' }}</td>
                                <td>
                                    <x-status-badge domain="general" :status="$coupon->isValid() ? 'active' : 'inactive'" set="bi" />
                                </td>
                                <td class="col-actions">
                                    <div class="cell-actions">
                                        <x-button href="{{ route('super.admin.coupons.edit', $coupon) }}" icon="bi bi-pencil" title="{{ __('general.edit') }}" style="width:30px;height:30px;padding:0;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" />
                                        <form method="POST" action="{{ route('super.admin.coupons.destroy', $coupon) }}" id="delete-coupon-{{ $coupon->id }}" style="display:none">
                                            @csrf @method('DELETE')
                                        </form>
                                        <x-button icon="bi bi-trash" title="{{ __('general.delete') }}" @click="confirmDeleteCoupon({{ $coupon->id }})" style="width:30px;height:30px;padding:0;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <x-empty-state icon="bi bi-ticket-perforated" :title="__('general.no_data')" :description="__('super-admin.no_coupons')" />
            @endif
        </div>

        @if($coupons->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$coupons" />
                <div>{{ $coupons->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
    function confirmDeleteCoupon(id) {
        const form = document.getElementById('delete-coupon-' + id);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('super-admin.confirm_delete_coupon') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }
    </script>
    @endpush
</x-super-admin-layout>
