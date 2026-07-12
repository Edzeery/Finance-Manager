<x-app-layout>
    <x-slot:title>{{ __('expense.edit') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('expense.edit') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="{{ route('expense.update', $expense) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('expense.category') }} <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-custom @error('category_id') is-invalid @enderror" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $expense->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ locale_name($cat) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('expense.amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount', $expense->amount) }}" class="form-custom @error('amount') is-invalid @enderror" required>
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                                @error('amount') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('expense.date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="date" value="{{ old('date', $expense->date->format('Y-m-d')) }}" class="form-custom @error('date') is-invalid @enderror" required>
                                @error('date') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('expense.description') }}</label>
                                <input type="text" name="description" value="{{ old('description', $expense->description) }}" class="form-custom" maxlength="1000">
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <x-toggle-switch name="is_recurring" id="is_recurring" :checked="old('is_recurring', $expense->is_recurring)" label="{{ __('expense.is_recurring') }}" />
                                </div>
                            </div>

                            <div id="recurringFields" class="col-12 row g-3" style="display:{{ old('is_recurring', $expense->is_recurring) ? 'flex' : 'none' }}">
                                <div class="col-md-6">
                                    <label class="form-label-custom">{{ __('expense.frequency') }}</label>
                                    <select name="recurring_frequency" class="form-custom">
                                        @foreach(\App\Enums\RecurringFrequency::cases() as $freq)
                                            <option value="{{ $freq->value }}" {{ (old('recurring_frequency') ?? $expense->recurring_frequency) === $freq->value ? 'selected' : '' }}>
                                                {{ __("general.{$freq->value}") }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">{{ __('expense.recurring_end_date') }}</label>
                                    <input type="date" name="recurring_end_date" value="{{ old('recurring_end_date', $expense->recurring_end_date?->format('Y-m-d')) }}" class="form-custom">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('expense.notes') }}</label>
                                <textarea name="notes" class="form-custom" rows="3" maxlength="1000">{{ old('notes', $expense->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-check-lg me-1"></i>{{ __('general.save') }}
                            </button>
                            <a href="{{ route('expense.index') }}" class="btn btn-outline-secondary btn-custom">
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
