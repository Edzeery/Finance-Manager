{{-- resources\views\super-admin\payment-methods-form.blade.php --}}
<x-super-admin-layout>
    <x-slot:title>{{ $paymentMethod ? __('super-admin.edit_payment_method') : __('super-admin.create_payment_method') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ $paymentMethod ? __('super-admin.edit_payment_method') : __('super-admin.create_payment_method') }}</x-slot>
    <x-slot:page-description>{{ $paymentMethod ? __('super-admin.edit_payment_method_desc') : __('super-admin.create_payment_method_desc') }}</x-slot>

    @php
        $knownGateway = $paymentMethod ? $registry->find($paymentMethod->key) : null;
        $gatewayFieldGroups = [];
        $registryArray = [];
        foreach ($registry->all() as $gKey => $gDef) {
            $nonEnabled = array_values(array_filter($gDef->fields, fn($f) => $f->key !== 'enabled'));
            $gatewayFieldGroups[$gKey] = [
                'name' => $gDef->name,
                'hasFields' => !empty($nonEnabled),
                'fields' => $nonEnabled,
            ];
            $registryArray[$gKey] = $gDef->toArray();
        }
        $selectedKey = old('key', $paymentMethod->key ?? '');
    @endphp

    <form method="POST" action="{{ $paymentMethod ? route('super.admin.payment-methods.update', $paymentMethod) : route('super.admin.payment-methods.store') }}" style="max-width:700px">
        @csrf @if($paymentMethod) @method('PUT') @endif

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom">{{ __('super-admin.payment_method_key') }} <span class="text-danger">*</span></label>
                <select name="key" id="gateway-key" class="form-custom @error('key') is-invalid @enderror" required>
                    <option value="">{{ __('super-admin.select_gateway') }}</option>
                    @foreach($registry->all() as $key => $def)
                        <option value="{{ $key }}" {{ $selectedKey === $key ? 'selected' : '' }}
                            data-name="{{ $def->name }}"
                            data-icon="{{ $def->icon }}"
                            data-description="{{ $def->description }}"
                            data-category="{{ $def->category }}">
                            {{ $def->name }} ({{ $def->category }})
                        </option>
                    @endforeach
                </select>
                @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">{{ __('super-admin.payment_method_name') }}</label>
                <input type="text" name="name" id="gateway-name" class="form-custom @error('name') is-invalid @enderror"
                       value="{{ old('name', $paymentMethod->name ?? '') }}" maxlength="255">
                <div class="form-hint small mt-1"><i class="bi bi-info-circle"></i> {{ __('super-admin.name_auto_fill_hint') }}</div>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">{{ __('super-admin.payment_method_description') }}</label>
            <textarea name="description" id="gateway-description" class="form-custom @error('description') is-invalid @enderror"
                      rows="2" maxlength="500">{{ old('description', $paymentMethod->description ?? '') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label-custom">{{ __('super-admin.payment_method_icon') }}</label>
                <input type="text" name="icon" id="gateway-icon" class="form-custom @error('icon') is-invalid @enderror"
                       value="{{ old('icon', $paymentMethod->icon ?? '') }}" maxlength="100"
                       placeholder="e.g. bi-credit-card">
                @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label-custom">{{ __('super-admin.payment_method_type') }} <span class="text-danger">*</span></label>
                <select name="type" id="method-type" class="form-custom @error('type') is-invalid @enderror" required>
                    <option value="online" {{ old('type', $paymentMethod->type ?? 'online') === 'online' ? 'selected' : '' }}>{{ __('super-admin.online') }}</option>
                    <option value="manual" {{ old('type', $paymentMethod->type ?? 'online') === 'manual' ? 'selected' : '' }}>{{ __('super-admin.manual') }}</option>
                    <option value="auto_complete" {{ old('type', $paymentMethod->type ?? 'online') === 'auto_complete' ? 'selected' : '' }}>{{ __('super-admin.auto_complete') }}</option>
                </select>
                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label-custom">{{ __('general.order') }}</label>
                <input type="number" name="sort_order" class="form-custom @error('sort_order') is-invalid @enderror"
                       value="{{ old('sort_order', $paymentMethod->sort_order ?? 0) }}" min="0" max="999">
                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                 <x-toggle-switch class="mb-3" name="is_active" value="1" :checked="$paymentMethod->is_active ?? false" label="{{ __('general.active') }}" hint="{{ __('general.active_hint') }}" />
            </div>
            <div class="col-md-6">
                 <x-toggle-switch class="mb-3" name="is_public" value="1" :checked="$paymentMethod->is_public ?? false" label="{{ __('super-admin.show_in_onboarding') }}" hint="{{ __('super-admin.show_in_onboarding_hint') }}" />
            </div>
        </div>

        <div class="mb-4">
            <div class="section-card">
                <div class="section-card-header">
                    <h6><i class="bi bi-lock"></i> {{ __('super-admin.gateway_credentials') }} <span id="creds-key-label" class="badge bg-secondary ms-2 d-none"></span></h6>
                </div>
                <div class="section-card-body">
                    <div class="settings-section-desc small mb-3">{{ __('super-admin.credentials_desc') }}</div>
                    <p id="creds-no-selection" class="text-muted text-center py-4 mb-0 {{ $selectedKey ? 'd-none' : '' }}">{{ __('super-admin.select_gateway_for_credentials') }}</p>

                    @php $creds = old('credentials', $currentCredentials ?? []); @endphp

                    <div id="credentials-fields" class="{{ $selectedKey ? '' : 'd-none' }}">
                        @foreach($gatewayFieldGroups as $gKey => $group)
                            <div class="gateway-creds-group {{ $selectedKey === $gKey ? '' : 'd-none' }}" data-gateway="{{ $gKey }}">
                                @if($group['hasFields'])
                                    @foreach($group['fields'] as $field)
                                        @php $fk = $field->key; @endphp
                                        <div class="mb-3">
                                            <label class="form-label-custom">
                                                {{ $field->label }}
                                                @if($field->required) <span class="text-danger">*</span> @endif
                                            </label>

                                            @switch($field->type)
                                                @case('select')
                                                    <select name="credentials[{{ $fk }}]" class="form-custom"  @if($field->required) required @endif>
                                                        @foreach($field->options as $opt)
                                                            <option value="{{ $opt['value'] }}" {{ ($creds[$fk] ?? $field->default) == $opt['value'] ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    @break

                                                @case('boolean')
                                                    <div class="d-flex align-items-center gap-2">
                                                        <x-toggle-switch name="credentials[{{ $fk }}]" :checked="filter_var($creds[$fk] ?? $field->default, FILTER_VALIDATE_BOOLEAN)" description="{{ $field->help ?? __('general.enabled') }}" />
                                                    </div>
                                                    @break

                                                @case('textarea')
                                                    <textarea  name="credentials[{{ $fk }}]" class="form-custom" rows="3"
                                                              placeholder="{{ $field->placeholder }}">{{ $creds[$fk] ?? $field->default ?? '' }}</textarea>
                                                    @break

                                                @case('password')
                                                    <x-password-input name="credentials[{{ $fk }}]" :value="$creds[$fk] ?? ''"
                                                        :maxlength="$field->maxLength ?? 255" :placeholder="$field->placeholder ?? '••••••••'" />
                                                    @break

                                                @default
                                                    <input type="{{ $field->type === 'url' ? 'url' : ($field->type === 'email' ? 'email' : 'text') }}"
                                                           name="credentials[{{ $fk }}]" class="form-custom"
                                                           value="{{ $creds[$fk] ?? $field->default ?? '' }}" maxlength="{{ $field->maxLength ?? 255 }}"
                                                           placeholder="{{ $field->placeholder }}">
                                            @endswitch

                                            @if($field->help && $field->type !== 'boolean')
                                                <div class="form-hint small mt-1">{{ $field->help }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle"></i> {{ __('super-admin.no_credentials_needed') }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i> {{ __('super-admin.credentials_save_hint') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ربط معدلات الضريبة والرسوم (Tax Rates) --}}
        <div class="mb-4">
            <div class="section-card">
                <div class="section-card-header">
                    <h6><i class="bi bi-percent"></i> {{ __('super-admin.tax_rate_links') ?? 'ربط الضرائب والرسوم' }}</h6>
                </div>
                <div class="section-card-body">
                    @if($taxRates->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-custom mb-0" style="font-size:13px">
                                <thead>
                                    <tr>
                                        <th>{{ __('super-admin.tax_rate_name') ?? 'المعدل' }}</th>
                                        <th>{{ __('general.type') }}</th>
                                        <th>{{ __('super-admin.tax_rate_value') ?? 'القيمة' }}</th>
                                        <th>{{ __('general.type') }} الربط</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($taxRates as $taxRate)
                                        @php
                                            $existing = $linkedTaxRates->get($taxRate->id);
                                            $selectedChargeType = old("tax_rate_links.{$taxRate->id}.charge_type", $existing?->pivot?->charge_type ?? '');
                                        @endphp
                                        <tr>
                                            <td>
                                                <span style="font-weight:500">{{ $taxRate->name }}</span>
                                                @if($taxRate->country)
                                                    <div style="font-size:11px;color:var(--text-muted)">{{ strtoupper($taxRate->country) }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge" style="font-size:10px;background:var(--info-light);color:var(--info);padding:3px 8px;border-radius:4px">
                                                    {{ $taxRate->type === 'percentage' ? '%' : __('general.fixed') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($taxRate->type === 'percentage')
                                                    {{ number_format($taxRate->rate, 2) }}%
                                                @else
                                                    {{ number_format($taxRate->rate, 2) }}
                                                @endif
                                            </td>
                                            <td>
                                                <select name="tax_rate_links[{{ $taxRate->id }}][charge_type]" class="form-custom" style="font-size:12px;padding:4px 6px;min-width:140px">
                                                    <option value="">{{ __('general.none') }}</option>
                                                    <option value="gateway_fee" {{ $selectedChargeType === 'gateway_fee' ? 'selected' : '' }}>
                                                        {{ __('super-admin.charge_gateway_fee') ?? 'رسم بوابة' }}
                                                    </option>
                                                    <option value="tax_added" {{ $selectedChargeType === 'tax_added' ? 'selected' : '' }}>
                                                        {{ __('super-admin.charge_tax_added') ?? 'ضريبة مضافة' }}
                                                    </option>
                                                    <option value="tax_disclosed" {{ $selectedChargeType === 'tax_disclosed' ? 'selected' : '' }}>
                                                        {{ __('super-admin.charge_tax_disclosed') ?? 'ضريبة إفصاح' }}
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="form-hint small mt-2">
                            <i class="bi bi-info-circle"></i>
                            <strong>رسم بوابة</strong>: يُضاف للمبلغ المدفوع فعلاً.
                            <strong>ضريبة مضافة</strong>: تُضاف للمبلغ المدفوع.
                            <strong>ضريبة إفصاح</strong>: تُعرض فقط في الفاتورة ولا تُضاف للمبلغ.
                        </div>
                    @else
                        <p class="text-muted mb-0 py-2">{{ __('super-admin.no_tax_rates') ?? 'لا توجد معدلات ضريبية. أنشئ معدلات ضريبية أولاً.' }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <x-button variant="accent" submit>{{ $paymentMethod ? __('general.update') : __('general.create') }}</x-button>
            <x-button href="{{ route('super.admin.payment-methods.index') }}" variant="outline">{{ __('general.cancel') }}</x-button>
        </div>
    </form>

    <script>
    (function() {
        var keySelect = document.getElementById('gateway-key');
        var nameInput = document.getElementById('gateway-name');
        var descInput = document.getElementById('gateway-description');
        var iconInput = document.getElementById('gateway-icon');
        var typeSelect = document.getElementById('method-type');
        var credsLabel = document.getElementById('creds-key-label');
        var noSelection = document.getElementById('creds-no-selection');
        var fieldsContainer = document.getElementById('credentials-fields');
        var allGroups = document.querySelectorAll('.gateway-creds-group');

        if (!keySelect || !fieldsContainer) return;

        var categoryMap = { online: 'online', bank_transfer: 'manual', wallet: 'auto_complete', cash: 'manual', delivery: 'manual', crypto: 'auto_complete', internal: 'online', custom: 'manual' };

        function updateFields(key) {
            var selected = document.querySelector('.gateway-creds-group[data-gateway="' + key + '"]');

            if (selected) {
                credsLabel.textContent = selected.querySelector('h6') ? '' : key;
                credsLabel.classList.remove('d-none');
                noSelection.classList.add('d-none');
                fieldsContainer.classList.remove('d-none');

                if (nameInput && (!nameInput.value.trim() || nameInput.dataset.autofilled === 'true')) {
                    nameInput.value = keySelect.options[keySelect.selectedIndex].getAttribute('data-name') || '';
                    nameInput.dataset.autofilled = 'true';
                }
                if (descInput && (!descInput.value.trim() || descInput.dataset.autofilled === 'true')) {
                    descInput.value = keySelect.options[keySelect.selectedIndex].getAttribute('data-description') || '';
                    descInput.dataset.autofilled = 'true';
                }
                if (iconInput && (!iconInput.value.trim() || iconInput.dataset.autofilled === 'true')) {
                    iconInput.value = keySelect.options[keySelect.selectedIndex].getAttribute('data-icon') || '';
                    iconInput.dataset.autofilled = 'true';
                }

                var cat = keySelect.options[keySelect.selectedIndex].getAttribute('data-category');
                if (typeSelect && categoryMap[cat]) {
                    typeSelect.value = categoryMap[cat];
                }

                for (var i = 0; i < allGroups.length; i++) {
                    var isTarget = allGroups[i] === selected;
                    allGroups[i].classList.toggle('d-none', !isTarget);
                    var inputs = allGroups[i].querySelectorAll('input, select, textarea');
                    for (var j = 0; j < inputs.length; j++) {
                        inputs[j].disabled = !isTarget;
                    }
                }
            } else {
                credsLabel.classList.add('d-none');
                noSelection.classList.remove('d-none');
                fieldsContainer.classList.add('d-none');
            }
        }

        keySelect.addEventListener('change', function() {
            updateFields(this.value);
        });

        if (keySelect.value) {
            updateFields(keySelect.value);
        }
    })();
    </script>
</x-super-admin-layout>
