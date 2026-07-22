{{-- resources\views\super-admin\payments.blade.php --}}
<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.payments') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.payments') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.payments_desc') }}</x-slot>

    @php
        $canVerify = auth()->user()->hasPermission('payment.verify');
        $canRefund = auth()->user()->hasPermission('payment.refund');
        $canViewRaw = auth()->user()->hasPermission('payment.view_raw');
    @endphp

    <x-filter-tabs :tabs="[
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-cash-coin'],
        'checkout_pending' => ['label' => __('general.pending'), 'count' => $countPending, 'icon' => 'bi-clock'],
        'checkout_paid' => ['label' => __('general.paid'), 'count' => $countPaid, 'icon' => 'bi-check-circle'],
        'checkout_failed' => ['label' => __('general.failed'), 'count' => $countFailed, 'icon' => 'bi-x-circle'],
        'checkout_canceled' => ['label' => __('general.canceled'), 'count' => $countCanceled, 'icon' => 'bi-slash-circle'],
        'checkout_expired' => ['label' => __('super-admin.expired'), 'count' => $countExpired, 'icon' => 'bi-hourglass-split'],
    ]" current="{{ request('status', 'all') }}" keyParam="status" defaultKey="all"
        :preserve="['search', 'per_page', 'refunded', 'date_from', 'date_to']"
        subParam="method"
        subCurrent="{{ request('method', '') }}"
        :subTabs="$methodSubTabs" />

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.payments.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('general.search') }}..." value="{{ request('search') }}" />
                    <x-select-filter name="refunded" :options="['yes' => __('super-admin.refunded'), 'no' => __('general.not_refunded')]" placeholder="{{ __('general.all_refunded') }}" min-width="120px" />
                    <input type="date" name="date_from" class="form-control grid-filter-sm"
                        style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)"
                        value="{{ request('date_from') }}">
                    <input type="date" name="date_to" class="form-control grid-filter-sm"
                        style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)"
                        value="{{ request('date_to') }}">
                    @if (request('status') && request('status') !== 'all')
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if (request('method'))
                        <input type="hidden" name="method" value="{{ request('method') }}">
                    @endif
                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
                    <x-clear-filters :filters="['search','status','refunded','method','date_from','date_to']" :route="route('super.admin.payments.index')" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <x-per-page :current="(int) request('per_page', 15)" :route="route('super.admin.payments.index')" :preserve="['search','status','refunded','method','date_from','date_to']" :options="[10, 15, 25, 50]" />
            </div>
        </div>

        <div class="data-grid-body">
            @if ($payments->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('super-admin.workspace') }}</th>
                            <th>{{ __('super-admin.payer') }}</th>
                            <th>{{ __('settings.plan') }}</th>
                            <th>{{ __('general.amount') }}</th>
                            <th>{{ __('super-admin.method') }}</th>
                            <th>{{ __('super-admin.reference') }}</th>
                            <th>{{ __('super-admin.payment_id') }}</th>
                            <th>{{ __('super-admin.verification') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th>{{ __('general.date') }}</th>
                            @if ($canVerify || $canRefund || $canViewRaw)
                                <th class="col-actions"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            @php
                                $isWebhook = in_array($payment->paymentMethod?->key, $webhookMethods);
                                $hasFeeOrTax = ($payment->gateway_fee ?? 0) > 0 || ($payment->tax_added ?? 0) > 0 || ($payment->discount_amount ?? 0) > 0;
                            @endphp
                            <tr>
                                <td>{{ $payment->workspace?->name ?? '—' }}</td>
                                <td>
                                    @if ($payment->user)
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:26px;height:26px;border-radius:50%;background:var(--accent);color:#0F172A;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0">
                                                {{ strtoupper(substr($payment->user->name, 0, 1)) }}
                                            </div>
                                            <span style="font-size:13px">{{ $payment->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="cell-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($payment->subscription?->plan)
                                        <span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:2px 10px;border-radius:6px">{{ $payment->subscription->plan->name }}</span>
                                    @else
                                        <span class="cell-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format($payment->amount, 2) }}</strong>
                                    <span style="font-size:12px;color:var(--text-muted)">{{ $payment->currency }}</span>
                                    @if ($hasFeeOrTax)
                                        <i class="bi bi-info-circle" style="font-size:12px;color:var(--text-muted);cursor:help"
                                            title="{{ __('super-admin.original') }}: {{ number_format($payment->original_amount ?? $payment->amount, 2) }} {{ $payment->currency }}
                                            {{ __('super-admin.discount_amount') }}: -{{ number_format($payment->discount_amount ?? 0, 2) }}
                                            {{ __('super-admin.gateway_fee') }}: {{ number_format($payment->gateway_fee ?? 0, 2) }}
                                            {{ __('super-admin.tax_added') }}: {{ number_format($payment->tax_added ?? 0, 2) }} + {{ __('super-admin.tax_disclosed') }}: {{ number_format($payment->tax_disclosed ?? 0, 2) }}"></i>
                                    @endif
                                </td>
                                <td>
                                     @php
                                        $paymentMethodGetway = $payment->paymentMethod?->key;
                                    @endphp
                                    <div style="font-size:12px;color:var(--text-secondary);text-transform:capitalize">
                                        {{ __("super-admin.{$paymentMethodGetway}") }}
                                    </div>
                                    @if ($payment->payment_method_type)
                                        <div style="font-size:11px;color:var(--text-muted)">{{ $payment->payment_method_type }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($payment->reference)
                                        <span style="font-size:12px;direction:ltr;display:inline-block;font-family:monospace">{{ $payment->reference }}</span>
                                    @else
                                        <span class="cell-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($payment->uuid)
                                        <code style="font-size:11px;direction:ltr;display:inline-block;font-family:monospace">{{ $payment->uuid }}</code>
                                    @else
                                        <span class="cell-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php $v = $payment->verification; @endphp
                                    @if ($v)
                                        <div class="d-flex align-items-center gap-1">
                                            @if ($v->receipt_path)
                                                <a href="#"
                                                    @click="event.preventDefault();openReceipt('{{ route('receipts.show', $v) }}')"
                                                    style="display:inline-flex;align-items:center;gap:4px;font-size:11px;color:var(--info);text-decoration:none;cursor:pointer">
                                                    <img src="{{ route('receipts.show', $v) }}" alt="{{ __('super-admin.receipt_preview_alt') }}"
                                                        style="width:28px;height:28px;object-fit:cover;border-radius:4px;border:1px solid var(--border)">
                                                </a>
                                            @endif
                                            <x-status-badge domain="general" :status="$v->status->value" set="bi" />
                                        </div>
                                    @else
                                        <span class="cell-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">

                                        <x-status-badge domain="payment" :status="$payment->status->value" set="bi" />
                                        @if ($payment->isRefunded())
                                            <span class="badge" style="font-size:9px;background:var(--info-light);color:var(--info);padding:2px 8px;border-radius:6px;font-weight:600">{{ __('super-admin.refunded') }}</span>
                                        @endif
                                        @if ($payment->webhook_processed_at)
                                            <i class="bi bi-cloud-check" style="font-size:10px;opacity:0.5" title="{{ __('super-admin.webhook_processed') }}"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="cell-muted">{{ $payment->paid_at?->format('Y/m/d') ?? $payment->created_at->format('Y/m/d') }}</td>
                                @if ($canVerify || $canRefund || $canViewRaw)
                                    <td class="col-actions">
                                        <div class="d-flex gap-1">
                                            @if ($canVerify && $payment->isPending() && !$isWebhook)
                                                @if ($payment->verification && $payment->verification->receipt_path)
                                                    <a href="{{ route('receipts.show', $payment->verification) }}" target="_blank" class="btn"
                                                        style="padding:5px 10px;font-size:11px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--info);text-decoration:none" title="{{ __('super-admin.view_receipt') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endif
                                                <button type="button" class="btn" style="padding:5px 10px;font-size:11px;border-radius:var(--radius-xs);border:none;background:var(--success-light);color:var(--success);font-weight:600;cursor:pointer" @click="approvePayment({{ $payment->id }})">
                                                    <x-status-icon domain="general" status="success" set="bi" /> {{ __('super-admin.approve') }}
                                                </button>
                                                <button type="button" class="btn" style="padding:5px 10px;font-size:11px;border-radius:var(--radius-xs);border:none;background:var(--danger-light);color:var(--danger);font-weight:600;cursor:pointer" @click="rejectPayment({{ $payment->id }})">
                                                    <x-status-icon domain="general" status="failed" set="bi" /> {{ __('super-admin.reject') }}
                                                </button>
                                            @endif
                                            @if ($canRefund && $payment->isRefundable())
                                                <button type="button" class="btn" style="padding:5px 10px;font-size:11px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--info);cursor:pointer" @click="refundPayment({{ $payment->id }}, {{ $payment->amount }}, '{{ $payment->currency }}')" title="{{ __('super-admin.refund') }}">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            @endif
                                            @if ($canViewRaw)
                                                <button type="button" class="btn" style="padding:5px 8px;font-size:11px;border-radius:var(--radius-xs);border:none;background:transparent;color:var(--text-muted);cursor:pointer" @click="showDetails({{ $payment->id }})" title="{{ __('super-admin.view_details') }}">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <x-empty-state icon="bi bi-cash-coin" :title="__('general.no_data')" :description="__('messages.no_results')" />
            @endif
        </div>

        @if ($payments->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$payments" />
                <div>{{ $payments->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>

    {{-- Receipt Modal --}}
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:14px 20px">
                    <h5 class="modal-title" style="font-size:15px;font-weight:600"><i class="bi bi-receipt ms-2"></i>{{ __('super-admin.payment_details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <img id="receiptFullImage" src="" alt="{{ __('super-admin.receipt_preview_alt') }}" style="max-width:100%;max-height:70vh;border-radius:8px;border:1px solid var(--border);box-shadow:0 4px 12px rgba(0,0,0,0.08)">
                </div>
            </div>
        </div>
    </div>

    {{-- Approve Modal --}}
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><x-status-icon domain="general" status="success" set="bi" /> {{ __('super-admin.approve_payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="modal-body" style="padding:20px">
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500;margin-bottom:6px">{{ __('super-admin.transaction_reference') }}</label>
                            <input type="text" name="transaction_reference" class="form-control"
                                style="font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;width:100%"
                                placeholder="{{ __('super-admin.transaction_ref_placeholder') }}">
                        </div>
                        <input type="hidden" name="notes" value="Approved by admin">
                    </div>
                    <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px">
                        <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal">{{ __('general.cancel') }}</button>
                        <button type="submit" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--success);color:white;font-weight:600;border:none">{{ __('super-admin.approve') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><x-status-icon domain="general" status="danger" set="bi" /> {{ __('super-admin.reject_payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body" style="padding:20px">
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500;margin-bottom:6px">{{ __('super-admin.reject_reason') }}</label>
                            <textarea name="notes" class="form-control" rows="3" required
                                style="font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;width:100%"
                                placeholder="{{ __('super-admin.reject_reason_placeholder') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px">
                        <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal">{{ __('general.cancel') }}</button>
                        <button type="submit" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none">{{ __('super-admin.reject') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Refund Modal --}}
    <div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-arrow-counterclockwise ms-2" style="color:var(--info)"></i>{{ __('super-admin.refund_payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="refundForm" method="POST">
                    @csrf
                    <div class="modal-body" style="padding:20px">
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500;margin-bottom:6px">{{ __('super-admin.refund_amount') }} (<span id="refundCurrency">DZD</span>)</label>
                            <input type="number" name="refund_amount" id="refundAmount" class="form-control" step="0.01" min="0.01"
                                style="font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;width:100%"
                                placeholder="0.00">
                            <small id="refundAmountHelp" style="font-size:11px;color:var(--text-muted)"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500;margin-bottom:6px">{{ __('super-admin.refund_reason') }}</label>
                            <textarea name="refund_reason" class="form-control" rows="3" required
                                style="font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;width:100%"
                                placeholder="{{ __('super-admin.refund_reason_placeholder') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px">
                        <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal">{{ __('general.cancel') }}</button>
                        <button type="submit" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--info);color:white;font-weight:600;border:none">{{ __('super-admin.refund') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Details Modal --}}
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:14px 20px">
                    <h5 class="modal-title" style="font-size:15px;font-weight:600"><i class="bi bi-info-circle ms-2"></i>{{ __('super-admin.payment_raw_details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px">
                    <div id="detailsContent" style="max-height:60vh;overflow-y:auto">
                        <pre><code id="detailsJson" style="font-size:12px;direction:ltr;text-align:left;white-space:pre-wrap;word-break:break-word"></code></pre>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal">{{ __('general.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.paymentsData = @json($paymentsData);

            function approvePayment(paymentId) {
                const form = document.getElementById('approveForm');
                form.action = '{{ route('super.admin.payments.approve', '__ID__') }}'.replace('__ID__', paymentId);
                const modal = new bootstrap.Modal(document.getElementById('approveModal'));
                modal.show();
            }
            function rejectPayment(paymentId) {
                const form = document.getElementById('rejectForm');
                form.action = '{{ route('super.admin.payments.reject', '__ID__') }}'.replace('__ID__', paymentId);
                const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                modal.show();
            }
            function refundPayment(paymentId, amount, currency) {
                const form = document.getElementById('refundForm');
                form.action = '{{ route('super.admin.payments.refund', '__ID__') }}'.replace('__ID__', paymentId);
                document.getElementById('refundAmount').max = amount;
                document.getElementById('refundAmount').value = amount;
                document.getElementById('refundCurrency').textContent = currency;
                document.getElementById('refundAmountHelp').textContent = '{{ __('super-admin.refund_max_amount') }}: ' + amount.toFixed(2) + ' ' + currency;
                const modal = new bootstrap.Modal(document.getElementById('refundModal'));
                modal.show();
            }
            function showDetails(paymentId) {
                const data = window.paymentsData[paymentId];
                if (!data) return;
                const json = JSON.stringify({
                    reference: data.reference,
                    transaction_id: data.transaction_id,
                    chargily_checkout_id: data.chargily_checkout_id,
                    gateway_reference: data.gateway_reference,
                    metadata: data.metadata,
                    gateway_payload: data.gateway_payload,
                    webhook_payload: data.webhook_payload,
                }, null, 2);
                document.getElementById('detailsJson').textContent = json;
                const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
                modal.show();
            }
            function openReceipt(url) {
                document.getElementById('receiptFullImage').src = url;
                const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
                modal.show();
            }
        </script>
    @endpush
</x-super-admin-layout>
