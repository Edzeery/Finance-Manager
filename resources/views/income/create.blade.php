<x-app-layout>
    <x-slot:title>{{ __('income.add') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('income.add') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="{{ route('income.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('income.category') }} <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-custom @error('category_id') is-invalid @enderror" required>
                                    <option value="">{{ __('general.select') }}</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ locale_name($cat) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('income.amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" class="form-custom @error('amount') is-invalid @enderror" required placeholder="0.00">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                                @error('amount') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('income.date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="form-custom @error('date') is-invalid @enderror" required>
                                @error('date') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('income.description') }}</label>
                                <input type="text" name="description" value="{{ old('description') }}" class="form-custom" maxlength="1000">
                            </div>

                            <div class="col-12">
                                <div class="mb-1">
                                    <x-toggle-switch name="is_recurring" id="is_recurring" :checked="old('is_recurring')" label="{{ __('income.is_recurring') }}" hint="{{ __('income.recurring_help') }}" />
                                </div>
                            </div>

                            <div id="recurringFields" class="col-12 row g-3" style="display:{{ old('is_recurring') ? 'flex' : 'none' }}">
                                <div class="col-md-6">
                                    <label class="form-label-custom">{{ __('income.frequency') }}</label>
                                    <select name="recurring_frequency" class="form-custom @error('recurring_frequency') is-invalid @enderror">
                                        <option value="">{{ __('general.select') }}</option>
                                        <option value="{{ \App\Enums\RecurringFrequency::Monthly->value }}" {{ old('recurring_frequency') === \App\Enums\RecurringFrequency::Monthly->value ? 'selected' : '' }}>{{ __('income.monthly') }}</option>
                                        <option value="{{ \App\Enums\RecurringFrequency::Weekly->value }}" {{ old('recurring_frequency') === \App\Enums\RecurringFrequency::Weekly->value ? 'selected' : '' }}>{{ __('income.weekly') }}</option>
                                        <option value="{{ \App\Enums\RecurringFrequency::Yearly->value }}" {{ old('recurring_frequency') === \App\Enums\RecurringFrequency::Yearly->value ? 'selected' : '' }}>{{ __('income.yearly') }}</option>
                                        <option value="{{ \App\Enums\RecurringFrequency::Daily->value }}" {{ old('recurring_frequency') === \App\Enums\RecurringFrequency::Daily->value ? 'selected' : '' }}>{{ __('income.daily') }}</option>
                                    </select>
                                    @error('recurring_frequency') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">{{ __('income.recurring_end_date') }}</label>
                                    <input type="date" name="recurring_end_date" value="{{ old('recurring_end_date') }}" class="form-custom @error('recurring_end_date') is-invalid @enderror">
                                    @error('recurring_end_date') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('income.notes') }}</label>
                                <textarea name="notes" class="form-custom" rows="3" maxlength="1000">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-check-lg me-1"></i>{{ __('general.save') }}
                            </button>
                            <a href="{{ route('income.index') }}" class="btn btn-outline-secondary btn-custom">
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
    function toggleRecurring() {
        var hidden = document.getElementById('is_recurring_hidden');
        document.getElementById('recurringFields').style.display =
            hidden && hidden.value === '1' ? 'flex' : 'none';
    }
    document.addEventListener('click', function(e) {
        var btn = e.target.closest && e.target.closest('#is_recurring');
        if (btn) setTimeout(toggleRecurring, 10);
    });
    toggleRecurring();
    </script>
    @endpush
</x-app-layout>
