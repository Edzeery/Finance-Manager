<x-super-admin-layout>
    <x-slot:title>{{ $plan ? __('super-admin.edit_plan') : __('super-admin.create_plan') }} -
        {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ $plan ? __('super-admin.edit_plan') : __('super-admin.create_plan') }}</x-slot>
    <x-slot:page-description>{{ $plan ? __('super-admin.edit_plan_desc') : __('super-admin.create_plan_desc') }}</x-slot>

    @php
        $currentTab = request('tab', 'details');
        $tabOrder = ['details', 'features', 'prices'];
        $currentIdx = array_search($currentTab, $tabOrder);
        $currentTabPrev = $currentIdx > 0 ? $tabOrder[$currentIdx - 1] : null;
        $currentTabNext = $currentIdx < count($tabOrder) - 1 ? $tabOrder[$currentIdx + 1] : null;
    @endphp

    <x-tabs :tabs="[
        'details' => ['label' => __('super-admin.plan_details'), 'icon' => 'bi bi-info-circle'],
        'features' => [
            'label' => __('super-admin.features'),
            'icon' => 'bi bi-list-check',
            'count' => $allFeatures->count(),
        ],
        'prices' => [
            'label' => __('super-admin.prices'),
            'icon' => 'bi bi-currency-dollar',
            'count' => $plan ? $prices->count() : null,
        ],
    ]" :current="$currentTab" style="underline" mode="client" on-tab-click="switchTab" />

    <div style="max-width:860px">
        <form method="POST" class="mb-3"
            action="{{ $plan ? route('super.admin.plans.update', $plan) : route('super.admin.plans.store') }}"
            id="planForm">
            @csrf @if ($plan)
                @method('PUT')
            @endif

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
                                    <input type="text" name="name_en"
                                        class="form-control @error('name_en') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.name_en') }}"
                                        value="{{ old('name_en', $plan->name_en ?? '') }}" maxlength="255" required>
                                    <label>{{ __('super-admin.name_en') }} <span class="text-danger">*</span></label>
                                    @error('name_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="slug"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.plan_slug') }}"
                                        value="{{ old('slug', $plan->slug ?? '') }}" maxlength="100" required>
                                    <label>{{ __('super-admin.plan_slug') }} <span class="text-danger">*</span></label>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="name_ar"
                                        class="form-control @error('name_ar') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.name_ar') }}"
                                        value="{{ old('name_ar', $plan->name_ar ?? '') }}" maxlength="255">
                                    <label>{{ __('super-admin.name_ar') }}</label>
                                    @error('name_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="name_fr"
                                        class="form-control @error('name_fr') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.name_fr') }}"
                                        value="{{ old('name_fr', $plan->name_fr ?? '') }}" maxlength="255">
                                    <label>{{ __('super-admin.name_fr') }}</label>
                                    @error('name_fr')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-floating-group">
                            <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror"
                                placeholder="{{ __('super-admin.desc_en') }}" rows="3" maxlength="1000"
                                style="min-height:70px;padding-top:20px">{{ old('description_en', $plan->description_en ?? '') }}</textarea>
                            <label>{{ __('super-admin.desc_en') }}</label>
                            @error('description_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.desc_ar') }}" rows="3" maxlength="1000"
                                        style="min-height:70px;padding-top:20px">{{ old('description_ar', $plan->description_ar ?? '') }}</textarea>
                                    <label>{{ __('super-admin.desc_ar') }}</label>
                                    @error('description_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <textarea name="description_fr" class="form-control @error('description_fr') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.desc_fr') }}" rows="3" maxlength="1000"
                                        style="min-height:70px;padding-top:20px">{{ old('description_fr', $plan->description_fr ?? '') }}</textarea>
                                    <label>{{ __('super-admin.desc_fr') }}</label>
                                    @error('description_fr')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 mb-3"
                            style="font-size:13px;background:var(--info-light);color:var(--info);border-radius:var(--radius-sm);padding:10px 14px;display:flex;align-items:center;gap:8px">
                            <i class="bi bi-currency-dollar"></i>
                            {{ __('super-admin.prices_manage_hint') }}
                            @if ($plan)
                                <a href="{{ route('super.admin.plans.edit', [$plan, 'tab' => 'prices']) }}"
                                    style="margin-inline-start:auto;font-weight:600;color:var(--accent)">{{ __('super-admin.plan_prices') }}
                                    &rarr;</a>
                            @endif
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating-group">
                                    <input type="number" name="sort_order"
                                        class="form-control @error('sort_order') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.plan_order') }}"
                                        value="{{ old('sort_order', $plan->sort_order ?? '') }}" min="0">
                                    <label>{{ __('super-admin.plan_order') }}</label>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="number" name="yearly_discount_percent"
                                        class="form-control @error('yearly_discount_percent') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.yearly_discount') }}"
                                        value="{{ old('yearly_discount_percent', $plan->yearly_discount_percent ?? '') }}"
                                        min="0" max="100" step="0.01">
                                    <label>{{ __('super-admin.yearly_discount') }}</label>
                                    @error('yearly_discount_percent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-hint">{{ __('super-admin.yearly_discount_hint') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="text" name="button_text_en"
                                        class="form-control @error('button_text_en') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.btn_en') }}"
                                        value="{{ old('button_text_en', $plan->button_text_en ?? '') }}"
                                        maxlength="100">
                                    <label>{{ __('super-admin.btn_en') }}</label>
                                    @error('button_text_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating-group">
                                    <input type="text" name="button_link"
                                        class="form-control @error('button_link') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.button_link') }}"
                                        value="{{ old('button_link', $plan->button_link ?? '') }}" maxlength="500">
                                    <label>{{ __('super-admin.button_link') }}</label>
                                    @error('button_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="button_text_ar"
                                        class="form-control @error('button_text_ar') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.btn_ar') }}"
                                        value="{{ old('button_text_ar', $plan->button_text_ar ?? '') }}"
                                        maxlength="100">
                                    <label>{{ __('super-admin.btn_ar') }}</label>
                                    @error('button_text_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="button_text_fr"
                                        class="form-control @error('button_text_fr') is-invalid @enderror"
                                        placeholder="{{ __('super-admin.btn_fr') }}"
                                        value="{{ old('button_text_fr', $plan->button_text_fr ?? '') }}"
                                        maxlength="100">
                                    <label>{{ __('super-admin.btn_fr') }}</label>
                                    @error('button_text_fr')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3 mt-3">
                            <label class="d-flex align-items-center gap-2"
                                style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)"
                                @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                                <x-toggle-switch name="is_free" :checked="$plan->is_free ?? false" />
                                <span
                                    style="font-size:13px;font-weight:500;color:var(--text)">{{ __('super-admin.free') }}</span>
                            </label>
                            <label class="d-flex align-items-center gap-2"
                                style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)"
                                @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                                <x-toggle-switch name="is_active" :checked="$plan->is_active ?? true" />
                                <span
                                    style="font-size:13px;font-weight:500;color:var(--text)">{{ __('general.active') }}</span>
                            </label>
                            <label class="d-flex align-items-center gap-2"
                                style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)"
                                @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                                <x-toggle-switch name="is_public" :checked="$plan->is_public ?? true" />
                                <span
                                    style="font-size:13px;font-weight:500;color:var(--text)">{{ __('super-admin.public') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======================== TAB: FEATURES ======================== --}}
            <div class="tab-panel" id="panel-features"
                style="display:{{ $currentTab === 'features' ? 'block' : 'none' }}">
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-puzzle"></i>{{ __('super-admin.feature_assignment') }}</h5>
                    </div>
                    <div class="section-card-body">
                        <div class="settings-section-desc small mb-3">
                            {{ __('super-admin.feature_assignment_hint') }}
                            <a href="{{ route('super.admin.features.create') }}"
                                style="color:var(--accent);text-decoration:none;font-weight:600" target="_blank">
                                <i class="bi bi-plus-circle"></i> {{ __('super-admin.create_feature') }}
                            </a>
                        </div>
                        @if ($allFeatures->isNotEmpty())
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
                                        @foreach ($allFeatures as $feature)
                                            @php
                                                $assigned = $assignedFeatures->get($feature->id);
                                                $checked = $assigned || $feature->is_core;
                                                $val = $assigned['value'] ?? '';
                                                $order = $assigned['sort_order'] ?? $feature->sort_order;
                                                $disabled = $feature->is_core ? 'disabled' : '';
                                            @endphp
                                            <tr style="{{ $feature->is_core ? 'opacity:0.7' : '' }}"
                                                class="feature-row">
                                                <td>
                                                    @if ($feature->is_core)
                                                        <input type="hidden"
                                                            name="plan_features[{{ $feature->id }}][feature_id]"
                                                            value="{{ $feature->id }}">
                                                    @endif
                                                    <input type="checkbox"
                                                        name="plan_features[{{ $feature->id }}][feature_id]"
                                                        value="{{ $feature->id }}" {{ $checked ? 'checked' : '' }}
                                                        {{ $disabled }}
                                                        style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer"
                                                        data-feature-id="{{ $feature->id }}"
                                                        class="feature-checkbox">
                                                </td>
                                                <td>
                                                    <span
                                                        style="font-size:13px;font-weight:500">{{ $feature->name_en }}</span>
                                                    @if ($feature->is_core)
                                                        <span class="badge"
                                                            style="font-size:9px;background:var(--info-light);color:var(--info);padding:1px 6px;border-radius:3px;font-weight:600;margin-inline-start:4px">{{ __('super-admin.core') }}</span>
                                                    @endif
                                                </td>
                                                <td><code
                                                        style="font-size:11px;background:var(--bg-subtle);padding:1px 6px;border-radius:3px">{{ $feature->slug }}</code>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="plan_features[{{ $feature->id }}][value]"
                                                        value="{{ $val }}" class="form-control"
                                                        style="padding:4px 8px;font-size:12px;height:auto"
                                                        placeholder="{{ $feature->type === 'boolean' ? 'true/false' : ($feature->type === 'value' ? 'number' : 'text') }}"
                                                        data-feature-input="{{ $feature->id }}">
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        name="plan_features[{{ $feature->id }}][sort_order]"
                                                        value="{{ $order }}" class="form-control"
                                                        style="padding:4px 8px;font-size:12px;height:auto;width:70px"
                                                        min="0" data-feature-input="{{ $feature->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0 py-2">
                                {{ __('super-admin.no_features_available') }}
                                <a href="{{ route('super.admin.features.create') }}"
                                    style="color:var(--accent)">{{ __('super-admin.create_feature') }}</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Wizard navigation (stepper: Previous / Next / Submit) --}}
            <div id="wizard-nav" class="d-flex gap-2 mt-4" style="justify-content:space-between;align-items:center">
                <div>
                    <x-button variant="outline" :href="route('super.admin.plans.index')">{{ __('general.cancel') }}</x-button>
                </div>
                <div class="d-flex gap-2">
                    <x-button variant="outline" icon="bi bi-chevron-left" data-prev
                        style="display:{{ $currentTab === 'details' ? 'none' : '' }}"
                        onclick="switchTab('{{ $currentTabPrev ?? 'details' }}')">{{ __('general.previous') }}</x-button>
                    <x-button variant="accent" icon="bi bi-chevron-right" icon-position="right" data-next
                        style="display:{{ $currentTab === 'prices' ? 'none' : '' }}"
                        onclick="switchTab('{{ $currentTabNext ?? 'prices' }}')">{{ __('general.next') }}</x-button>
                    <x-button variant="accent" data-submit submit
                        style="display:{{ $currentTab === 'prices' ? '' : 'none' }}">
                        {{ $plan ? __('general.update') : __('general.create') }}
                    </x-button>
                </div>
            </div>

            {{-- close form here for edit; keep it open for create to wrap prices --}}
            @if ($plan)
        </form>
        @endif

        {{-- ======================== TAB: PRICES ======================== --}}
        <div class="tab-panel" id="panel-prices" style="display:{{ $currentTab === 'prices' ? 'block' : 'none' }}">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-currency-dollar"></i>{{ __('super-admin.plan_prices') }}</h5>
                </div>
                <div class="section-card-body">
                    <div class="settings-section-desc small mb-3">{{ __('super-admin.prices_manage_hint') }}</div>

                    @if ($plan)
                        @if ($prices->isNotEmpty())
                            <div class="table-responsive">
                                <table class="data-table" style="width:100%" id="edit-prices-table">
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
                                        @foreach ($prices as $price)
                                            <tr>
                                                <td>
                                                    <span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:3px 10px;border-radius:6px;font-weight:600">
                                                        {{ $price->billing_period === 'monthly' ? __('super-admin.monthly') : __('super-admin.yearly') }}
                                                    </span>
                                                </td>
                                                <td><strong>{{ $price->currency }}</strong></td>
                                                <td><strong>{{ number_format($price->price, 2) }}</strong></td>
                                                <td>
                                                    @if ($price->is_active)
                                                        <span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.active') }}</span>
                                                    @else
                                                        <span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td class="col-actions">
                                                    <div class="cell-actions">
                                                        <a href="{{ route('super.admin.plans.prices.edit', [$plan, $price]) }}" class="action-btn" title="{{ __('general.edit') }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('super.admin.plans.prices.destroy', [$plan, $price]) . '?_tab=prices' }}" style="display:inline" id="delete-price-{{ $price->id }}">
                                                            @csrf @method('DELETE')
                                                        </form>
                                                        <button type="button" class="action-btn" style="color:var(--danger);border-color:transparent" title="{{ __('general.delete') }}" onclick="confirmDeletePrice({{ $price->id }})">
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
                    @else
                        <div id="create-prices-empty" class="empty-state py-4">
                            <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted);width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                                <i class="bi bi-currency-dollar" style="font-size:22px"></i>
                            </div>
                            <h4 style="font-size:15px;font-weight:600;margin:0 0 4px">{{ __('super-admin.no_prices') }}</h4>
                            <p style="font-size:13px;color:var(--text-muted);margin:0">{{ __('super-admin.no_prices_for_plan') }}</p>
                        </div>
                        <div class="table-responsive" id="create-prices-table-wrapper" style="display:none">
                            <table class="data-table" style="width:100%" id="create-prices-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('super-admin.price_period') }}</th>
                                        <th>{{ __('super-admin.price_currency') }}</th>
                                        <th>{{ __('super-admin.price_amount') }}</th>
                                        <th>{{ __('general.status') }}</th>
                                        <th class="col-actions"></th>
                                    </tr>
                                </thead>
                                <tbody id="create-prices-tbody"></tbody>
                            </table>
                        </div>
                    @endif

                    <hr style="border-color:var(--border);margin:20px 0">

                    @if ($plan)
                        <h6 style="font-size:14px;font-weight:600;margin:0 0 12px;display:flex;align-items:center;gap:6px">
                            <i class="bi bi-plus-circle"></i>{{ __('super-admin.create_price') }}
                        </h6>
                        <form method="POST" action="{{ route('super.admin.plans.prices.store', $plan) . '?_tab=prices' }}" style="max-width:540px">
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
                                    <x-button variant="accent" icon="bi bi-plus-lg" submit block>{{ __('general.add') }}</x-button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <x-toggle-switch name="is_active" :checked="true" />
                                <span style="font-size:12px;color:var(--text-muted)">{{ __('general.active') }}</span>
                            </div>
                        </form>
                    @else
                        <h6 style="font-size:14px;font-weight:600;margin:0 0 12px;display:flex;align-items:center;gap:6px">
                            <i class="bi bi-plus-circle"></i>{{ __('super-admin.create_price') }}
                        </h6>
                        <p class="small text-muted mb-2">{{ __('super-admin.prices_manage_create_hint') }}</p>
                        <div class="row g-2 align-items-end" style="max-width:540px">
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <select id="price-period-input" class="form-control" style="padding:7px 10px;font-size:13px">
                                        <option value="monthly">{{ __('super-admin.monthly') }}</option>
                                        <option value="yearly">{{ __('super-admin.yearly') }}</option>
                                    </select>
                                    <label style="font-size:11px">{{ __('super-admin.price_period') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="text" id="price-currency-input" class="form-control" style="padding:7px 10px;font-size:13px" value="USD" maxlength="10">
                                    <label style="font-size:11px">{{ __('super-admin.price_currency') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="number" id="price-amount-input" class="form-control" style="padding:7px 10px;font-size:13px" step="0.01" min="0">
                                    <label style="font-size:11px">{{ __('super-admin.price_amount') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <x-button variant="accent" icon="bi bi-plus-lg" block onclick="addPriceRow()">{{ __('general.add') }}</x-button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <input type="hidden" id="price-active-input-hidden" value="1">
                            <x-toggle-switch id="price-active-input" :checked="true" />
                            <span style="font-size:12px;color:var(--text-muted)">{{ __('general.active') }}</span>
                        </div>
                        <input type="hidden" name="prices_count" id="prices_count" value="-1">
                    @endif
                </div>
            </div>
        </div>

        {{-- close main form for create mode --}}
        @if (!$plan)
            </form>
        @endif
    </div>

    @push('scripts')
        <script>
            var tabOrder = ['details', 'features', 'prices'];

            function switchTab(tab) {
                var tabs = document.querySelectorAll('.tabs-tab');
                var panels = {
                    details: document.getElementById('panel-details'),
                    features: document.getElementById('panel-features'),
                    prices: document.getElementById('panel-prices'),
                };

                tabs.forEach(function(t) {
                    t.classList.toggle('active', t.dataset.tab === tab);
                });

                Object.keys(panels).forEach(function(key) {
                    if (panels[key]) {
                        panels[key].style.display = key === tab ? 'block' : 'none';
                    }
                });

                var idx = tabOrder.indexOf(tab);
                var navBtns = document.getElementById('wizard-nav');
                if (navBtns) {
                    navBtns.querySelectorAll('[data-prev]').forEach(function(b) {
                        b.style.display = idx <= 0 ? 'none' : '';
                    });
                    navBtns.querySelectorAll('[data-next]').forEach(function(b) {
                        b.style.display = idx >= tabOrder.length - 1 ? 'none' : '';
                    });
                    navBtns.querySelectorAll('[data-submit]').forEach(function(b) {
                        b.style.display = idx >= tabOrder.length - 1 ? '' : 'none';
                    });
                    if (idx >= 0 && idx < tabOrder.length - 1) {
                        navBtns.querySelector('[data-next]').onclick = function() {
                            switchTab(tabOrder[idx + 1]);
                        };
                    }
                    if (idx > 0) {
                        navBtns.querySelector('[data-prev]').onclick = function() {
                            switchTab(tabOrder[idx - 1]);
                        };
                    }
                }

                var url = new URL(window.location.href);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            }

            (function() {
                switchTab('{{ $currentTab }}');

                document.querySelectorAll('.feature-checkbox').forEach(function(cb) {
                    cb.addEventListener('change', function() {
                        var id = this.dataset.featureId;
                        var featureInputs = document.querySelectorAll('[data-feature-input="' + id + '"]');
                        var row = this.closest('.feature-row');
                        if (this.checked) {
                            row.style.opacity = '1';
                            featureInputs.forEach(function(input) {
                                input.disabled = false;
                            });
                        } else {
                            row.style.opacity = '0.5';
                            featureInputs.forEach(function(input) {
                                input.disabled = true;
                            });
                        }
                    });
                    cb.dispatchEvent(new Event('change'));
                });
            })();

            function addPriceRow() {
                var period = document.getElementById('price-period-input');
                var currency = document.getElementById('price-currency-input');
                var amount = document.getElementById('price-amount-input');
                var activeHidden = document.getElementById('price-active-input-hidden');

                if (!amount.value || parseFloat(amount.value) < 0) {
                    amount.focus();
                    return;
                }

                var countInput = document.getElementById('prices_count');
                var idx = parseInt(countInput.value, 10) + 1;
                countInput.value = idx;

                var isActive = activeHidden ? activeHidden.value === '1' : true;
                var periodLabel = period.value === 'monthly' ? '{{ __('super-admin.monthly') }}' : '{{ __('super-admin.yearly') }}';

                var tbody = document.getElementById('create-prices-tbody');
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td><span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:3px 10px;border-radius:6px;font-weight:600">' + periodLabel + '</span></td>' +
                    '<td><strong>' + currency.value + '</strong></td>' +
                    '<td><strong>' + parseFloat(amount.value).toFixed(2) + '</strong></td>' +
                    '<td>' +
                    '<input type="hidden" name="prices[' + idx + '][billing_period]" value="' + period.value + '">' +
                    '<input type="hidden" name="prices[' + idx + '][currency]" value="' + currency.value + '">' +
                    '<input type="hidden" name="prices[' + idx + '][price]" value="' + amount.value + '">' +
                    '<input type="hidden" name="prices[' + idx + '][is_active]" value="' + (isActive ? '1' : '0') + '">' +
                    (isActive
                        ? '<span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.active') }}</span>'
                        : '<span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.inactive') }}</span>'
                    ) + '</td>' +
                    '<td class="col-actions"><div class="cell-actions">' +
                    '<button type="button" class="action-btn" style="color:var(--danger);border-color:transparent" title="{{ __('general.remove') }}" onclick="removePriceRow(this)"><i class="bi bi-x-lg"></i></button>' +
                    '</div></td>';

                tbody.appendChild(tr);

                document.getElementById('create-prices-table-wrapper').style.display = '';
                var emptyEl = document.getElementById('create-prices-empty');
                if (emptyEl) emptyEl.style.display = 'none';

                period.value = 'monthly';
                currency.value = 'USD';
                amount.value = '';
                if (activeHidden) {
                    var toggleBtn = document.getElementById('price-active-input');
                    if (toggleBtn && typeof setToggle === 'function') {
                        setToggle('price-active-input', true);
                    }
                }
            }

            function removePriceRow(btn) {
                var tr = btn.closest('tr');
                tr.remove();
                var tbody = document.getElementById('create-prices-tbody');
                if (!tbody || tbody.children.length === 0) {
                    document.getElementById('create-prices-table-wrapper').style.display = 'none';
                    var emptyEl = document.getElementById('create-prices-empty');
                    if (emptyEl) emptyEl.style.display = '';
                }
            }

            function confirmDeletePrice(id) {
                var form = document.getElementById('delete-price-' + id);
                if (!form) return;
                showConfirmModal(
                    '{{ __('general.confirm') }}',
                    '{{ __('super-admin.confirm_delete_price') }}',
                    function(confirmed) {
                        if (confirmed) form.submit();
                    },
                    '{{ __('general.delete') }}',
                    'btn-danger'
                );
            }
        </script>
    @endpush
</x-super-admin-layout>
