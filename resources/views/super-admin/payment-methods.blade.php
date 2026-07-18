{{-- resources\views\super-admin\payment-methods.blade.php --}}
<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.payment_methods') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.payment_methods') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.payment_methods_desc') }}</x-slot>

    <div x-data="{ tab: 'methods' }">
        <div class="d-flex gap-2 mb-4 border-bottom pb-2">
            <button @click="tab = 'methods'" :class="{ 'active-tab': tab === 'methods' }" class="btn btn-sm px-3 d-flex gap-2" style="border:none;background:none;font-weight:500;color:var(--text-muted);transition:all 0.15s">
                <i class="bi bi-credit-card-2-front me-1"></i>{{ __('super-admin.payment_methods') }}
            </button>
            <button @click="tab = 'gateways'" :class="{ 'active-tab': tab === 'gateways' }" class="btn btn-sm px-3 d-flex gap-2" style="border:none;background:none;font-weight:500;color:var(--text-muted);transition:all 0.15s">
                <i class="bi bi-diagram-3 me-1"></i>{{ __('super-admin.gateway_structures') }}
            </button>
        </div>

        <div x-show="tab === 'methods'" x-transition:enter.duration.200ms>
            <div class="data-grid">
                <div class="data-grid-toolbar">
                    <div class="data-grid-toolbar-left">
                        <form method="GET" action="{{ route('super.admin.payment-methods.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                            <x-search-filter name="search" placeholder="{{ __('super-admin.search_payment_method') }}..." value="{{ request('search') }}" />

                            <x-select-filter name="type" :options="[
                                'online' => __('super-admin.online'),
                                'manual' => __('super-admin.manual'),
                                'auto_complete' => __('super-admin.auto_complete'),
                            ]" placeholder="{{ __('general.all_types') }}" min-width="130px" />
                            <x-select-filter name="status" :options="[
                                'active' => __('general.active'),
                                'inactive' => __('general.inactive'),
                            ]" placeholder="{{ __('general.all_status') }}" min-width="110px" />
                            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
                            <x-clear-filters :filters="['search','type','status']" :route="route('super.admin.payment-methods.index')" />
                        </form>
                    </div>
                    <div class="data-grid-toolbar-right">
                        <div class="d-flex align-items-center gap-2">
                            <x-per-page :current="(int) request('per_page', 15)" :route="route('super.admin.payment-methods.index')" :preserve="['search','type','status']" :options="[10, 15, 25, 50]" />
                            <a href="{{ route('super.admin.payment-methods.create') }}" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                                <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_payment_method') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="data-grid-body">
                    @if($paymentMethods->count())
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('super-admin.payment_method_icon') }}</th>
                                    <th>{{ __('super-admin.payment_method_key') }}</th>
                                    <th>{{ __('super-admin.payment_method_name') }}</th>
                                    <th>{{ __('super-admin.payment_method_type') }}</th>
                                    <th>{{ __('general.order') }}</th>
                                    <th>{{ __('super-admin.public') }}</th>
                                    <th>{{ __('general.status') }}</th>
                                    <th class="col-actions"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentMethods as $method)
                                    <tr>
                                        <td>
                                            <span style="width:34px;height:34px;border-radius:8px;background:var(--bg-subtle);display:flex;align-items:center;justify-content:center;font-size:16px">
                                                <i class="bi {{ $method->icon ?: 'bi-credit-card' }}"></i>
                                            </span>
                                        </td>
                                        <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px">{{ $method->key }}</code></td>
                                        <td>
                                            <span style="font-weight:500">{{ $method->name }}</span>
                                            @if($method->description)
                                                <div style="font-size:12px;color:var(--text-muted)">{{ $method->description }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($method->isOnline())
                                                <span class="badge" style="font-size:10px;background:var(--info-light);color:var(--info);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('super-admin.online') }}</span>
                                            @elseif($method->isManual())
                                                <span class="badge" style="font-size:10px;background:var(--warning-light);color:var(--warning);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('super-admin.manual') }}</span>
                                            @else
                                                <span class="badge" style="font-size:10px;background:var(--accent-light);color:var(--accent);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('super-admin.auto_complete') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $method->sort_order }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('super.admin.payment-methods.toggle-public', $method) }}" style="display:inline">
                                                @csrf
                                                <x-toggle-switch name="is_public" :checked="$method->is_public" standalone="true" />
                                            </form>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('super.admin.payment-methods.toggle-status', $method) }}" style="display:inline">
                                                @csrf
                                                <x-toggle-switch name="is_active" :checked="$method->is_active" standalone="true" />
                                            </form>
                                        </td>
                                        <td class="col-actions">
                                            <div class="cell-actions">
                                                <a href="{{ route('super.admin.payment-methods.edit', $method) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('super.admin.payment-methods.destroy', $method) }}" id="delete-payment-method-{{ $method->id }}" style="display:none">
                                                    @csrf @method('DELETE')
                                                </form>
                                                <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __('general.delete') }}" @click="confirmDeletePaymentMethod({{ $method->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="data-grid-footer">
                            <span>{{ __('general.showing') }} {{ $paymentMethods->firstItem() }}–{{ $paymentMethods->lastItem() }} {{ __('general.of') }} {{ $paymentMethods->total() }}</span>
                            <div>{{ $paymentMethods->appends(request()->except('page'))->links() }}</div>
                        </div>
                    @else
                        <x-empty-state icon="bi bi-credit-card-2-front" :title="__('general.no_data')" />
                    @endif
                </div>
            </div>
        </div>

        <div x-show="tab === 'gateways'" x-cloak x-transition:enter.duration.200ms>
            <div class="data-grid">
                <div class="data-grid-toolbar">
                    <div class="data-grid-toolbar-left"></div>
                    <div class="data-grid-toolbar-right">
                        <a href="{{ route('super.admin.gateways.create') }}" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                            <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_gateway_structure') }}
                        </a>
                    </div>
                </div>

                <div class="data-grid-body">
                    @if($gateways->count())
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('super-admin.payment_method_icon') }}</th>
                                    <th>{{ __('super-admin.payment_method_key') }}</th>
                                    <th>{{ __('super-admin.payment_method_name') }}</th>
                                    <th>{{ __('super-admin.category') }}</th>
                                    <th>{{ __('super-admin.field_count') }}</th>
                                    <th>{{ __('general.order') }}</th>
                                    <th class="col-actions"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gateways as $gateway)
                                    <tr>
                                        <td>
                                            <span style="width:34px;height:34px;border-radius:8px;background:var(--bg-subtle);display:flex;align-items:center;justify-content:center;font-size:16px">
                                                <i class="bi {{ $gateway->icon ?: 'bi-diagram-3' }}"></i>
                                            </span>
                                        </td>
                                        <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px">{{ $gateway->key }}</code></td>
                                        <td>
                                            <span style="font-weight:500">{{ $gateway->name }}</span>
                                            @if($gateway->description)
                                                <div style="font-size:12px;color:var(--text-muted)">{{ $gateway->description }}</div>
                                            @endif
                                        </td>
                                        <td><span class="badge" style="font-size:10px;background:var(--info-light);color:var(--info);padding:3px 10px;border-radius:6px;font-weight:600">{{ $gateway->category }}</span></td>
                                        <td>{{ count($gateway->fields ?? []) }}</td>
                                        <td>{{ $gateway->sort_order }}</td>
                                        <td class="col-actions">
                                            <div class="cell-actions">
                                                <a href="{{ route('super.admin.gateways.edit', $gateway) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('super.admin.gateways.destroy', $gateway) }}" id="delete-gateway-{{ $gateway->id }}" style="display:none">
                                                    @csrf @method('DELETE')
                                                </form>
                                                <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __('general.delete') }}" @click="confirmDeleteGateway({{ $gateway->id }}, '{{ $gateway->key }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <x-empty-state icon="bi bi-diagram-3" :title="__('general.no_data')" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
    .active-tab {
        color: var(--accent) !important;
        border-bottom: 2px solid var(--accent) !important;
        border-radius: 0 !important;
    }
    </style>

    @push('scripts')
    <script>
    function confirmDeletePaymentMethod(id) {
        const form = document.getElementById('delete-payment-method-' + id);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('super-admin.confirm_delete_payment_method') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }

    function confirmDeleteGateway(id, key) {
        const form = document.getElementById('delete-gateway-' + id);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('super-admin.confirm_delete_gateway') }}' + ' (' + key + ')',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }
    </script>
    @endpush
</x-super-admin-layout>
