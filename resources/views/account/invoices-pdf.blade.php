<!DOCTYPE html>
<html dir="auto">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; padding: 20px; }
        h2 { margin-bottom: 4px; }
        .text-muted { color: #888; font-size: 11px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; }
        .bg-success { background: #d4edda; color: #155724; }
        .bg-warning { background: #fff3cd; color: #856404; }
        .bg-danger { background: #f8d7da; color: #721c24; }
        .bg-secondary { background: #e2e3e5; color: #383d41; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; font-weight: 600; font-size: 11px; }
        .text-end { text-align: right; }
        .fw-700 { font-weight: 700; }
        .bg-subtle { background: #f9f9f9; }
        .text-danger { color: #dc3545; }
        .text-info { color: #17a2b8; }
        .border-dashed { border-top: 1px dashed #ccc; }
        .info-card { display: inline-block; width: 30%; vertical-align: top; margin-bottom: 12px; }
        .info-label { font-size: 10px; color: #888; }
        .info-value { font-weight: 600; font-size: 13px; }
    </style>
</head>
<body>
    <div style="text-align:center;margin-bottom:20px">
        <h2>{{ config('app.name') }}</h2>
        <p class="text-muted">{{ $invoice->number }}</p>
        <p class="text-muted">{{ $invoice->created_at->format('F d, Y') }}</p>
        <span class="badge bg-{{ $invoice->status->value === 'paid' ? 'success' : ($invoice->status->value === 'draft' ? 'warning' : ($invoice->status->value === 'overdue' ? 'danger' : 'secondary')) }}">
            {{ $invoice->status->label() }}
        </span>
    </div>

    <div style="margin-bottom:16px">
        <div class="info-card">
            <div class="info-label">{{ __('settings.invoice_plan') }}</div>
            <div class="info-value">{{ $invoice->subscription?->plan?->name ?? '—' }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">{{ __('settings.invoice_period') }}</div>
            <div class="info-value">
                @if($invoice->period_start && $invoice->period_end)
                    {{ $invoice->period_start->format('M Y') }} — {{ $invoice->period_end->format('M Y') }}
                @else
                    —
                @endif
            </div>
        </div>
        <div class="info-card">
            <div class="info-label">{{ __('settings.payment_method') }}</div>
            <div class="info-value">{{ $invoice->subscription?->payment_method ?? '—' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('settings.description') }}</th>
                <th class="text-end">{{ __('settings.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ __('settings.subtotal') }} ({{ $invoice->subscription?->plan?->name ?? '' }})</td>
                <td class="text-end fw-500">{{ $displayPrice($invoice->subtotal, $invoice->currency) }}</td>
            </tr>
            @if((float) $invoice->discount > 0)
            <tr>
                <td style="color:#dc3545">
                    {{ __('settings.discount') }}
                    @if($invoice->coupon)
                        <span style="font-size:10px;color:#888">({{ $invoice->coupon->code }})</span>
                    @endif
                </td>
                <td class="text-end fw-500" style="color:#dc3545">-{{ $displayPrice($invoice->discount, $invoice->currency) }}</td>
            </tr>
            @endif
            @if((float) $invoice->proration_credit > 0)
            <tr>
                <td style="color:#17a2b8">{{ __('settings.proration_credit') ?? 'رصيد براتا' }}</td>
                <td class="text-end fw-500" style="color:#17a2b8">-{{ $displayPrice($invoice->proration_credit, $invoice->currency) }}</td>
            </tr>
            @endif
            @if((float) $invoice->gateway_fee > 0)
            <tr>
                <td>{{ __('settings.gateway_fee') ?? 'رسم بوابة دفع' }}</td>
                <td class="text-end fw-500">{{ $displayPrice($invoice->gateway_fee, $invoice->currency) }}</td>
            </tr>
            @endif
            @if((float) $invoice->tax_added > 0)
            <tr>
                <td>{{ __('settings.tax') }}</td>
                <td class="text-end fw-500">{{ $displayPrice($invoice->tax_added, $invoice->currency) }}</td>
            </tr>
            @endif
            <tr style="background:#f9f9f9">
                <td style="padding:10px 12px;border-top:2px solid #333;font-weight:700">{{ __('settings.total_due') }}</td>
                <td style="padding:10px 12px;border-top:2px solid #333;text-align:right;font-weight:700;font-size:14px">{{ $displayPrice($invoice->total, $invoice->currency) }}</td>
            </tr>
            @if((float) $invoice->tax_disclosed > 0)
            <tr>
                <td style="padding:6px 12px;border-top:1px dashed #ccc;font-size:10px;color:#888">
                    {{ __('settings.tax_disclosed_label') ?? 'ضريبة/زكاة إفصاح (غير مضافة للمبلغ)' }}
                </td>
                <td style="padding:6px 12px;border-top:1px dashed #ccc;text-align:right;font-size:10px;color:#888">{{ $displayPrice($invoice->tax_disclosed, $invoice->currency) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <p style="text-align:center;color:#888;font-size:10px;margin-top:24px">
        {{ config('app.name') }} — {{ $invoice->created_at->format('Y') }}
    </p>
</body>
</html>