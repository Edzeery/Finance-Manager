<div class="info-section mb-3">
    <div class="info-section-header">
        <i class="bi bi-credit-card-2-front me-1"></i>
        {{ __('onboarding.payment_info') }}
    </div>
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.payment_status') }}</span>
            <span class="info-value">
                <span class="badge rounded-pill {{ $this->statusBadgeClass($payment->status->value) }}">{{ $payment->status->label() }}</span>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.payment_method_label') }}</span>
            <span class="info-value">{{ $this->methodLabel($payment->method) }}</span>
        </div>
        @if ($payment->payment_method_type)
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.payment_method_label') }} Type</span>
            <span class="info-value text-uppercase">{{ $payment->payment_method_type }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.payment_amount') }}</span>
            <span class="info-value fw-bold">{{ $this->formatAmount((float) $payment->amount) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.payment_date') }}</span>
            <span class="info-value">{{ $payment->created_at->format('d M Y, H:i') }}</span>
        </div>
        @if ($payment->transaction_id)
        <div class="info-row">
            <span class="info-label">Transaction ID</span>
            <span class="info-value text-muted small text-truncate" style="max-width:200px;direction:ltr;">{{ $payment->transaction_id }}</span>
        </div>
        @endif
    </div>
</div>

@isset($plan)
<div class="info-section mb-3">
    <div class="info-section-header">
        <i class="bi bi-box-seam me-1"></i>
        {{ __('onboarding.subscription_info') }}
    </div>
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.plan_name') }}</span>
            <span class="info-value">{{ $plan['name'] ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.price') }}</span>
            <span class="info-value">
                @php $price = $payment->original_amount ?? $payment->amount; @endphp
                {{ $this->formatAmount((float) $price) }}
            </span>
        </div>
        @if ($payment->discount_amount > 0)
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.coupon_discount') }}</span>
            <span class="info-value text-success">-{{ $this->formatAmount((float) $payment->discount_amount) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label fw-bold">{{ __('onboarding.total') }}</span>
            <span class="info-value fw-bold">{{ $this->formatAmount((float) $payment->amount) }}</span>
        </div>
        @endif
    </div>
</div>
@endisset

@php $_hasFees = $payment && ($payment->gateway_fee > 0 || $payment->tax_added > 0 || $payment->tax_disclosed > 0); @endphp
@if ($_hasFees)
<div class="info-section mb-3">
    <div class="info-section-header">
        <i class="bi bi-receipt me-1"></i>
        {{ __('onboarding.fee_breakdown') }}
    </div>
    <div class="info-grid">
        @if ($payment->gateway_fee > 0)
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.gateway_fee') }}</span>
            <span class="info-value">+{{ $this->formatAmount((float) $payment->gateway_fee) }}</span>
        </div>
        @endif
        @if ($payment->tax_added > 0)
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.tax_added') }}</span>
            <span class="info-value">+{{ $this->formatAmount((float) $payment->tax_added) }}</span>
        </div>
        @endif
        @if ($payment->tax_disclosed > 0)
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.tax_disclosed') }}</span>
            <span class="info-value">{{ $this->formatAmount((float) $payment->tax_disclosed) }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label fw-bold">{{ __('onboarding.total') }}</span>
            <span class="info-value fw-bold">{{ $this->formatAmount((float) $payment->amount) }}</span>
        </div>
    </div>
</div>
@endif

@isset($invoice)
<div class="info-section mb-3">
    <div class="info-section-header">
        <i class="bi bi-receipt me-1"></i>
        {{ __('onboarding.invoice_info') }}
    </div>
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.invoice_number') }}</span>
            <span class="info-value">{{ $invoice['number'] ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.invoice_status') }}</span>
            <span class="info-value"><span class="badge rounded-pill {{ $this->statusBadgeClass($invoice['status'] ?? 'draft') }}">{{ __('general.' . ($invoice['status'] ?? 'draft')) }}</span></span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.invoice_subtotal') }}</span>
            <span class="info-value">{{ $this->formatAmount((float) ($invoice['subtotal'] ?? 0)) }}</span>
        </div>
        @if (!empty($invoice['tax']) && $invoice['tax'] > 0)
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.invoice_tax') }}</span>
            <span class="info-value">{{ $this->formatAmount((float) $invoice['tax']) }}</span>
        </div>
        @endif
        @if (!empty($invoice['discount']) && $invoice['discount'] > 0)
        <div class="info-row">
            <span class="info-label">{{ __('onboarding.invoice_discount') }}</span>
            <span class="info-value text-success">-{{ $this->formatAmount((float) $invoice['discount']) }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label fw-bold">{{ __('onboarding.invoice_total') }}</span>
            <span class="info-value fw-bold">{{ $this->formatAmount((float) ($invoice['total'] ?? 0)) }}</span>
        </div>
    </div>
</div>
@endisset
