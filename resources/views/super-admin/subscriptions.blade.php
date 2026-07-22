<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.subscriptions') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.subscriptions') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.subscriptions_desc') }}</x-slot>

    <x-filter-tabs :tabs="[
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-credit-card'],
        'active' => ['label' => __('general.active'), 'count' => $countActive, 'icon' => 'bi-check-circle'],
        'trialing' => ['label' => __('super-admin.trialing'), 'count' => $countTrialing, 'icon' => 'bi-star'],
        'past_due' => [
            'label' => __('super-admin.past_due'),
            'count' => $countPastDue,
            'icon' => 'bi-exclamation-triangle',
        ],
        'canceled' => ['label' => __('super-admin.canceled'), 'count' => $countCanceled, 'icon' => 'bi-slash-circle'],
        'expired' => ['label' => __('super-admin.expired'), 'count' => $countExpired, 'icon' => 'bi-hourglass-split'],
    ]" current="{{ request('status', 'all') }}" keyParam="status" defaultKey="all"
        :preserve="['search', 'per_page']" subParam="plan_id" subCurrent="{{ request('plan_id', '') }}" :subTabs="$planSubTabs" />

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.subscriptions.index') }}"
                    class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('super-admin.search_invoice') }}..."
                        value="{{ request('search') }}" min-width="200px" />
                    @if (request('status') && request('status') !== 'all')
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if (request('plan_id'))
                        <input type="hidden" name="plan_id" value="{{ request('plan_id') }}">
                    @endif
                    <button type="submit" class="btn"
                        style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
                    <x-clear-filters :filters="['search', 'status', 'plan_id']" :route="route('super.admin.subscriptions.index')" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <x-per-page :current="(int) request('per_page', 15)" :route="route('super.admin.subscriptions.index')" :preserve="['search', 'status', 'plan_id']" :options="[10, 15, 25, 50]" />
            </div>
        </div>

        <div class="data-grid-body">
            @if ($subscriptions->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('super-admin.workspace') }}</th>
                            <th>{{ __('settings.plan') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th>{{ __('super-admin.started') }}</th>
                            <th>{{ __('super-admin.ends_at') }}</th>
                            <th>{{ __('super-admin.auto_renew') }}</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscriptions as $sub)
                            <tr x-data="subRow({
                                id: {{ $sub->id }},
                                origStatus: '{{ $sub->status->value }}',
                                origAutoRenew: {{ $sub->auto_renew ? 'true' : 'false' }},
                                origPlanId: {{ $sub->subscription_plan_id }},
                                plans: @js($plans->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->values()->all()),
                                csrf: '{{ csrf_token() }}',
                                urls: {
                                    status: '{{ route('super.admin.subscriptions.update-status', $sub) }}',
                                    autoRenew: '{{ route('super.admin.subscriptions.toggle-renew-ajax', $sub) }}',
                                    plan: '{{ route('super.admin.subscriptions.update-plan', $sub) }}',
                                },
                            })">
                                <td>
                                    <a href="{{ route('super.admin.subscriptions.show', $sub) }}"
                                        style="font-weight:500;color:var(--text);text-decoration:none">{{ $sub->workspace?->name ?? __('general.unknown') }}</a>
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <span class="badge sub-plan-name"
                                            style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:2px 8px;border-radius:6px">{{ $sub->plan?->name ?? '—' }}</span>
                                        <button type="button" class="btn btn-sm p-0 border-0 bg-transparent"
                                            @click="openPlanModal()" title="{{ __('settings.change_plan') }}">
                                            <i class="bi bi-pencil"
                                                style="font-size:11px;color:var(--text-muted)"></i>
                                        </button>
                                        <span x-show="planFlash === 'ok'" x-cloak x-transition
                                            style="color:var(--success);font-size:11px"><i
                                                class="bi bi-check-circle-fill"></i></span>
                                        <span x-show="planFlash === 'error'" x-cloak x-transition
                                            style="color:var(--danger);font-size:11px"><i
                                                class="bi bi-x-circle-fill"></i></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <span class="sub-status-badge">
                                            <x-status-badge domain="subscription" :status="$sub->status->value" set="bi" />
                                        </span>
                                        <x-status-select domain="subscription" :selected="$sub->status->value" set="bi"
                                            size="sm" class="sub-status-select" />
                                        <span x-show="statusSaving" x-cloak><i
                                                class="bi bi-arrow-clockwise inline-save-spin"
                                                style="font-size:11px;color:var(--text-muted)"></i></span>
                                        <span x-show="statusFlash === 'ok'" x-cloak x-transition
                                            style="color:var(--success);font-size:11px"><i
                                                class="bi bi-check-circle-fill"></i></span>
                                        <span x-show="statusFlash === 'error'" x-cloak x-transition
                                            style="color:var(--danger);font-size:11px"><i
                                                class="bi bi-x-circle-fill"></i></span>
                                    </div>
                                </td>
                                <td class="cell-muted" style="font-size:12px">
                                    {{ $sub->starts_at?->format('Y/m/d') ?? '—' }}</td>
                                <td class="cell-muted" style="font-size:12px">
                                    {{ $sub->ends_at?->format('Y/m/d') ?? '—' }}</td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <span class="sub-renew-badge">
                                            <x-status-badge domain="general" :status="$sub->auto_renew ? 'yes' : 'no'" set="bi" />
                                        </span>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                x-model="autoRenew" @change="toggleAutoRenew()"
                                                :disabled="autoRenewSaving"
                                                style="cursor:pointer;width:2em;height:1.1em">
                                        </div>
                                        <span x-show="autoRenewSaving" x-cloak><i
                                                class="bi bi-arrow-clockwise inline-save-spin"
                                                style="font-size:11px;color:var(--text-muted)"></i></span>
                                        <span x-show="renewFlash === 'ok'" x-cloak x-transition
                                            style="color:var(--success);font-size:11px"><i
                                                class="bi bi-check-circle-fill"></i></span>
                                        <span x-show="renewFlash === 'error'" x-cloak x-transition
                                            style="color:var(--danger);font-size:11px"><i
                                                class="bi bi-x-circle-fill"></i></span>
                                    </div>
                                </td>
                                <td class="col-actions">
                                    <a href="{{ route('super.admin.subscriptions.show', $sub) }}"
                                        class="btn btn-icon" title="{{ __('general.view') }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <x-empty-state icon="bi bi-credit-card" :title="__('general.no_data')" :description="__('messages.no_results')" />
            @endif
        </div>

        @if ($subscriptions->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$subscriptions" />
                <div>{{ $subscriptions->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
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
                    <button type="button" id="planSaveBtn" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none">
                        <i class="bi bi-check-lg"></i> {{ __('general.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window._currentPlanRow = null;

            function subRow(config) {
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
                    renewFlash: '',

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
                        const row = this.$el;
                        this.$el.addEventListener('change', (e) => {
                            const statusContainer = e.target.closest('.status-select');
                            if (statusContainer && row.contains(statusContainer)) {
                                this.saveStatus(e.target.value);
                            }
                        });
                    },

                    openPlanModal() {
                        window._currentPlanRow = this;
                        const select = document.getElementById('planSelect');
                        select.innerHTML = this.plans.map(p =>
                            `<option value="${p.id}" ${p.id == this.planId ? 'selected' : ''}>${p.name}</option>`
                        ).join('');
                        const modal = new bootstrap.Modal(document.getElementById('planModal'));
                        modal.show();
                    },

                    async savePlanFromModal() {
                        const row = window._currentPlanRow;
                        if (!row) return;
                        const newPlanId = document.getElementById('planSelect').value;
                        if (newPlanId == row.planId) {
                            bootstrap.Modal.getInstance(document.getElementById('planModal')).hide();
                            return;
                        }
                        const saveBtn = document.getElementById('planSaveBtn');
                        saveBtn.classList.add('btn-submitting');
                        row.planFlash = '';
                        try {
                            const data = await row.request(row.urls.plan, { subscription_plan_id: newPlanId });
                            if (data.success) {
                                row.planId = newPlanId;
                                const nameEl = row.$el.querySelector('.sub-plan-name');
                                if (nameEl) nameEl.textContent = data.plan_name;
                                row.planFlash = 'ok';
                                setTimeout(() => { row.planFlash = ''; }, 3000);
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
                                row.planFlash = 'error';
                                setTimeout(() => { row.planFlash = ''; }, 3000);
                            }
                        } catch (e) {
                            row.planFlash = 'error';
                            setTimeout(() => { row.planFlash = ''; }, 3000);
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
                            const data = await this.request(this.urls.status, {
                                status: newStatus
                            });
                            if (data.success) {
                                const badgeEl = this.$el.querySelector('.sub-status-badge');
                                if (badgeEl) badgeEl.innerHTML = data.badge;
                                this.statusFlash = 'ok';
                                setTimeout(() => {
                                    this.statusFlash = '';
                                }, 3000);
                            } else {
                                this.status = oldStatus;
                                this.statusFlash = 'error';
                                setTimeout(() => {
                                    this.statusFlash = '';
                                }, 3000);
                            }
                        } catch (e) {
                            this.status = oldStatus;
                            this.statusFlash = 'error';
                            setTimeout(() => {
                                this.statusFlash = '';
                            }, 3000);
                        }
                        this.statusSaving = false;
                    },

                    async toggleAutoRenew() {
                        const oldVal = this.autoRenew;
                        this.autoRenewSaving = true;
                        this.renewFlash = '';
                        try {
                            const data = await this.request(this.urls.autoRenew);
                            if (data.success) {
                                this.autoRenew = data.auto_renew;
                                const badgeEl = this.$el.querySelector('.sub-renew-badge');
                                if (badgeEl) badgeEl.innerHTML = data.badge;
                                this.renewFlash = 'ok';
                                setTimeout(() => { this.renewFlash = ''; }, 3000);
                            } else {
                                this.autoRenew = oldVal;
                                this.renewFlash = 'error';
                                setTimeout(() => { this.renewFlash = ''; }, 3000);
                            }
                        } catch (e) {
                            this.autoRenew = oldVal;
                            this.renewFlash = 'error';
                            setTimeout(() => { this.renewFlash = ''; }, 3000);
                        }
                        this.autoRenewSaving = false;
                    },
                };
            }

            document.getElementById('planSaveBtn').addEventListener('click', function() {
                if (window._currentPlanRow) {
                    window._currentPlanRow.savePlanFromModal();
                }
            });

            document.getElementById('planModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('planSaveBtn').classList.remove('btn-submitting');
                document.getElementById('planSaveBtn').style.display = '';
                window._currentPlanRow = null;
                var modalBody = document.querySelector('#planModal .modal-body');
                modalBody.innerHTML = '<label class="form-label" style="font-size:13px;font-weight:500;margin-bottom:6px">{{ __("settings.plan") }}</label><select id="planSelect" class="form-select form-select-sm" style="font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:8px 12px;width:100%"></select>';
            });
        </script>
    @endpush
</x-super-admin-layout>
