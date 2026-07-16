<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.plans') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.plans') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.plans_desc') }}</x-slot>

    <x-filter-tabs :tabs="[
        'plans' => ['label' => __('super-admin.plans'), 'icon' => 'bi-box'],
        'features' => ['label' => __('super-admin.features'), 'icon' => 'bi-list-check'],
        'prices' => ['label' => __('super-admin.prices'), 'icon' => 'bi-currency-dollar'],
    ]" :current="$tab ?? 'plans'" :preserve="['search','status','per_page','features_search','features_type','features_core','features_per_page','price_plan_id','page','features_page']" keyParam="tab" />

    {{-- ============================== TAB: PLANS ============================== --}}
    @if(($tab ?? 'plans') === 'plans')
    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.plans.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <input type="hidden" name="tab" value="plans">
                    <x-search-filter name="search" placeholder="{{ __('general.search') }}..." value="{{ request('search') }}" />
                    <x-select-filter name="status" :options="[
                        'active' => __('general.active'),
                        'inactive' => __('general.inactive'),
                    ]" placeholder="{{ __('general.all_status') }}" min-width="110px" />
                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
                    <x-clear-filters :filters="['search','status']" :route="route('super.admin.plans.index', ['tab' => 'plans'])" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <div class="d-flex align-items-center gap-2">
                    <x-per-page :current="(int) request('per_page', 20)" :route="route('super.admin.plans.index')" :preserve="['search','status','tab']" :options="[10, 20, 30, 50]" />
                    <a href="{{ route('super.admin.plans.create') }}" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                        <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_plan') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="data-grid-body">
            @if($plans->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>{{ __('general.name') }}</th>
                            <th>{{ __('super-admin.plan_slug') }}</th>
                            <th>{{ __('super-admin.plan_price') }}</th>
                            <th>{{ __('super-admin.features') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th style="width:80px">{{ __('super-admin.plan_order') }}</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                            <tr>
                                <td style="color:var(--text-muted);font-size:12px">{{ $plan->sort_order }}</td>
                                <td>
                                    <strong>{{ $plan->name }}</strong>
                                    @if($plan->is_free)
                                        <span class="badge" style="font-size:9px;background:var(--info-light);color:var(--info);padding:2px 8px;border-radius:4px;font-weight:600;margin-inline-start:6px">{{ __('super-admin.free') }}</span>
                                    @endif
                                    @if(!$plan->is_public)
                                        <span class="badge" style="font-size:9px;background:var(--warning-light);color:var(--warning);padding:2px 8px;border-radius:4px;font-weight:600;margin-inline-start:6px">{{ __('super-admin.hidden') }}</span>
                                    @endif
                                </td>
                                <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px">{{ $plan->slug }}</code></td>
                                <td>
                                    @if($plan->is_free)
                                        <span style="color:var(--text-muted)">{{ __('super-admin.free') }}</span>
                                    @else
                                        <strong>{{ number_format($plan->monthly_price, 2) }}/mo</strong>
                                        @if($plan->yearly_price > 0)
                                            <div class="cell-muted">{{ number_format($plan->yearly_price, 2) }}/yr</div>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size:12px;color:var(--text-muted)">
                                        {{ $plan->plan_features_count ?? 0 }} {{ __('super-admin.features_lc') }}
                                        @if($plan->active_prices_count ?? false)
                                            &middot; {{ $plan->active_prices_count }} {{ __('super-admin.prices_lc') }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <x-status-badge domain="general" :status="$plan->is_active ? 'active' : 'inactive'" set="bi" />
                                </td>
                                <td style="font-size:13px;color:var(--text-muted)">{{ $plan->sort_order }}</td>
                                <td class="col-actions">
                                    <div class="cell-actions">
                                        <a href="{{ route('super.admin.plans.edit', $plan) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('super.admin.plans.destroy', $plan) }}" id="delete-plan-{{ $plan->id }}" style="display:none">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __('general.delete') }}" @click="confirmDeletePlan({{ $plan->id }})">
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
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-currency-dollar"></i></div>
                    <h4>{{ __('general.no_data') }}</h4>
                    <p>{{ __('super-admin.no_plans') }}</p>
                </div>
            @endif
        </div>

        @if($plans->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$plans" />
                <div>{{ $plans->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>

    {{-- ============================== TAB: FEATURES ============================== --}}
    @elseif(($tab ?? 'plans') === 'features')
    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.plans.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <input type="hidden" name="tab" value="features">
                    <x-search-filter name="features_search" placeholder="{{ __('general.search') }}..." value="{{ request('features_search') }}" />
                    <x-select-filter name="features_type" :options="[
                        'boolean' => 'Boolean',
                        'value' => 'Value',
                        'text' => 'Text',
                    ]" placeholder="{{ __('super-admin.all_types') }}" min-width="100px" />
                    <x-select-filter name="features_core" :options="[
                        'true' => __('super-admin.core'),
                        'false' => __('super-admin.addon'),
                    ]" placeholder="{{ __('super-admin.all_features') }}" min-width="90px" />
                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
                    <x-clear-filters :filters="['features_search','features_type','features_core']" :route="route('super.admin.plans.index', ['tab' => 'features'])" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <div class="d-flex align-items-center gap-2">
                    <x-per-page :current="(int) request('features_per_page', 20)" :route="route('super.admin.plans.index')" :preserve="['tab','features_search','features_type','features_core']" name="features_per_page" page-name="features_page" :options="[10, 20, 30, 50]" />
                    <a href="{{ route('super.admin.features.create') }}" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                        <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_feature') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="data-grid-body">
            @if($features->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>{{ __('super-admin.feature_name_en') }}</th>
                            <th>{{ __('super-admin.feature_name_ar') }}</th>
                            <th>{{ __('super-admin.feature_slug') }}</th>
                            <th>{{ __('super-admin.feature_type') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($features as $feature)
                            <tr>
                                <td style="color:var(--text-muted);font-size:12px">{{ $feature->sort_order }}</td>
                                <td>
                                    <strong>{{ $feature->name_en }}</strong>
                                    @if($feature->is_core)
                                        <span class="badge" style="font-size:9px;background:var(--info-light);color:var(--info);padding:2px 8px;border-radius:4px;font-weight:600;margin-inline-start:6px">{{ __('super-admin.core') }}</span>
                                    @endif
                                </td>
                                <td style="font-size:13px;color:var(--text-secondary)">{{ $feature->name_ar ?: '—' }}</td>
                                <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px">{{ $feature->slug }}</code></td>
                                <td><span style="font-size:12px;color:var(--text-secondary)">{{ $feature->type }}</span></td>
                                <td>
                                    <x-status-badge domain="general" :status="$feature->is_core ? 'yes' : 'no'" set="bi" />
                                </td>
                                <td class="col-actions">
                                    <div class="cell-actions">
                                        <a href="{{ route('super.admin.features.edit', $feature) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('super.admin.features.destroy', $feature) }}" id="delete-feature-{{ $feature->id }}" style="display:none">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __('general.delete') }}" @click="confirmDeleteFeature({{ $feature->id }})">
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
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-list-check"></i></div>
                    <h4>{{ __('general.no_data') }}</h4>
                    <p>{{ __('super-admin.no_features') }}</p>
                </div>
            @endif
        </div>

        @if($features->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$features" />
                <div>{{ $features->appends(request()->except('features_page'))->links('pagination::bootstrap-5', ['pageName' => 'features_page']) }}</div>
            </div>
        @endif
    </div>

    {{-- ============================== TAB: PRICES ============================== --}}
    @elseif(($tab ?? 'plans') === 'prices')
    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.plans.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <input type="hidden" name="tab" value="prices">
                    <div style="min-width:200px">
                        @php $pricePlanOptions = $allPlansForPrices->mapWithKeys(fn($p) => [$p->id => $p->name . ' (' . $p->slug . ')'])->toArray(); @endphp
                        <x-select-filter name="price_plan_id" :options="$pricePlanOptions" placeholder="{{ __('super-admin.select_plan') }}" min-width="200px" :selected="$selectedPlan?->id" onchange="this.form.submit()" />
                    </div>
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                @if($selectedPlan)
                    <a href="{{ route('super.admin.plans.prices.create', $selectedPlan) }}" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                        <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_price') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="data-grid-body">
            @if($selectedPlan && $prices->count())
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
                                        <a href="{{ route('super.admin.plans.prices.edit', [$selectedPlan, $price]) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('super.admin.plans.prices.destroy', [$selectedPlan, $price]) }}" id="delete-price-{{ $price->id }}" style="display:none">
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
            @elseif($selectedPlan && !$prices->count())
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-currency-dollar"></i></div>
                    <h4>{{ __('general.no_data') }}</h4>
                    <p>{{ __('super-admin.no_prices_for_plan') }}</p>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-currency-dollar"></i></div>
                    <h4>{{ __('super-admin.select_plan') }}</h4>
                    <p>{{ __('super-admin.prices_select_plan_hint') }}</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
    function confirmDeletePlan(id) {
        const form = document.getElementById('delete-plan-' + id);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('super-admin.confirm_delete_plan') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }
    function confirmDeleteFeature(id) {
        const form = document.getElementById('delete-feature-' + id);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('super-admin.confirm_delete_feature') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }
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
