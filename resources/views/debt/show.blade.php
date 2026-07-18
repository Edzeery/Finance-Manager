<x-app-layout>
    <x-slot:title>{{ $debt->counterparty_name }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ $debt->counterparty_name }}</x-slot>
    <x-slot:page-description>
        <x-status-badge domain="debt_type" :status="$debt->type->value" set="bi" />
        &nbsp;| {{ __('debt.remaining_amount') }}: <strong>{{ number_format($debt->remaining_amount, 2) }} {{ config('finance.currency_symbol') }}</strong>
    </x-slot>


    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-box stat-income">
                <div class="stat-label">{{ __('debt.total_amount') }}</div>
                <div class="stat-value">{{ number_format($debt->total_amount, 2) }}</div>
                <small style="color:var(--text-muted)">{{ config('finance.currency_symbol') }}</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box stat-expense">
                <div class="stat-label">{{ __('debt.remaining_amount') }}</div>
                <div class="stat-value">{{ number_format($debt->remaining_amount, 2) }}</div>
                <small style="color:var(--text-muted)">{{ config('finance.currency_symbol') }}</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box" style="background:var(--bg)">
                <div class="stat-label">{{ __('general.status') }}</div>
                <div class="d-flex align-items-center gap-2">
                    <div class="progress" style="flex:1; height:8px; background:var(--border)">
                        <div class="progress-bar" style="width:{{ $debt->progress }}%; background:{{ $debt->progress >= 100 ? 'var(--success)' : 'var(--accent)' }}; border-radius:4px"></div>
                    </div>
                    <span class="fw-bold" style="font-size:14px">{{ $debt->progress }}%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history"></i>
                        <span>{{ __('debt.payment_history') }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($debt->payments->count())
                        <div class="table-responsive">
                            <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>{{ __('debt.payment_date') }}</th>
                                    <th class="text-end">{{ __('debt.payment_amount') }}</th>
                                    <th>{{ __('debt.payment_notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($debt->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('Y/m/d') }}</td>
                                        <td text-start fw-bold style="color:var(--success)">-{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->notes ?: 'â€”' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    @else
                        <div class="p-4 text-center" style="color:var(--text-muted); font-size:14px">
                            <x-empty-state icon="bi bi-inbox" :title="__('general.no_data')" />
                        </div>
                    @endif
                </div>
            </div>

                    @if($debt->status !== \App\Enums\DebtStatus::Paid)
                <div class="card-custom mt-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle"></i>
                        <span>{{ __('debt.add_payment') }}</span>
                    </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('debt.payments.store', $debt) }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('debt.payment_amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-custom" required placeholder="0.00" max="{{ $debt->remaining_amount }}">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('debt.payment_date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-custom" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('debt.payment_notes') }}</label>
                                <input type="text" name="notes" class="form-custom" maxlength="1000">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-accent btn-custom">
                                    <i class="bi bi-check-lg me-1"></i>{{ __('debt.add_payment') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle"></i>
                        <span>{{ __('debt.single') }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">{{ __('debt.counterparty') }}</span>
                        <span class="info-value">{{ $debt->counterparty_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('debt.due_date') }}</span>
                        <span class="info-value">{{ $debt->due_date?->format('Y/m/d') ?: 'â€”' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('debt.reminder_date') }}</span>
                        <span class="info-value">{{ $debt->reminder_date?->format('Y/m/d') ?: 'â€”' }}</span>
                    </div>
                    @if($debt->description)
                        <div class="info-row">
                            <span class="info-label">{{ __('debt.description') }}</span>
                            <span class="info-value">{{ $debt->description }}</span>
                        </div>
                    @endif
                    @if($debt->notes)
                        <div class="info-row">
                            <span class="info-label">{{ __('debt.notes') }}</span>
                            <span class="info-value">{{ $debt->notes }}</span>
                        </div>
                    @endif
                    @php $canEditDebt = auth()->user()->hasPermission('debt.update'); $canDeleteDebt = auth()->user()->hasPermission('debt.delete'); @endphp
                    <div class="d-flex gap-2 mt-3">
                        @if($canEditDebt)
                            <a href="{{ route('debt.edit', $debt) }}" class="btn btn-outline-secondary btn-custom" style="flex:1">
                                <i class="bi bi-pencil me-1"></i>{{ __('general.edit') }}
                            </a>
                        @endif
                        @if($canDeleteDebt)
                            <form action="{{ route('debt.destroy', $debt) }}" method="POST" id="delete-debt-{{ $debt->id }}" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-outline-danger btn-custom w-100" @click="window.confirmDelete('debt', {{ $debt->id }})">
                                <i class="bi bi-trash me-1"></i>{{ __('general.delete') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
