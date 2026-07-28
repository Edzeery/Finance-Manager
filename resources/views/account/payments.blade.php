<x-app-layout>
    @php
        $formatAmount = function (float $amount, string $currency) {
            return number_format($amount, 2) . ' ' . \App\Services\CurrencyHelper::symbol($currency);
        };
        $getMethodLabel = function (?string $method) use ($gateways) {
            if (!$method) {
                return '—';
            }
            if (isset($gateways[$method])) {
                return $gateways[$method]->name;
            }
            return ucfirst($method);
        };
    @endphp
    <x-slot:title>{{ __('settings.payment_history') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('settings.payment_history') }}</x-slot>
    <x-slot:page-description>{{ __('settings.payment_history_desc') }}</x-slot>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div></div>
        <div class="d-flex gap-2 align-items-center">
            <x-per-page :current="(int) request('per_page', 15)" />
        </div>
    </div>

    <div class="settings-card">
        @if ($payments && $payments->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('settings.invoice_date') }}</th>
                            <th>{{ __('settings.invoice_amount') }}</th>
                            <th>{{ __('settings.payment_method') }}</th>
                            <th>{{ __('super-admin.payment_id') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            @php $continueUrl = $payment->isPending() ? $payment->getContinueUrl() : null; @endphp
                            <tr>
                                <td style="font-size:13px">{{ $payment->created_at->format('Y/m/d H:i') }}</td>
                                <td>
                                    <span style="font-weight:600">
                                        {{ $formatAmount($payment->amount, $payment->currency ?? 'USD') }}
                                        @if ($payment->original_amount > $payment->amount)
                                            <span
                                                style="text-decoration:line-through;color:var(--text-muted);font-weight:400;font-size:12px">
                                                {{ $formatAmount($payment->original_amount, $payment->currency ?? 'USD') }}
                                            </span>
                                        @endif
                                    </span>
                                </td>
                                <td style="font-size:13px">
                                    @php
                                        $paymentMethodGetway = $payment->paymentMethod?->key;
                                    @endphp
                                    <div class=""
                                        style="font-size:12px;color:var(--text-secondary);text-transform:capitalize">
                                        {{ __("super-admin.{$paymentMethodGetway}") }}
                                    </div>
                                    @if ($payment->payment_method_type)
                                        <div style="font-size:11px;color:var(--text-muted)">
                                            {{ $payment->payment_method_type }}</div>
                                    @endif
                                </td>
                                <td><code
                                        style="font-size:11px;font-family:monospace">{{ $payment->uuid ?? '—' }}</code>
                                </td>
                                <td>
                                    <x-status-badge domain="payment" :status="$payment->status->value" set="bi" />
                                    @if($payment->isRefunded())
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px">
                                            <i class="bi bi-arrow-counterclockwise" style="margin-inline-end:2px"></i>{{ __('general.refunded') }}
                                            @if($payment->refund_amount > 0)
                                                — {{ $formatAmount($payment->refund_amount, $payment->currency ?? 'USD') }}
                                            @endif
                                            @if($payment->refunded_at)
                                                <span style="display:block;font-size:10px">{{ $payment->refunded_at->format('Y/m/d H:i') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($continueUrl)
                                        <x-button href="{{ $continueUrl }}" target="_blank" size="sm" icon="bi bi-credit-card" class="btn-warning">{{ __('settings.complete_payment') }}</x-button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <x-pagination-info :items="$payments" />
                    <div>
                        {{ $payments->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-receipt" style="font-size:48px;color:var(--text-muted);opacity:0.4"></i>
                <p class="text-muted mt-3 mb-0">{{ __('settings.no_payments') }}</p>
            </div>
        @endif
    </div>

    <div class="mt-3">
        <x-button href="{{ route('billing.subscriptions') }}" variant="outline" icon="bi bi-arrow-left">{{ __('general.back') }}</x-button>
    </div>
</x-app-layout>
