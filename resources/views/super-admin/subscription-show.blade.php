<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.subscriptions') }} #{{ $subscription->id }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.subscriptions') }} #{{ $subscription->id }}</x-slot>
    <x-slot:page-description>{{ $subscription->workspace?->name ?? __('general.unknown') }} &mdash; {{ $subscription->plan?->name ?? '—' }}</x-slot>

    <div class="detail-grid">
        <div class="detail-main">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-info-circle"></i>{{ __('super-admin.subscription_details') }}</h5>
                </div>
                <div class="section-card-body">
                    <table class="info-table">
                        <tr>
                            <td class="info-label">{{ __('super-admin.workspace') }}</td>
                            <td class="info-value">{{ $subscription->workspace?->name ?? __('general.unknown') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('settings.plan') }}</td>
                            <td class="info-value"><span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:2px 10px;border-radius:6px">{{ $subscription->plan?->name ?? '—' }}</span></td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('general.status') }}</td>
                            <td class="info-value">
                                @php $sc = ['active' => ['bg' => 'var(--success-light)', 'c' => 'var(--success)'], 'trialing' => ['bg' => 'var(--info-light)', 'c' => 'var(--info)'], 'past_due' => ['bg' => 'var(--warning-light)', 'c' => 'var(--warning)'], 'canceled' => ['bg' => 'var(--border)', 'c' => 'var(--text-muted)'], 'expired' => ['bg' => 'var(--danger-light)', 'c' => 'var(--danger)']]; @endphp
                                <span class="badge" style="font-size:10px;background:{{ $sc[$subscription->status->value]['bg'] ?? 'var(--border)' }};color:{{ $sc[$subscription->status->value]['c'] ?? 'var(--text-muted)' }};padding:3px 12px;border-radius:6px;font-weight:600">{{ $subscription->status->label() }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('super-admin.started') }}</td>
                            <td class="info-value">{{ $subscription->starts_at?->format('Y/m/d H:i') ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('super-admin.ends_at') }}</td>
                            <td class="info-value">{{ $subscription->ends_at?->format('Y/m/d H:i') ?? '—' }}</td>
                        </tr>
                        @if($subscription->trial_ends_at)
                        <tr>
                            <td class="info-label">{{ __('general.days_left') }}</td>
                            <td class="info-value">{{ $subscription->daysRemaining() }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="info-label">{{ __('super-admin.auto_renew') }}</td>
                            <td class="info-value">
                                @if($subscription->auto_renew)
                                    <span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.yes') }}</span>
                                @else
                                    <span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.no') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('general.type') }}</td>
                            <td class="info-value">{{ $subscription->payment_method ? __("super-admin.{$subscription->payment_method}") : '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-receipt"></i>{{ __('super-admin.invoices') }}</h5>
                </div>
                <div class="section-card-body p-0">
                    @if($subscription->invoices->count())
                        <div style="overflow-x:auto">
                            <table class="data-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('super-admin.invoice_number') }}</th>
                                        <th>{{ __('super-admin.invoice_amount') }}</th>
                                        <th>{{ __('general.status') }}</th>
                                        <th>{{ __('general.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subscription->invoices as $inv)
                                    <tr>
                                        <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px">{{ $inv->number }}</code></td>
                                        <td><strong>{{ number_format($inv->total, 2) }} {{ $inv->currency ?? config('finance.currency_symbol') }}</strong></td>
                                        <td>
                                            @php $bi = ['paid' => ['bg' => 'var(--success-light)', 'c' => 'var(--success)'], 'draft' => ['bg' => 'var(--warning-light)', 'c' => 'var(--warning)'], 'overdue' => ['bg' => 'var(--danger-light)', 'c' => 'var(--danger)']]; $b = $bi[$inv->status->value] ?? ['bg' => 'var(--border)', 'c' => 'var(--text-muted)']; @endphp
                                            <span class="badge" style="font-size:10px;background:{{ $b['bg'] }};color:{{ $b['c'] }};padding:3px 10px;border-radius:6px;font-weight:600">{{ $inv->status->label() }}</span>
                                        </td>
                                        <td class="cell-muted">{{ $inv->created_at->format('Y/m/d') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-receipt"></i></div>
                            <h4>{{ __('super-admin.no_invoices') }}</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="detail-sidebar">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-lightning-fill"></i>{{ __('general.actions') }}</h5>
                </div>
                <div class="section-card-body d-flex flex-column gap-2">
                    <form method="POST" action="{{ route('super.admin.subscriptions.cancel', $subscription) }}" id="cancel-subscription-{{ $subscription->id }}" style="display:none">
                        @csrf
                    </form>
                    <button type="button" class="btn" style="width:100%;padding:8px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--danger);background:transparent;color:var(--danger);font-weight:500;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px" @click="confirmCancelSubscription({{ $subscription->id }})" {{ $subscription->status === \App\Enums\SubscriptionStatus::Canceled || $subscription->status === \App\Enums\SubscriptionStatus::Expired ? 'disabled' : '' }}>
                        <i class="bi bi-x-circle"></i>{{ __('settings.cancel_subscription') }}
                    </button>
                    <form method="POST" action="{{ route('super.admin.subscriptions.toggle-renew', $subscription) }}">
                        @csrf
                        <button type="submit" class="btn" style="width:100%;padding:8px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px">
                            <i class="bi bi-arrow-repeat"></i>
                            {{ $subscription->auto_renew ? __('super-admin.auto_renew') . ': ' . __('general.no') : __('super-admin.auto_renew') . ': ' . __('general.yes') }}
                        </button>
                    </form>
                    @if($subscription->isActive() && $plans->count() > 1)
                    <form method="POST" action="{{ route('super.admin.subscriptions.change-plan', $subscription) }}">
                        @csrf
                        <div class="d-flex gap-2">
                            <select name="subscription_plan_id" class="form-control" style="flex:1;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ $plan->id === $subscription->subscription_plan_id ? 'selected' : '' }}>{{ $plan->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>

            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-cash-coin"></i>{{ __('super-admin.payments') }}</h5>
                </div>
                <div class="section-card-body p-0">
                    @if($subscription->payments->count())
                        @foreach($subscription->payments->take(10) as $payment)
                            <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid var(--border-light)">
                                <div>
                                    <strong style="font-size:13px">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</strong>
                                    <div class="cell-muted" style="font-size:11px">{{ __("super-admin.{$payment->method}") }} &middot; {{ $payment->paid_at?->format('Y/m/d') ?? $payment->created_at->format('Y/m/d') }}</div>
                                </div>
                                @php $pc = ['completed' => 'var(--success)', 'pending' => 'var(--warning)', 'failed' => 'var(--danger)', 'refunded' => 'var(--info)']; @endphp
                                <span class="badge" style="font-size:10px;background:var(--border);color:{{ $pc[$payment->status->value] ?? 'var(--text-muted)' }};padding:3px 10px;border-radius:6px;font-weight:600">{{ $payment->status->label() }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-cash-coin"></i></div>
                            <h4>{{ __('messages.no_results') }}</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('super.admin.subscriptions.index') }}" class="btn" style="padding:8px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="bi bi-arrow-left"></i>{{ __('general.back') }}
        </a>
    </div>

    @push('scripts')
    <script>
    function confirmCancelSubscription(id) {
        const form = document.getElementById('cancel-subscription-' + id);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('settings.cancel_confirm') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('settings.cancel_subscription') }}',
            'btn-danger'
        );
    }
    </script>
    @endpush
</x-super-admin-layout>
