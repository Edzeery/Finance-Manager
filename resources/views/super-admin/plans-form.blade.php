<x-super-admin-layout>
    <x-slot:title>{{ $plan ? __('super-admin.edit_plan') : __('super-admin.create_plan') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ $plan ? __('super-admin.edit_plan') : __('super-admin.create_plan') }}</x-slot>
    <x-slot:page-description>{{ $plan ? __('super-admin.edit_plan_desc') : __('super-admin.create_plan_desc') }}</x-slot>

    @php
        $currentTab = request('tab', 'details');
    @endphp

    <div class="tabs-wrapper" style="margin-bottom:24px">
        <div class="tabs-header" style="display:flex;gap:0;border-bottom:2px solid var(--border);overflow-x:auto">
            <button type="button" class="tab-btn" data-tab="details"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;border-bottom:2px solid {{ $currentTab === 'details' ? 'var(--accent)' : 'transparent' }};margin-bottom:-2px;transition:all 0.15s;color:{{ $currentTab === 'details' ? 'var(--accent)' : 'var(--text-muted)' }}">
                <i class="bi bi-info-circle"></i>{{ __('super-admin.plan_details') }}
            </button>
            <button type="button" class="tab-btn" data-tab="features"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;border-bottom:2px solid {{ $currentTab === 'features' ? 'var(--accent)' : 'transparent' }};margin-bottom:-2px;transition:all 0.15s;color:{{ $currentTab === 'features' ? 'var(--accent)' : 'var(--text-muted)' }}">
                <i class="bi bi-list-check"></i>{{ __('super-admin.features') }} <span class="badge" style="font-size:9px;background:var(--bg-subtle);color:var(--text-muted);padding:1px 6px;border-radius:4px">{{ $allFeatures->count() }}</span>
            </button>
            @if($plan)
            <button type="button" class="tab-btn" data-tab="prices"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;border-bottom:2px solid {{ $currentTab === 'prices' ? 'var(--accent)' : 'transparent' }};margin-bottom:-2px;transition:all 0.15s;color:{{ $currentTab === 'prices' ? 'var(--accent)' : 'var(--text-muted)' }}">
                <i class="bi bi-currency-dollar"></i>{{ __('super-admin.prices') }} <span class="badge" style="font-size:9px;background:var(--bg-subtle);color:var(--text-muted);padding:1px 6px;border-radius:4px">{{ $prices->count() }}</span>
            </button>
            @endif
        </div>
    </div>

    <div style="max-width:860px">
        <form method="POST" action="{{ $plan ? route('super.admin.plans.update', $plan) : route('super.admin.plans.store') }}" id="planForm">
            @csrf @if($plan) @method('PUT') @endif

            {{-- ======================== TAB: DETAILS ======================== --}}
            <div class="tab-panel" id="panel-details" style="display:{{ $currentTab === 'details' ? 'block' : 'none' }}">
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-info-circle"></i>{{ __('super-admin.plan_information') }}</h5>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('general.name') }}" value="{{ old('name', $plan->name ?? '') }}" maxlength="255" required>
                                    <label>{{ __('general.name') }} <span class="text-danger">*</span></label>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="{{ __('super-admin.plan_slug') }}" value="{{ old('slug', $plan->slug ?? '') }}" maxlength="100" required>
                                    <label>{{ __('super-admin.plan_slug') }} <span class="text-danger">*</span></label>
                                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-floating-group">
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="{{ __('general.description') }}" rows="3" maxlength="1000" style="min-height:70px;padding-top:20px">{{ old('description', $plan->description ?? '') }}</textarea>
                            <label>{{ __('general.description') }}</label>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mt-3 mb-3" style="font-size:13px;background:var(--info-light);color:var(--info);border-radius:var(--radius-sm);padding:10px 14px;display:flex;align-items:center;gap:8px">
                            <i class="bi bi-currency-dollar"></i>
                            {{ __('super-admin.prices_manage_hint') }}
                            @if($plan)
                                <a href="{{ route('super.admin.plans.edit', [$plan, 'tab' => 'prices']) }}" style="margin-inline-start:auto;font-weight:600;color:var(--accent)">{{ __('super-admin.plan_prices') }} &rarr;</a>
                            @endif
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating-group">
                                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="{{ __('super-admin.plan_order') }}" value="{{ old('sort_order', $plan->sort_order ?? '') }}" min="0">
                                    <label>{{ __('super-admin.plan_order') }}</label>
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="number" name="yearly_discount_percent" class="form-control @error('yearly_discount_percent') is-invalid @enderror" placeholder="{{ __('super-admin.yearly_discount') }}" value="{{ old('yearly_discount_percent', $plan->yearly_discount_percent ?? '') }}" min="0" max="100" step="0.01">
                                    <label>{{ __('super-admin.yearly_discount') }}</label>
                                    @error('yearly_discount_percent') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-hint">{{ __('super-admin.yearly_discount_hint') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="text" name="button_text" class="form-control @error('button_text') is-invalid @enderror" placeholder="{{ __('super-admin.button_text') }}" value="{{ old('button_text', $plan->button_text ?? '') }}" maxlength="100">
                                    <label>{{ __('super-admin.button_text') }}</label>
                                    @error('button_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating-group">
                                    <input type="text" name="button_link" class="form-control @error('button_link') is-invalid @enderror" placeholder="{{ __('super-admin.button_link') }}" value="{{ old('button_link', $plan->button_link ?? '') }}" maxlength="500">
                                    <label>{{ __('super-admin.button_link') }}</label>
                                    @error('button_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3 mt-3">
                            <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)" @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                                <x-toggle-switch name="is_free" :checked="$plan->is_free ?? false" />
                                <span style="font-size:13px;font-weight:500;color:var(--text)">{{ __('super-admin.free') }}</span>
                            </label>
                            <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)" @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                                <x-toggle-switch name="is_active" :checked="$plan->is_active ?? true" />
                                <span style="font-size:13px;font-weight:500;color:var(--text)">{{ __('general.active') }}</span>
                            </label>
                            <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)" @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                                <x-toggle-switch name="is_public" :checked="$plan->is_public ?? true" />
                                <span style="font-size:13px;font-weight:500;color:var(--text)">{{ __('super-admin.public') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======================== TAB: FEATURES ======================== --}}
            <div class="tab-panel" id="panel-features" style="display:{{ $currentTab === 'features' ? 'block' : 'none' }}">
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-puzzle"></i>{{ __('super-admin.feature_assignment') }}</h5>
                    </div>
                    <div class="section-card-body">
                        <div class="settings-section-desc small mb-3">
                            {{ __('super-admin.feature_assignment_hint') }}
                            <a href="{{ route('super.admin.features.create') }}" style="color:var(--accent);text-decoration:none;font-weight:600" target="_blank">
                                <i class="bi bi-plus-circle"></i> {{ __('super-admin.create_feature') }}
                            </a>
                        </div>
                        @if($allFeatures->isNotEmpty())
                            <div class="table-responsive">
                                <table class="data-table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width:30px"></th>
                                            <th>{{ __('super-admin.feature_name_en') }}</th>
                                            <th>{{ __('super-admin.feature_slug') }}</th>
                                            <th style="width:140px">{{ __('super-admin.feature_value') }}</th>
                                            <th style="width:80px">{{ __('super-admin.feature_order') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allFeatures as $feature)
                                            @php
                                                $assigned = $assignedFeatures->get($feature->id);
                                                $checked = $assigned || $feature->is_core;
                                                $val = $assigned['value'] ?? '';
                                                $order = $assigned['sort_order'] ?? $feature->sort_order;
                                                $disabled = $feature->is_core ? 'disabled' : '';
                                            @endphp
                                            <tr style="{{ $feature->is_core ? 'opacity:0.7' : '' }}" class="feature-row">
                                                <td>
                                                    <input type="checkbox" name="plan_features[{{ $feature->id }}][feature_id]" value="{{ $feature->id }}"
                                                        {{ $checked ? 'checked' : '' }}
                                                        {{ $disabled }}
                                                        style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer"
                                                        data-feature-id="{{ $feature->id }}"
                                                        class="feature-checkbox">
                                                </td>
                                                <td>
                                                    <span style="font-size:13px;font-weight:500">{{ $feature->name_en }}</span>
                                                    @if($feature->is_core)
                                                        <span class="badge" style="font-size:9px;background:var(--info-light);color:var(--info);padding:1px 6px;border-radius:3px;font-weight:600;margin-inline-start:4px">{{ __('super-admin.core') }}</span>
                                                    @endif
                                                </td>
                                                <td><code style="font-size:11px;background:var(--bg-subtle);padding:1px 6px;border-radius:3px">{{ $feature->slug }}</code></td>
                                                <td>
                                                    <input type="text" name="plan_features[{{ $feature->id }}][value]" value="{{ $val }}"
                                                        class="form-control" style="padding:4px 8px;font-size:12px;height:auto"
                                                        placeholder="{{ $feature->type === 'boolean' ? 'true/false' : ($feature->type === 'value' ? 'number' : 'text') }}"
                                                        data-feature-input="{{ $feature->id }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="plan_features[{{ $feature->id }}][sort_order]" value="{{ $order }}"
                                                        class="form-control" style="padding:4px 8px;font-size:12px;height:auto;width:70px" min="0">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0 py-2">
                                {{ __('super-admin.no_features_available') }}
                                <a href="{{ route('super.admin.features.create') }}" style="color:var(--accent)">{{ __('super-admin.create_feature') }}</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Submit buttons (visible on details + features tabs) --}}
            <div id="form-actions" style="display:{{ in_array($currentTab, ['details', 'features']) ? 'flex' : 'none' }}" class="d-flex gap-2 mt-4">
                <button type="submit" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                    {{ $plan ? __('general.update') : __('general.create') }}
                </button>
                <a href="{{ route('super.admin.plans.index') }}" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                    {{ __('general.cancel') }}
                </a>
            </div>
        </form>

        {{-- ======================== TAB: PRICES (outside main form) ======================== --}}
        <div class="tab-panel" id="panel-prices" style="display:{{ $currentTab === 'prices' ? 'block' : 'none' }}">
            @if($plan)
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-currency-dollar"></i>{{ __('super-admin.plan_prices') }}</h5>
                    </div>
                    <div class="section-card-body">
                        <div class="settings-section-desc small mb-3">
                            {{ __('super-admin.prices_manage_hint') }}
                        </div>

                        @if($prices->isNotEmpty())
                            <div class="table-responsive">
                                <table class="data-table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('super-admin.price_period') }}</th>
                                            <th>{{ __('super-admin.price_currency') }}</th>
                                            <th>{{ __('super-admin.price_amount') }}</th>
                                            <th>{{ __('general.status') }}</th>
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
                                                    @if($price->is_active)
                                                        <span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.active') }}</span>
                                                    @else
                                                        <span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td class="col-actions">
                                                    <div class="cell-actions">
                                                        <a href="{{ route('super.admin.plans.prices.edit', [$plan, $price]) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.edit') }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('super.admin.plans.prices.destroy', [$plan, $price]) . '?_tab=prices' }}" style="display:inline" id="delete-price-{{ $price->id }}">
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
                            </div>
                        @else
                            <div class="empty-state py-4">
                                <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted);width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                                    <i class="bi bi-currency-dollar" style="font-size:22px"></i>
                                </div>
                                <h4 style="font-size:15px;font-weight:600;margin:0 0 4px">{{ __('super-admin.no_prices') }}</h4>
                                <p style="font-size:13px;color:var(--text-muted);margin:0">{{ __('super-admin.no_prices_for_plan') }}</p>
                            </div>
                        @endif

                        <hr style="border-color:var(--border);margin:20px 0">

                        <h6 style="font-size:14px;font-weight:600;margin:0 0 12px;display:flex;align-items:center;gap:6px">
                            <i class="bi bi-plus-circle"></i>{{ __('super-admin.create_price') }}
                        </h6>
                        <form method="POST" action="{{ route('super.admin.plans.prices.store', $plan) . '?_tab=prices' }}" style="max-width:520px">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <div class="form-floating-group">
                                        <select name="billing_period" class="form-control" style="padding:7px 10px;font-size:13px" required>
                                            <option value="monthly">{{ __('super-admin.monthly') }}</option>
                                            <option value="yearly">{{ __('super-admin.yearly') }}</option>
                                        </select>
                                        <label style="font-size:11px">{{ __('super-admin.price_period') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating-group">
                                        <input type="text" name="currency" class="form-control" style="padding:7px 10px;font-size:13px" value="USD" maxlength="10" required>
                                        <label style="font-size:11px">{{ __('super-admin.price_currency') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating-group">
                                        <input type="number" name="price" class="form-control" style="padding:7px 10px;font-size:13px" step="0.01" min="0" required>
                                        <label style="font-size:11px">{{ __('super-admin.price_amount') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer;width:100%">
                                        <i class="bi bi-plus-lg"></i> {{ __('general.add') }}
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <x-toggle-switch name="is_active" :checked="true" />
                                <span style="font-size:12px;color:var(--text-muted)">{{ __('general.active') }}</span>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="section-card">
                    <div class="section-card-body">
                        <div class="empty-state py-4">
                            <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted);width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                                <i class="bi bi-currency-dollar" style="font-size:22px"></i>
                            </div>
                            <h4 style="font-size:15px;font-weight:600;margin:0 0 4px">{{ __('super-admin.prices') }}</h4>
                            <p style="font-size:13px;color:var(--text-muted);margin:0">{{ __('super-admin.save_plan_first') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    (function() {
        var tabs = document.querySelectorAll('.tab-btn');
        var panels = {
            details: document.getElementById('panel-details'),
            features: document.getElementById('panel-features'),
            prices: document.getElementById('panel-prices'),
        };
        var formActions = document.getElementById('form-actions');

        tabs.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var tab = this.dataset.tab;

                tabs.forEach(function(t) {
                    t.style.color = 'var(--text-muted)';
                    t.style.borderBottomColor = 'transparent';
                });
                this.style.color = 'var(--accent)';
                this.style.borderBottomColor = 'var(--accent)';

                Object.keys(panels).forEach(function(key) {
                    if (panels[key]) {
                        panels[key].style.display = key === tab ? 'block' : 'none';
                    }
                });

                if (formActions) {
                    formActions.style.display = (tab === 'details' || tab === 'features') ? 'flex' : 'none';
                }

                var url = new URL(window.location.href);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            });
        });

        document.querySelectorAll('.feature-checkbox').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var id = this.dataset.featureId;
                var valueInput = document.querySelector('[data-feature-input="' + id + '"]');
                var row = this.closest('.feature-row');
                if (this.checked) {
                    row.style.opacity = '1';
                    if (valueInput) valueInput.disabled = false;
                } else {
                    row.style.opacity = '0.5';
                    if (valueInput) valueInput.disabled = true;
                }
            });
            cb.dispatchEvent(new Event('change'));
        });
    })();

    function confirmDeletePrice(id) {
        var form = document.getElementById('delete-price-' + id);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('super-admin.confirm_delete_price') }}',
            function(confirmed) { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }
    </script>
    @endpush
</x-super-admin-layout>