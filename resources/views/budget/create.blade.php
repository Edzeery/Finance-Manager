<x-app-layout>
    <x-slot:title>{{ __('budget.add') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('budget.add') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="{{ route('budget.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('budget.single') }} ({{ __('general.ar') }}) <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="form-custom @error('name_ar') is-invalid @enderror" required maxlength="255">
                                @error('name_ar') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('budget.single') }} ({{ __('general.fr') }})</label>
                                <input type="text" name="name_fr" value="{{ old('name_fr') }}" class="form-custom @error('name_fr') is-invalid @enderror" maxlength="255">
                                @error('name_fr') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('budget.single') }} ({{ __('general.en') }})</label>
                                <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-custom @error('name_en') is-invalid @enderror" maxlength="255">
                                @error('name_en') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('budget.type') }} <span class="text-danger">*</span></label>
                                <select name="type" class="form-custom @error('type') is-invalid @enderror" required>
                                    <option value="monthly" {{ old('type') === 'monthly' ? 'selected' : '' }}>{{ __('budget.monthly') }}</option>
                                    <option value="yearly" {{ old('type') === 'yearly' ? 'selected' : '' }}>{{ __('budget.yearly') }}</option>
                                    <option value="custom" {{ old('type') === 'custom' ? 'selected' : '' }}>{{ __('budget.custom') }}</option>
                                </select>
                                @error('type') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('budget.total_amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="total_amount" value="{{ old('total_amount') }}" class="form-custom @error('total_amount') is-invalid @enderror" required placeholder="0.00" id="budget_total">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                                @error('total_amount') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <div class="mt-4 pt-2">
                                    <x-toggle-switch name="is_active" :checked="old('is_active', '1')" label="{{ __('budget.is_active') }}" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('budget.start_date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" class="form-custom @error('start_date') is-invalid @enderror" required id="budget_start_date">
                                @error('start_date') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('budget.end_date') }}</label>
                                <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-custom @error('end_date') is-invalid @enderror" id="budget_end_date">
                                <small class="text-muted">{{ __('budget.auto_end_date_hint') }}</small>
                                @error('end_date') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('budget.notes') }}</label>
                                <textarea name="notes" class="form-custom" rows="2" maxlength="1000">{{ old('notes') }}</textarea>
                            </div>

                            <div class="col-12">
                                <hr>
                                <h5 class="fw-bold mb-3"><i class="bi bi-list-ul me-2"></i>{{ __('budget.categories') }}</h5>
                                <div id="categories-container">
                                    @foreach($categories as $cat)
                                        <div class="category-row d-flex align-items-center gap-3 mb-2 p-2" style="background:var(--bg); border-radius:8px">
                                            <span style="flex:1; font-size:14px">
                                                <i class="{{ $cat->icon ?? 'bi-tag' }}" style="color:{{ $cat->color ?? '#64748B' }}"></i>
                                                {{ locale_name($cat) }}
                                            </span>
                                            <div class="input-group" style="width:200px">
                                                <input type="hidden" name="categories[{{ $loop->index }}][category_id]" value="{{ $cat->id }}">
                                                <input type="number" step="0.01" min="0" name="categories[{{ $loop->index }}][allocated_amount]" class="form-custom category-amount @error('categories.{{ $loop->index }}.allocated_amount') is-invalid @enderror" style="width:120px" placeholder="0.00" value="{{ old('categories.' . $loop->index . '.allocated_amount') }}">
                                                <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:11px">{{ config('finance.currency_symbol') }}</span>
                                            </div>
                                            @error('categories.{{ $loop->index }}.allocated_amount') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-2 p-2 d-flex justify-content-between" style="background:var(--bg); border-radius:8px; font-size:14px">
                                    <span class="fw-bold">{{ __('budget.total_amount') }}</span>
                                    <span id="allocated-total" class="fw-bold" style="color:var(--accent)">0.00</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-check-lg me-1"></i>{{ __('general.save') }}
                            </button>
                            <a href="{{ route('budget.index') }}" class="btn btn-outline-secondary btn-custom">
                                <i class="bi bi-x-lg me-1"></i>{{ __('general.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function updateAllocatedTotal() {
        let total = 0;
        document.querySelectorAll('.category-amount').forEach(function(input) {
            total += parseFloat(input.value) || 0;
        });
        var el = document.getElementById('allocated-total');
        if (el) el.textContent = total.toFixed(2);
    }
    function autoCalcEndDate() {
        var type = document.querySelector('select[name="type"]').value;
        var startDate = document.getElementById('budget_start_date').value;
        var endDateField = document.getElementById('budget_end_date');

        if (!startDate || type === 'custom') return;

        var start = new Date(startDate);
        var end = new Date(start);

        if (type === 'monthly') {
            end.setMonth(end.getMonth() + 1);
            end.setDate(end.getDate() - 1);
        } else if (type === 'yearly') {
            end.setFullYear(end.getFullYear() + 1);
            end.setDate(end.getDate() - 1);
        }

        endDateField.value = end.toISOString().split('T')[0];
    }
    function initBudgetForm() {
        document.querySelectorAll('.category-amount').forEach(function(input) {
            input.addEventListener('input', updateAllocatedTotal);
        });
        var typeSelect = document.querySelector('select[name="type"]');
        var startInput = document.getElementById('budget_start_date');
        if (typeSelect) typeSelect.addEventListener('change', autoCalcEndDate);
        if (startInput) startInput.addEventListener('change', autoCalcEndDate);
        autoCalcEndDate();
    }
    initBudgetForm();
    </script>
    @endpush
</x-app-layout>
