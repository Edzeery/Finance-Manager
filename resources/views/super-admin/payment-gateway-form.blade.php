<x-super-admin-layout>
    <x-slot:title>{{ $gateway ? __('super-admin.edit_gateway_structure') : __('super-admin.create_gateway_structure') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ $gateway ? __('super-admin.edit_gateway_structure') : __('super-admin.create_gateway_structure') }}</x-slot>
    <x-slot:page-description>{{ $gateway ? __('super-admin.edit_gateway_structure') : __('super-admin.create_gateway_structure') }}</x-slot>

    <form method="POST" action="{{ $gateway ? route('super.admin.gateways.update', $gateway) : route('super.admin.gateways.store') }}" style="max-width:800px">
        @csrf @if($gateway) @method('PUT') @endif

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom">{{ __('super-admin.gateway_key') }} <span class="text-danger">*</span></label>
                <input type="text" name="key" class="form-custom @error('key') is-invalid @enderror"
                       value="{{ old('key', $gateway->key ?? '') }}" maxlength="50" required
                       placeholder="e.g. my_gateway">
                @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">{{ __('super-admin.gateway_name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-custom @error('name') is-invalid @enderror"
                       value="{{ old('name', $gateway->name ?? '') }}" maxlength="255" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label-custom">{{ __('super-admin.category') }} <span class="text-danger">*</span></label>
                <select name="category" class="form-custom @error('category') is-invalid @enderror" required>
                    @foreach($registry->categories() as $catKey => $catLabel)
                        <option value="{{ $catKey }}" {{ old('category', $gateway->category ?? '') === $catKey ? 'selected' : '' }}>{{ $catLabel }}</option>
                    @endforeach
                </select>
                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label-custom">{{ __('super-admin.gateway_icon') }}</label>
                <input type="text" name="icon" class="form-custom @error('icon') is-invalid @enderror"
                       value="{{ old('icon', $gateway->icon ?? '') }}" maxlength="100"
                       placeholder="bi-credit-card">
                @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label-custom">{{ __('general.order') }}</label>
                <input type="number" name="sort_order" class="form-custom @error('sort_order') is-invalid @enderror"
                       value="{{ old('sort_order', $gateway->sort_order ?? 0) }}" min="0" max="999">
                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">{{ __('super-admin.gateway_description') }}</label>
            <textarea name="description" class="form-custom @error('description') is-invalid @enderror"
                      rows="2" maxlength="500">{{ old('description', $gateway->description ?? '') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-toggle-switch
                    name="sandbox"
                    value="1"
                    :checked="$gateway->sandbox ?? false"
                    label="Sandbox"
                />
            </div>
            <div class="col-md-4">
                <x-toggle-switch
                    name="webhook"
                    value="1"
                    :checked="$gateway->webhook ?? false"
                    label="Webhook"
                />
            </div>
            <div class="col-md-4">
                <label class="form-label-custom">{{ __('super-admin.supported_currencies') }}</label>
                @php $allCurrencies = \App\Helpers\CurrencyHelper::availableCurrencies(); @endphp
                @if ($allCurrencies)
                    <div class="d-flex flex-wrap gap-3 mt-1" style="max-height:140px;overflow-y:auto">
                        @foreach($allCurrencies as $cur)
                            <div class="form-check form-check-inline m-0">
                                <input type="checkbox" name="supported_currencies[]" value="{{ $cur['code'] }}"
                                       id="cur_{{ $cur['code'] }}" class="form-check-input"
                                       {{ in_array($cur['code'], old('supported_currencies', $gateway->supported_currencies ?? ['DZD'])) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="cur_{{ $cur['code'] }}">{{ $cur['code'] }} — {{ $cur['name'] }}</label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small mt-1">{{ __('general.no_data') }}</p>
                    <input type="hidden" name="supported_currencies" value="">
                @endif
                @error('supported_currencies') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @error('supported_currencies.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="section-card mb-4">
            <div class="section-card-header">
                <h6><i class="bi bi-layout-three-columns"></i> {{ __('super-admin.fields_builder') }}</h6>
            </div>
            <div class="section-card-body">
                <div class="settings-section-desc small mb-3">{{ __('super-admin.gateway_save_hint') }}</div>

                <div id="fields-builder">
                    @php $savedFields = old('fields', $gateway->fields ?? []); @endphp
                    @foreach($savedFields as $i => $f)
                        @php
                            $isSelect = ($f['type'] ?? 'text') === 'select';
                            $defOld = old("fields.{$i}.default");
                            $defVal = $defOld !== null ? $defOld : ($f['default'] ?? '');
                            $optList = old("fields.{$i}.options");
                            if ($optList === null && !empty($f['options'])) {
                                $optList = $f['options'];
                            }
                        @endphp
                        <div class="field-row border rounded p-2 mb-2" data-index="{{ $i }}">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-2">
                                    <input type="text" name="fields[{{ $i }}][key]" value="{{ $f['key'] ?? '' }}" class="form-custom" placeholder="{{ __('super-admin.field_key') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="fields[{{ $i }}][label]" value="{{ $f['label'] ?? '' }}" class="form-custom" placeholder="{{ __('super-admin.field_label') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="fields[{{ $i }}][type]" class="form-custom">
                                        @foreach(['text','password','textarea','email','url','number','select','boolean'] as $t)
                                            <option value="{{ $t }}" {{ ($f['type'] ?? 'text') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="fields[{{ $i }}][placeholder]" value="{{ $f['placeholder'] ?? '' }}" class="form-custom" placeholder="{{ __('super-admin.field_placeholder') }}">
                                </div>
                                <div class="col-md-1 d-flex flex-column align-items-center">
                                    <x-toggle-switch name="fields[{{ $i }}][required]" :checked="$f['required'] ?? false"
                                       />
                                    <span class="small text-muted mt-1">{{ __('super-admin.field_required') }}</span>
                                </div>
                                <div class="col-md-1 d-flex flex-column align-items-center">
                                    <x-toggle-switch name="fields[{{ $i }}][encrypted]" :checked="$f['encrypted'] ?? false"
                                        />
                                    <span class="small text-muted mt-1">{{ __('super-admin.field_encrypted') }}</span>
                                </div>
                                <div class="col-md-1">
                                    <x-button variant="danger" size="sm" icon="bi bi-trash" class="remove-field-row" />
                                </div>
                            </div>
                            <div class="row g-2 mt-1">
                                <div class="col-md-11">
                                    <input type="text" name="fields[{{ $i }}][help]" value="{{ $f['help'] ?? '' }}" class="form-custom" placeholder="{{ __('super-admin.field_help') }}">
                                </div>
                                <div class="col-md-1">
                                    <input type="hidden" name="fields[{{ $i }}][maxLength]" value="{{ $f['maxLength'] ?? 255 }}">
                                </div>
                            </div>
                            <div class="row g-2 mt-1 select-options" style="{{ $isSelect ? '' : 'display:none' }}">
                                <div class="col-md-2">
                                    <label class="small text-muted mb-1 d-block">{{ __('super-admin.field_default') }}</label>
                                    <input type="text" name="fields[{{ $i }}][default]" value="{{ $defVal }}" class="form-custom" placeholder="{{ __('super-admin.field_default') }}">
                                </div>
                                <div class="col-md-10">
                                    <label class="small text-muted mb-1 d-block">{{ __('super-admin.field_options') }}</label>
                                    <div class="options-list">
                                        @if ($optList)
                                            @foreach($optList as $oi => $opt)
                                                @php $ov = is_array($opt) ? ($opt['value'] ?? '') : ''; $ol = is_array($opt) ? ($opt['label'] ?? $ov) : ''; @endphp
                                                <div class="row g-1 mb-1 option-row">
                                                    <div class="col-md-4">
                                                        <input type="text" name="fields[{{ $i }}][options][{{ $oi }}][value]" value="{{ $ov }}" class="form-custom" placeholder="{{ __('super-admin.field_option_value') }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" name="fields[{{ $i }}][options][{{ $oi }}][label]" value="{{ $ol }}" class="form-custom" placeholder="{{ __('super-admin.field_option_label') }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <x-button variant="danger" size="sm" icon="bi bi-x-lg" class="remove-option" />
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <x-button variant="outline" size="sm" icon="bi bi-plus" class="add-option" data-fi="{{ $i }}">{{ __('super-admin.add_option') }}</x-button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <x-button variant="outline" size="sm" icon="bi bi-plus" id="add-field-row">{{ __('super-admin.add_field') }}</x-button>
            </div>
        </div>

        <div class="d-flex gap-2">
            <x-button variant="accent" submit>{{ $gateway ? __('general.update') : __('general.create') }}</x-button>
            <x-button href="{{ route('super.admin.payment-methods.index') }}" variant="outline">{{ __('general.cancel') }}</x-button>
        </div>
    </form>

    @push('scripts')
    <script>
    function createToggleHTML(name, checked, hiddenId) {
        var isChecked = checked ? true : false;
        var icon = isChecked ? 'bi-toggle2-on' : 'bi-toggle2-off';
        var color = isChecked ? 'var(--success)' : 'var(--text-muted)';
        var val = isChecked ? '1' : '0';
        return '<input type="hidden" name="' + name + '" value="' + val + '" id="' + hiddenId + '">' +
            '<button type="button" class="btn btn-sm p-0 border-0 bg-transparent toggle-switch-btn" ' +
            '@click="toggleSwitch($el, \'' + hiddenId + '\')" aria-label="Toggle" style="transition:all 0.15s;cursor:pointer">' +
            '<i class="bi ' + icon + '" style="font-size:20px;color:' + color + ';pointer-events:none;transition:color 0.15s"></i>' +
            '</button>';
    }

    function createOptionRow(fi, oi, val, lbl) {
        var html = '<div class="row g-1 mb-1 option-row">' +
            '<div class="col-md-4"><input type="text" name="fields[' + fi + '][options][' + oi + '][value]" value="' + (val || '') + '" class="form-custom" placeholder="{{ __('super-admin.field_option_value') }}"></div>' +
            '<div class="col-md-6"><input type="text" name="fields[' + fi + '][options][' + oi + '][label]" value="' + (lbl || '') + '" class="form-custom" placeholder="{{ __('super-admin.field_option_label') }}"></div>' +
            '<div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger remove-option"><i class="bi bi-x-lg"></i></button></div>' +
            '</div>';
        var div = document.createElement('div');
        div.innerHTML = html;
        var row = div.firstElementChild;
        row.querySelector('.remove-option').addEventListener('click', function() { row.remove(); });
        return row;
    }

    function initFieldBuilder() {
        var container = document.getElementById('fields-builder');
        var addBtn = document.getElementById('add-field-row');
        if (!container || !addBtn) return;

        function getNextIndex() {
            var rows = container.querySelectorAll('.field-row');
            var max = -1;
            rows.forEach(function(r) {
                var idx = parseInt(r.getAttribute('data-index'));
                if (idx > max) max = idx;
            });
            return max + 1;
        }

        function getNextOptionIndex(optionsList) {
            var rows = optionsList.querySelectorAll('.option-row');
            var max = -1;
            rows.forEach(function(r) {
                var inputs = r.querySelectorAll('input');
                for (var k = 0; k < inputs.length; k++) {
                    var m = inputs[k].name.match(/options\[(\d+)\]/);
                    if (m) { var v = parseInt(m[1]); if (v > max) max = v; }
                }
            });
            return max + 1;
        }

        function bindOptionRow(row) {
            row.querySelector('.remove-option').addEventListener('click', function() { row.remove(); });
        }

        function createFieldRow(index) {
            var row = document.createElement('div');
            row.className = 'field-row border rounded p-2 mb-2';
            row.setAttribute('data-index', index);
            var reqId = 'tgl_req_' + index;
            var encId = 'tgl_enc_' + index;
            var reqHtml = createToggleHTML('fields[' + index + '][required]', true, reqId);
            var encHtml = createToggleHTML('fields[' + index + '][encrypted]', false, encId);
            row.innerHTML =
                '<div class="row g-2 align-items-center">' +
                    '<div class="col-md-2"><input type="text" name="fields[' + index + '][key]" class="form-custom" placeholder="{{ __('super-admin.field_key') }}"></div>' +
                    '<div class="col-md-2"><input type="text" name="fields[' + index + '][label]" class="form-custom" placeholder="{{ __('super-admin.field_label') }}"></div>' +
                    '<div class="col-md-2"><select name="fields[' + index + '][type]" class="form-custom">' +
                        '@foreach(['text','password','textarea','email','url','number','select','boolean'] as $t)' +
                        '<option value="{{ $t }}">{{ $t }}</option>' +
                        '@endforeach' +
                    '</select></div>' +
                    '<div class="col-md-2"><input type="text" name="fields[' + index + '][placeholder]" class="form-custom" placeholder="{{ __('super-admin.field_placeholder') }}"></div>' +
                    '<div class="col-md-1 d-flex flex-column align-items-center">' + reqHtml + '<span class="small text-muted mt-1">{{ __('super-admin.field_required') }}</span></div>' +
                    '<div class="col-md-1 d-flex flex-column align-items-center">' + encHtml + '<span class="small text-muted mt-1">{{ __('super-admin.field_encrypted') }}</span></div>' +
                    '<div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger remove-field-row"><i class="bi bi-trash"></i></button></div>' +
                '</div>' +
                '<div class="row g-2 mt-1">' +
                    '<div class="col-md-11"><input type="text" name="fields[' + index + '][help]" class="form-custom" placeholder="{{ __('super-admin.field_help') }}"></div>' +
                    '<div class="col-md-1"><input type="hidden" name="fields[' + index + '][maxLength]" value="255"></div>' +
                '</div>' +
                '<div class="row g-2 mt-1 select-options" style="display:none">' +
                    '<div class="col-md-2">' +
                        '<label class="small text-muted mb-1 d-block">{{ __('super-admin.field_default') }}</label>' +
                        '<input type="text" name="fields[' + index + '][default]" class="form-custom" placeholder="{{ __('super-admin.field_default') }}">' +
                    '</div>' +
                    '<div class="col-md-10">' +
                        '<label class="small text-muted mb-1 d-block">{{ __('super-admin.field_options') }}</label>' +
                        '<div class="options-list"></div>' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary add-option" data-fi="' + index + '"><i class="bi bi-plus"></i> {{ __('super-admin.add_option') }}</button>' +
                    '</div>' +
                '</div>';

            row.querySelector('select[name="fields[' + index + '][type]"]').addEventListener('change', function() {
                var opts = row.querySelector('.select-options');
                if (opts) opts.style.display = this.value === 'select' ? '' : 'none';
            });

            row.querySelector('.add-option').addEventListener('click', function() {
                var list = this.parentElement.querySelector('.options-list');
                var oi = getNextOptionIndex(list);
                var optRow = createOptionRow(index, oi, '', '');
                list.appendChild(optRow);
            });

            return row;
        }

        function toggleSelectOptions(typeSelect) {
            var row = typeSelect.closest('.field-row');
            if (!row) return;
            var opts = row.querySelector('.select-options');
            if (opts) opts.style.display = typeSelect.value === 'select' ? '' : 'none';
        }

        addBtn.addEventListener('click', function() {
            var idx = getNextIndex();
            var row = createFieldRow(idx);
            container.appendChild(row);
            row.querySelector('.remove-field-row').addEventListener('click', function() {
                row.remove();
            });
            toggleSelectOptions(row.querySelector('select[name="fields[' + idx + '][type]"]'));
        });

        Array.from(container.querySelectorAll('.remove-field-row')).forEach(function(btn) {
            btn.addEventListener('click', function() {
                btn.closest('.field-row').remove();
            });
        });

        Array.from(container.querySelectorAll('.field-row')).forEach(function(row) {
            var sels = row.querySelectorAll('select');
            for (var s = 0; s < sels.length; s++) {
                if (sels[s].name && sels[s].name.endsWith('[type]')) {
                    toggleSelectOptions(sels[s]);
                    sels[s].addEventListener('change', function() { toggleSelectOptions(this); });
                }
            }
            var addOptBtn = row.querySelector('.add-option');
            if (addOptBtn) {
                addOptBtn.addEventListener('click', function() {
                    var list = this.parentElement.querySelector('.options-list');
                    var fi = this.getAttribute('data-fi');
                    var oi = getNextOptionIndex(list);
                    var optRow = createOptionRow(fi, oi, '', '');
                    list.appendChild(optRow);
                });
            }
            var optRows = row.querySelectorAll('.option-row');
            for (var r = 0; r < optRows.length; r++) {
                bindOptionRow(optRows[r]);
            }
        });
    }

    initFieldBuilder();
    </script>
    @endpush
</x-super-admin-layout>
