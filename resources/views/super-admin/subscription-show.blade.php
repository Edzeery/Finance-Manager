<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.subscriptions') }} #{{ $subscription->id }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.subscriptions') }} #{{ $subscription->id }}</x-slot>
    <x-slot:page-description>{{ $subscription->workspace?->name ?? __('general.unknown') }} &mdash; {{ $subscription->plan?->name ?? '—' }}</x-slot>

    <div class="detail-grid"
         x-data="subscriptionActions({
             id: {{ $subscription->id }},
             origStatus: '{{ $subscription->status->value }}',
             origAutoRenew: {{ $subscription->auto_renew ? 'true' : 'false' }},
             origPlanId: {{ $subscription->subscription_plan_id }},
             plans: @js($plans->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->values()->all()),
             urls: {
                 status: '{{ route('super.admin.subscriptions.update-status', $subscription) }}',
                 autoRenew: '{{ route('super.admin.subscriptions.toggle-renew-ajax', $subscription) }}',
                 plan: '{{ route('super.admin.subscriptions.update-plan', $subscription) }}',
             },
             csrf: '{{ csrf_token() }}',
         })">

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
                            <td class="info-value">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <span id="plan-display" class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:2px 10px;border-radius:6px">{{ $subscription->plan?->name ?? '—' }}</span>
                                    @if($plans->count() > 1)
                                    <x-button icon="bi bi-pencil" @click="openPlanModal()" title="{{ __('settings.change_plan') }}" style="padding:0;border:0;background:transparent;font-size:12px;color:var(--text-muted)" />
                                    @endif
                                    <span x-show="planFlash === 'ok'" x-cloak x-transition style="color:var(--success);font-size:12px">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </span>
                                    <span x-show="planFlash === 'error'" x-cloak x-transition style="color:var(--danger);font-size:12px">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('general.status') }}</td>
                            <td class="info-value">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <span id="status-badge">
                                        <x-status-badge domain="subscription" :status="$subscription->status->value" set="bi" />
                                    </span>
                                    <div style="min-width:180px">
                                        <x-status-select
                                            domain="subscription"
                                            :selected="$subscription->status->value"
                                            set="bi"
                                            size="sm"
                                        />
                                    </div>
                                    <span x-show="statusSaving" x-cloak>
                                        <i class="bi bi-arrow-clockwise inline-save-spin" style="font-size:12px;color:var(--text-muted)"></i>
                                    </span>
                                    <span x-show="statusFlash === 'ok'" x-cloak x-transition style="color:var(--success);font-size:12px">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </span>
                                    <span x-show="statusFlash === 'error'" x-cloak x-transition style="color:var(--danger);font-size:12px">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </span>
                                </div>
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
                                <div class="d-inline-flex align-items-center gap-2">
                                    <span id="auto-renew-badge">
                                        <x-status-badge domain="general" :status="$subscription->auto_renew ? 'yes' : 'no'" set="bi" />
                                    </span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               x-model="autoRenew"
                                               @change="toggleAutoRenew()"
                                               :disabled="autoRenewSaving"
                                               style="cursor:pointer">
                                    </div>
                                    <span x-show="autoRenewSaving" x-cloak>
                                        <i class="bi bi-arrow-clockwise inline-save-spin" style="font-size:12px;color:var(--text-muted)"></i>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('general.type') }}</td>
                            <td class="info-value">{{ ($subscription->paymentMethod?->key ?? $subscription->payment_method) ? __("super-admin." . ($subscription->paymentMethod?->key ?? $subscription->payment_method)) : '—' }}</td>
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
                                            <x-status-badge domain="invoice" :status="$inv->status->value" set="bi" />
                                        </td>
                                        <td class="cell-muted">{{ $inv->created_at->format('Y/m/d') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <x-empty-state icon="bi bi-receipt" :title="__('super-admin.no_invoices')" />
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
                    <x-button variant="danger" block icon="bi bi-x-circle" @click="confirmCancelSubscription({{ $subscription->id }})" :disabled="$subscription->status === \App\Enums\SubscriptionStatus::Canceled || $subscription->status === \App\Enums\SubscriptionStatus::Expired">{{ __('settings.cancel_subscription') }}</x-button>
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
                                    <div class="cell-muted" style="font-size:11px">{{ __("super-admin." . ($payment->paymentMethod?->key)) }} &middot; {{ $payment->paid_at?->format('Y/m/d') ?? $payment->created_at->format('Y/m/d') }}</div>
                                </div>
                                <x-status-badge domain="payment" :status="$payment->status->value" set="bi" />
                            </div>
                        @endforeach
                    @else
                        <x-empty-state icon="bi bi-cash-coin" :title="__('messages.no_results')" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="planModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:14px 20px">
                    <h5 class="modal-title" style="font-size:15px;font-weight:600"><i class="bi bi-arrow-repeat ms-2"></i>{{ __('settings.change_plan') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px">
                    <label class="form-label" style="font-size:13px;font-weight:500;margin-bottom:6px">{{ __('settings.plan') }}</label>
                    <select id="planSelect" class="form-select form-select-sm" style="font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:8px 12px;width:100%"></select>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal">{{ __('general.cancel') }}</button>
                    <button type="button" id="planSaveBtn" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:var(--primary);font-weight:600;border:none">
                        <i class="bi bi-check-lg"></i> {{ __('general.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <x-button href="{{ route('super.admin.subscriptions.index') }}" icon="bi bi-arrow-left" class="btn-back">{{ __('general.back') }}</x-button>
    </div>

    @push('scripts')
    <script>
    function subscriptionActions(config) {
        return {
            status: config.origStatus,
            autoRenew: config.origAutoRenew,
            planId: config.origPlanId,
            plans: config.plans,
            urls: config.urls,
            csrf: config.csrf,

            planFlash: '',
            statusSaving: false,
            statusFlash: '',
            autoRenewSaving: false,

            async request(url, body) {
                const opts = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                };
                if (body !== undefined) opts.body = JSON.stringify(body);
                const res = await fetch(url, opts);
                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    throw new Error(err.message || 'Request failed');
                }
                return res.json();
            },

            init() {
                this.$el.addEventListener('change', (e) => {
                    const statusContainer = e.target.closest('.status-select');
                    if (statusContainer && this.$el.contains(statusContainer)) {
                        this.saveStatus(e.target.value);
                    }
                }, true);
            },

            openPlanModal() {
                window._showPlanCtx = this;
                const select = document.getElementById('planSelect');
                select.innerHTML = this.plans.map(p =>
                    `<option value="${p.id}" ${p.id == this.planId ? 'selected' : ''}>${p.name}</option>`
                ).join('');
                const modal = new bootstrap.Modal(document.getElementById('planModal'));
                modal.show();
            },

            async savePlanFromModal() {
                const ctx = window._showPlanCtx;
                if (!ctx) return;
                const newPlanId = document.getElementById('planSelect').value;
                if (newPlanId == ctx.planId) {
                    bootstrap.Modal.getInstance(document.getElementById('planModal')).hide();
                    return;
                }
                const saveBtn = document.getElementById('planSaveBtn');
                saveBtn.classList.add('btn-submitting');
                ctx.planFlash = '';
                try {
                    const data = await ctx.request(ctx.urls.plan, { subscription_plan_id: newPlanId });
                    if (data.success) {
                        ctx.planId = newPlanId;
                        document.getElementById('plan-display').textContent = data.plan_name;
                        ctx.planFlash = 'ok';
                        setTimeout(() => { ctx.planFlash = ''; }, 3000);
                        var modalBody = document.querySelector('#planModal .modal-body');
                        var successHtml = '<div class="text-center py-2"><i class="bi bi-check-circle-fill" style="font-size:28px;color:var(--success)"></i>';
                        successHtml += '<div class="mt-2 fw-semibold" style="font-size:13px">{{ __("settings.plan_changed") }}</div>';
                        if (data.invoice_number) {
                            successHtml += '<div class="cell-muted mt-1" style="font-size:12px">#' + data.invoice_number + '</div>';
                        }
                        if (data.payment_amount !== null && data.payment_amount !== undefined) {
                            successHtml += '<div class="mt-1" style="font-size:12px;font-weight:600">' + parseFloat(data.payment_amount).toFixed(2) + ' {{ config("finance.currency_symbol") }}</div>';
                        }
                        successHtml += '</div>';
                        modalBody.innerHTML = successHtml;
                        saveBtn.style.display = 'none';
                        setTimeout(function() { bootstrap.Modal.getInstance(document.getElementById('planModal')).hide(); }, 2000);
                    } else {
                        ctx.planFlash = 'error';
                        setTimeout(() => { ctx.planFlash = ''; }, 3000);
                    }
                } catch (e) {
                    ctx.planFlash = 'error';
                    setTimeout(() => { ctx.planFlash = ''; }, 3000);
                }
                saveBtn.classList.remove('btn-submitting');
                saveBtn.style.display = '';
            },

            async saveStatus(newStatus) {
                if (newStatus === this.status || this.statusSaving) return;
                const oldStatus = this.status;
                this.status = newStatus;
                this.statusSaving = true;
                this.statusFlash = '';
                try {
                    const data = await this.request(this.urls.status, { status: newStatus });
                    if (data.success) {
                        document.getElementById('status-badge').innerHTML = data.badge;
                        this.statusFlash = 'ok';
                        setTimeout(() => { this.statusFlash = ''; }, 3000);
                    } else {
                        this.status = oldStatus;
                        this.statusFlash = 'error';
                        setTimeout(() => { this.statusFlash = ''; }, 3000);
                    }
                } catch (e) {
                    this.status = oldStatus;
                    this.statusFlash = 'error';
                    setTimeout(() => { this.statusFlash = ''; }, 3000);
                }
                this.statusSaving = false;
            },

            async toggleAutoRenew() {
                const oldVal = this.autoRenew;
                this.autoRenewSaving = true;
                try {
                    const data = await this.request(this.urls.autoRenew);
                    if (data.success) {
                        this.autoRenew = data.auto_renew;
                        document.getElementById('auto-renew-badge').innerHTML = data.badge;
                    } else {
                        this.autoRenew = oldVal;
                    }
                } catch (e) {
                    this.autoRenew = oldVal;
                }
                this.autoRenewSaving = false;
            },
        };
    }

    document.getElementById('planSaveBtn').addEventListener('click', function() {
        if (window._showPlanCtx) {
            window._showPlanCtx.savePlanFromModal();
        }
    });

    document.getElementById('planModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('planSaveBtn').classList.remove('btn-submitting');
        document.getElementById('planSaveBtn').style.display = '';
        window._showPlanCtx = null;
        var modalBody = document.querySelector('#planModal .modal-body');
        modalBody.innerHTML = '<label class="form-label" style="font-size:13px;font-weight:500;margin-bottom:6px">{{ __("settings.plan") }}</label><select id="planSelect" class="form-select form-select-sm" style="font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:8px 12px;width:100%"></select>';
    });

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
