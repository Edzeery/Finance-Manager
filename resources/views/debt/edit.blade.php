<x-app-layout>
    <x-slot:title>{{ __('debt.edit') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('debt.edit') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="{{ route('debt.update', $debt) }}" method="POST">
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
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-check-lg ms-1"></i>{{ __('general.save') }}
                            </button>
                            <a href="{{ route('debt.index') }}" class="btn btn-outline-secondary btn-custom">
                                <i class="bi bi-x-lg"></i>{{ __('general.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
