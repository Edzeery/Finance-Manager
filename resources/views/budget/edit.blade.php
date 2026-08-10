<x-app-layout>
    <x-slot:title>{{ __('budget.edit') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('budget.edit') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="{{ route('budget.update', $budget) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('budget.single') }} ({{ __('general.ar') }}) <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" value="{{ old('name_ar', $budget->name_ar) }}" class="form-custom @error('name_ar') is-invalid @enderror" required>
                                @error('name_ar') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('budget.single') }} ({{ __('general.fr') }})</label>
                                <input type="text" name="name_fr" value="{{ old('name_fr', $budget->name_fr) }}" class="form-custom @error('name_fr') is-invalid @enderror">
                                @error('name_fr') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('budget.single') }} ({{ __('general.en') }})</label>
                                <input type="text" name="name_en" value="{{ old('name_en', $budget->name_en) }}" class="form-custom @error('name_en') is-invalid @enderror">
                                @error('name_en') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('budget.type') }} <span class="text-danger">*</span></label>
                                <select name="type" class="form-custom @error('type') is-invalid @enderror" required>
                                    <option value="monthly" {{ old('type', $budget->type) === 'monthly' ? 'selected' : '' }}>{{ __('budget.monthly') }}</option>
                                    <option value="yearly" {{ old('type', $budget->type) === 'yearly' ? 'selected' : '' }}>{{ __('budget.yearly') }}</option>
                                    <option value="custom" {{ old('type', $budget->type) === 'custom' ? 'selected' : '' }}>{{ __('budget.custom') }}</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('budget.total_amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="total_amount" value="{{ old('total_amount', $budget->total_amount) }}" class="form-custom" required id="budget_total">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mt-4 pt-2">
                                    <x-toggle-switch name="is_active" :checked="old('is_active', $budget->is_active)" label="{{ __('budget.is_active') }}" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('budget.start_date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" value="{{ old('start_date', $budget->start_date->format('Y-m-d')) }}" class="form-custom" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('budget.end_date') }}</label>
                                <input type="date" name="end_date" value="{{ old('end_date', $budget->end_date?->format('Y-m-d')) }}" class="form-custom">
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('budget.notes') }}</label>
                                <textarea name="notes" class="form-custom" rows="2" maxlength="1000">{{ old('notes', $budget->notes) }}</textarea>
                            </div>

                            <div class="col-12">
                                <hr>
                                <h5 class="fw-bold mb-3"><i class="bi bi-list-ul ms-2"></i>{{ __('budget.categories') }}</h5>
                                @php $selectedCatIds = $budget->categories->pluck('expense_category_id')->toArray(); @endphp
                                @foreach($categories as $cat)
                                    @php
                                        $existing = $budget->categories->firstWhere('expense_category_id', $cat->id);
                                        $idx = $loop->index;
                                        $defaultPct = $cat->default_budget_percentage !== null ? (float) $cat->default_budget_percentage : null;
                                        $oldUsePct = old('categories.' . $idx . '.use_percentage', $existing?->percentage !== null ? '1' : '0');
                                        $isPctMode = $oldUsePct === '1';
                                        $allocated = old('categories.' . $idx . '.allocated_amount', $existing?->allocated_amount ?? '');
                                        $pctVal = old('categories.' . $idx . '.percentage', $existing?->percentage ?? '');
                                    @endphp
                                    <div class="category-row d-flex align-items-center gap-3 mb-2 p-2" style="background:var(--bg); border-radius:8px" data-default-percentage="{{ $defaultPct ?? '' }}">
                                        <span style="flex:1; font-size:14px">
                                            <i class="{{ $cat->icon ?? 'bi-tag' }}" style="color:{{ $cat->color ?? 'var(--text-muted)' }}"></i>
                                            {{ locale_name($cat) }}
                                            @if($defaultPct !== null)
                                                <span class="badge badge-custom ms-1" style="background:rgba(34,197,94,0.12); color:var(--success); font-size:11px">{{ __('budget.default_percentage', ['percent' => $defaultPct]) }}</span>
                                            @endif
                                        </span>
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <div class="btn-group btn-group-sm allocation-toggle" role="group" style="border:1px solid var(--border); border-radius:8px; overflow:hidden">
                                                <button type="button" class="btn mode-btn mode-amount {{ !$isPctMode ? 'active' : '' }}" data-mode="amount" style="font-size:12px">{{ __('budget.amount') }}</button>
                                                <button type="button" class="btn mode-btn mode-percentage {{ $isPctMode ? 'active' : '' }}" data-mode="percentage" style="font-size:12px">{{ __('budget.percentage') }}</button>
                                            </div>
                                            <div class="input-group amount-group" style="width:200px; {{ $isPctMode ? 'display:none' : '' }}">
                                                <input type="hidden" name="categories[{{ $idx }}][category_id]" value="{{ $cat->id }}">
                                                <input type="hidden" name="categories[{{ $idx }}][use_percentage]" class="use-percentage-input" value="{{ $oldUsePct }}">
                                                <input type="number" step="0.01" min="0" name="categories[{{ $idx }}][allocated_amount]" class="form-custom category-amount @error('categories.' . $idx . '.allocated_amount') is-invalid @enderror" style="width:120px" placeholder="0.00" value="{{ $allocated }}">
                                                <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:11px">{{ config('finance.currency_symbol') }}</span>
                                            </div>
                                            <div class="input-group percentage-group" style="width:130px; {{ $isPctMode ? '' : 'display:none' }}">
                                                <input type="number" step="0.01" min="0" max="100" name="categories[{{ $idx }}][percentage]" class="form-custom category-percentage @error('categories.' . $idx . '.percentage') is-invalid @enderror" placeholder="0" value="{{ $pctVal }}">
                                                <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:11px">%</span>
                                            </div>
                                        </div>
                                        @error('categories.' . $idx . '.allocated_amount') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                                        @error('categories.' . $idx . '.percentage') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                                    </div>
                                @endforeach
                                <div class="mt-2 p-2 d-flex justify-content-between" style="background:var(--bg); border-radius:8px; font-size:14px">
                                    <span class="fw-bold">{{ __('budget.total_amount') }}</span>
                                    <span id="allocated-total" class="fw-bold" style="color:var(--accent)">0.00</span>
                                </div>
                                <div class="mt-1 p-2 d-flex justify-content-between" style="background:var(--bg); border-radius:8px; font-size:14px">
                                    <span>{{ __('budget.percentage_sum') }}</span>
                                    <span id="percentage-sum" class="fw-bold" style="color:var(--success)">0.00%</span>
                                </div>
                                @error('categories') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <x-button submit icon="bi bi-check-lg">{{ __('general.save') }}</x-button>
                            <x-button href="{{ route('budget.index') }}" icon="bi bi-x-lg" variant="outline">{{ __('general.cancel') }}</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function parseBudgetTotal() {
        var el = document.getElementById('budget_total');
        return el ? (parseFloat(el.value) || 0) : 0;
    }
    function rowUsePct(row) { return row.querySelector('.use-percentage-input'); }
    function rowAmountInput(row) { return row.querySelector('.category-amount'); }
    function rowPctInput(row) { return row.querySelector('.category-percentage'); }
    function rowIsPercentageMode(row) {
        var u = rowUsePct(row);
        return u ? u.value === '1' : false;
    }
    function computeRowAmount(row) {
        if (!rowIsPercentageMode(row)) return;
        var pct = parseFloat(rowPctInput(row).value) || 0;
        if (pct > 100) { pct = 100; rowPctInput(row).value = 100; }
        rowAmountInput(row).value = (parseBudgetTotal() * pct / 100).toFixed(2);
    }
    function setAllocationMode(row, mode) {
        rowUsePct(row).value = mode === 'percentage' ? '1' : '0';
        row.querySelector('.mode-amount').classList.toggle('active', mode === 'amount');
        row.querySelector('.mode-percentage').classList.toggle('active', mode === 'percentage');
        row.querySelector('.amount-group').style.display = mode === 'percentage' ? 'none' : '';
        row.querySelector('.percentage-group').style.display = mode === 'percentage' ? '' : 'none';
        if (mode === 'percentage') {
            var pctInput = rowPctInput(row);
            if (pctInput.value === '') {
                var def = row.dataset.defaultPercentage;
                pctInput.value = (def !== '' && def !== undefined) ? def : '';
            }
        }
        computeRowAmount(row);
        updateAllocatedTotal();
    }
    function updateAllocatedTotal() {
        let total = 0;
        document.querySelectorAll('.category-amount').forEach(function(input) {
            total += parseFloat(input.value) || 0;
        });
        var el = document.getElementById('allocated-total');
        if (el) el.textContent = total.toFixed(2);

        var pctTotal = 0;
        var over = false;
        document.querySelectorAll('.category-percentage').forEach(function(input) {
            var group = input.closest('.percentage-group');
            if (group && group.style.display === 'none') return;
            pctTotal += parseFloat(input.value) || 0;
            if ((parseFloat(input.value) || 0) > 100) over = true;
        });
        var sumEl = document.getElementById('percentage-sum');
        if (sumEl) {
            sumEl.textContent = pctTotal.toFixed(2) + '%';
            sumEl.style.color = (pctTotal > 100 || over) ? 'var(--danger)' : 'var(--success)';
        }
    }
    function initBudgetForm() {
        document.querySelectorAll('.category-row').forEach(function(row) {
            row.querySelectorAll('.mode-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    setAllocationMode(row, btn.dataset.mode);
                });
            });
            var amountInput = rowAmountInput(row);
            if (amountInput) amountInput.addEventListener('input', updateAllocatedTotal);
            var pctInput = rowPctInput(row);
            if (pctInput) {
                pctInput.addEventListener('input', function() {
                    var v = parseFloat(pctInput.value) || 0;
                    if (v > 100) { pctInput.value = 100; }
                    if (v < 0) { pctInput.value = 0; }
                    computeRowAmount(row);
                    updateAllocatedTotal();
                });
            }
            setAllocationMode(row, rowIsPercentageMode(row) ? 'percentage' : 'amount');
        });

        var totalInput = document.getElementById('budget_total');
        if (totalInput) {
            totalInput.addEventListener('input', function() {
                document.querySelectorAll('.category-row').forEach(function(row) {
                    if (rowIsPercentageMode(row)) computeRowAmount(row);
                });
                updateAllocatedTotal();
            });
        }

        updateAllocatedTotal();
    }
    initBudgetForm();
    </script>
    @endpush
</x-app-layout>
