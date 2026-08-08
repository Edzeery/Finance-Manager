<x-app-layout>
    <x-slot:title>{{ __('income.add') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('income.add') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="{{ route('income.store') }}" method="POST"
                          x-data="incomeForm()"
                          @change="if ($event.target.id === 'is_new_debt_hidden') showDebtFields = ($event.target.value === '1')">
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

                            <div class="col-12">
                                <div class="mb-1">
                                    <x-toggle-switch name="is_new_debt" id="is_new_debt" :checked="old('is_new_debt')" label="{{ __('income.register_as_debt') }}" hint="{{ __('income.register_as_debt_hint') }}" />
                                </div>
                            </div>

                            <div class="col-12 row g-3" x-show="showDebtFields" x-transition x-cloak>
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-2 mb-2 p-2" style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); border-radius:8px; font-size:13px">
                                        <i class="bi bi-info-circle-fill" style="color:var(--warning)"></i>
                                        <span style="color:var(--text-secondary)">{{ __('income.debt_info_message') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">{{ __('debt.counterparty_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="debt_counterparty" value="{{ old('debt_counterparty') }}" class="form-custom @error('debt_counterparty') is-invalid @enderror" placeholder="{{ __('debt.counterparty_name') }}" :required="showDebtFields">
                                    @error('debt_counterparty') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">{{ __('debt.due_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="debt_due_date" value="{{ old('debt_due_date') }}" class="form-custom @error('debt_due_date') is-invalid @enderror" :required="showDebtFields">
                                    @error('debt_due_date') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <x-toggle-switch name="count_at_incurrence" id="count_at_incurrence" :checked="old('count_at_incurrence')" label="{{ __('debt.count_at_incurrence') }}" hint="{{ __('debt.count_at_incurrence_hint') }}" />
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <x-button submit icon="bi bi-check-lg">{{ __('general.save') }}</x-button>
                            <x-button href="{{ route('income.index') }}" icon="bi bi-x-lg" variant="outline">{{ __('general.cancel') }}</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function incomeForm() {
        return {
            showDebtFields: false,
            init() {
                this.$nextTick(() => {
                    var el = document.getElementById('is_new_debt_hidden');
                    if (el && el.value === '1') this.showDebtFields = true;
                });
            }
        };
    }

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
