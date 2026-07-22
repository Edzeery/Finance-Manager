<x-app-layout>
    <x-slot:title>{{ __('expense.add') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('expense.add') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="{{ route('expense.store') }}" method="POST"
                          x-data="expenseForm()" x-ref="expenseForm">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('expense.category') }} <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-custom @error('category_id') is-invalid @enderror" required
                                        x-on:change="checkBudget($event.target.value)">
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
                                <label class="form-label-custom">{{ __('expense.amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" class="form-custom @error('amount') is-invalid @enderror" required placeholder="0.00">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                                @error('amount') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            {{-- Budget Status Banner --}}
                            <div class="col-12" x-show="showBanner" x-transition x-cloak>
                                {{-- Has Budget --}}
                                <template x-if="budgetStatus === true">
                                    <div class="d-flex align-items-center gap-3 p-3" style="background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.2); border-radius:10px; font-size:13px">
                                        <i class="bi bi-pie-chart-fill" style="color:#3B82F6; font-size:18px"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold mb-1" style="color:#3B82F6">{{ __('expense.budget_found') }}</div>
                                            <div class="d-flex gap-3 flex-wrap">
                                                <span>{{ __('budget.allocated') }}: <strong x-text="budgetInfo.allocated?.toFixed(2)"></strong></span>
                                                <span>{{ __('budget.spent') }}: <strong x-text="budgetInfo.spent?.toFixed(2)"></strong></span>
                                                <span style="color:var(--success)">{{ __('budget.remaining') }}: <strong x-text="budgetInfo.remaining?.toFixed(2)"></strong></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- No Budget --}}
                                <template x-if="budgetStatus === false">
                                    <div class="p-3" style="background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.2); border-radius:10px">
                                        <div class="d-flex align-items-start gap-3">
                                            <i class="bi bi-exclamation-triangle-fill" style="color:#EF4444; font-size:18px; margin-top:2px"></i>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold mb-2" style="color:#EF4444">{{ __('expense.no_budget_warning') }}</div>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="{{ route('budget.create') }}" wire:navigate
                                                       class="btn btn-sm btn-custom"
                                                       style="background:#3B82F6; color:#fff; font-size:12px; padding:4px 12px; border-radius:6px">
                                                        <i class="bi bi-plus-lg ms-1"></i>{{ __('expense.create_budget_now') }}
                                                    </a>
                                                    <button type="button" x-on:click="enableDebt()"
                                                            class="btn btn-sm btn-custom"
                                                            style="background:#F59E0B; color:#fff; font-size:12px; padding:4px 12px; border-radius:6px">
                                                        <i class="bi bi-credit-card ms-1"></i>{{ __('expense.register_as_debt') }}
                                                    </button>
                                                    <button type="button" x-on:click="dismissWarning()"
                                                            class="btn btn-sm btn-custom"
                                                        style="background:var(--bg); color:var(--text-muted); border:1px solid var(--border); font-size:12px; padding:4px 12px; border-radius:6px">
                                                        <i class="bi bi-arrow-right ms-1"></i>{{ __('expense.continue_without_budget') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('expense.date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="form-custom @error('date') is-invalid @enderror" required>
                                @error('date') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('expense.description') }}</label>
                                <input type="text" name="description" value="{{ old('description') }}" class="form-custom" placeholder="{{ __('expense.description') }}" maxlength="1000">
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <x-toggle-switch name="is_recurring" id="is_recurring" :checked="old('is_recurring')" label="{{ __('expense.is_recurring') }}" />
                                </div>
                            </div>

                            <div id="recurringFields" class="col-12 row g-3" style="display:{{ old('is_recurring') ? 'flex' : 'none' }}">
                                <div class="col-md-6">
                                    <label class="form-label-custom">{{ __('expense.frequency') }}</label>
                                    <select name="recurring_frequency" class="form-custom">
                                        <option value="{{ \App\Enums\RecurringFrequency::Monthly->value }}" {{ old('recurring_frequency') === \App\Enums\RecurringFrequency::Monthly->value ? 'selected' : '' }}>{{ __('general.monthly') }}</option>
                                        <option value="{{ \App\Enums\RecurringFrequency::Weekly->value }}" {{ old('recurring_frequency') === \App\Enums\RecurringFrequency::Weekly->value ? 'selected' : '' }}>{{ __('general.weekly') }}</option>
                                        <option value="{{ \App\Enums\RecurringFrequency::Yearly->value }}" {{ old('recurring_frequency') === \App\Enums\RecurringFrequency::Yearly->value ? 'selected' : '' }}>{{ __('general.yearly') }}</option>
                                        <option value="{{ \App\Enums\RecurringFrequency::Daily->value }}" {{ old('recurring_frequency') === \App\Enums\RecurringFrequency::Daily->value ? 'selected' : '' }}>{{ __('general.daily') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">{{ __('expense.recurring_end_date') }}</label>
                                    <input type="date" name="recurring_end_date" value="{{ old('recurring_end_date') }}" class="form-custom">
                                </div>
                            </div>

                            <input type="hidden" name="is_new_debt" value="0" x-ref="isDebtHidden">

                            <div class="col-12 row g-3" x-show="showDebtFields" x-transition x-cloak>
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-2 mb-2 p-2" style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); border-radius:8px; font-size:13px">
                                        <i class="bi bi-info-circle-fill" style="color:#F59E0B"></i>
                                        <span style="color:#92400E">{{ __('expense.debt_info_message') }}</span>
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
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('expense.notes') }}</label>
                                <textarea name="notes" class="form-custom" rows="3" placeholder="{{ __('expense.notes') }}" maxlength="1000">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-check-lg ms-1"></i>{{ __('general.save') }}
                            </button>
                            <a href="{{ route('expense.index') }}" class="btn btn-outline-secondary btn-custom">
                                <i class="bi bi-x-lg"></i>{{ __('general.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function expenseForm() {
        return {
            budgetStatus: null,
            budgetInfo: null,
            showBanner: false,
            showDebtFields: false,
            checkingBudget: false,

            init() {
                this.$nextTick(() => {
                    var selected = this.$el.querySelector('select[name="category_id"]').value;
                    if (selected) this.checkBudget(selected);
                });
            },

            enableDebt() {
                this.showDebtFields = true;
                this.showBanner = false;
                this.$refs.isDebtHidden.value = '1';
            },

            dismissWarning() {
                this.showBanner = false;
                this.showDebtFields = false;
                this.$refs.isDebtHidden.value = '0';
            },

            async checkBudget(categoryId) {
                this.showDebtFields = false;
                this.$refs.isDebtHidden.value = '0';

                if (!categoryId) {
                    this.budgetStatus = null;
                    this.budgetInfo = null;
                    this.showBanner = false;
                    return;
                }

                this.checkingBudget = true;
                this.budgetStatus = null;
                this.budgetInfo = null;

                try {
                    const res = await fetch(`/expense-categories/${categoryId}/budget-status`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });
                    const data = await res.json();
                    this.budgetStatus = data.has_budget;
                    this.budgetInfo = data;
                    this.showBanner = true;
                } catch (e) {
                    this.budgetStatus = null;
                    this.showBanner = false;
                }

                this.checkingBudget = false;
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

    document.addEventListener('livewire:navigated', function() {
        toggleRecurring();
    });
    </script>
    @endpush
</x-app-layout>
