<x-app-layout>
    <x-slot:title>{{ __('debt.edit') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('debt.edit') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="{{ route('debt.update', $debt) }}" method="POST"
                          x-data="debtForm()"
                          @change="if ($event.target && $event.target.name === 'type') debtType = $event.target.value">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('debt.type') }} <span class="text-danger">*</span></label>
                                <x-status-select
                                    domain="debt_type"
                                    name="type"
                                    :selected="old('type', $debt->type->value)"
                                    size="md"
                                    set="bi"
                                />
                                @error('type') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('debt.counterparty') }} <span class="text-danger">*</span></label>
                                <input type="text" name="counterparty_name" value="{{ old('counterparty_name', $debt->counterparty_name) }}" class="form-custom @error('counterparty_name') is-invalid @enderror" required maxlength="255">
                                @error('counterparty_name') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6" x-show="debtType === 'owing'" x-cloak>
                                <label class="form-label-custom">{{ __('debt.expense_category') }}</label>
                                <select name="expense_category_id" class="form-custom @error('expense_category_id') is-invalid @enderror">
                                    <option value="">{{ __('general.select') }}</option>
                                    @foreach($expenseCategories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('expense_category_id', $debt->expense_category_id) == $cat->id ? 'selected' : '' }}>{{ locale_name($cat) }}</option>
                                    @endforeach
                                </select>
                                @error('expense_category_id') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6" x-show="debtType === 'owed'" x-cloak>
                                <label class="form-label-custom">{{ __('debt.income_category') }}</label>
                                <select name="income_category_id" class="form-custom @error('income_category_id') is-invalid @enderror">
                                    <option value="">{{ __('general.select') }}</option>
                                    @foreach($incomeCategories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('income_category_id', $debt->income_category_id) == $cat->id ? 'selected' : '' }}>{{ locale_name($cat) }}</option>
                                    @endforeach
                                </select>
                                @error('income_category_id') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <x-toggle-switch name="count_at_incurrence" id="count_at_incurrence" :checked="old('count_at_incurrence', $debt->count_at_incurrence)" label="{{ __('debt.count_at_incurrence') }}" hint="{{ __('debt.count_at_incurrence_hint') }}" />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('debt.total_amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="total_amount" value="{{ old('total_amount', $debt->total_amount) }}" class="form-custom @error('total_amount') is-invalid @enderror" required>
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                                @error('total_amount') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('debt.paid_amount') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="paid_amount" value="{{ old('paid_amount', $debt->paid_amount) }}" class="form-custom @error('paid_amount') is-invalid @enderror">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                                @error('paid_amount') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('debt.due_date') }}</label>
                                <input type="date" name="due_date" value="{{ old('due_date', $debt->due_date?->format('Y-m-d')) }}" class="form-custom @error('due_date') is-invalid @enderror">
                                @error('due_date') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('debt.reminder_date') }}</label>
                                <input type="date" name="reminder_date" value="{{ old('reminder_date', $debt->reminder_date?->format('Y-m-d')) }}" class="form-custom @error('reminder_date') is-invalid @enderror">
                                @error('reminder_date') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('debt.description') }}</label>
                                <textarea name="description" class="form-custom" rows="3" maxlength="1000">{{ old('description', $debt->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('debt.notes') }}</label>
                                <textarea name="notes" class="form-custom" rows="3" maxlength="1000">{{ old('notes', $debt->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <x-button submit icon="bi bi-check-lg">{{ __('general.save') }}</x-button>
                            <x-button href="{{ route('debt.index') }}" icon="bi bi-x-lg" variant="outline">{{ __('general.cancel') }}</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function debtForm() {
        return {
            debtType: '{{ $debt->type->value }}',
            init() {
                this.$nextTick(() => {
                    var el = document.querySelector('input[name="type"]');
                    if (el && el.value) this.debtType = el.value;
                });
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
