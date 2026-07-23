<!DOCTYPE html>
<html dir="auto">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', 'Segoe UI', sans-serif; font-size: 12px; color: #1e1b4b; padding: 30px; line-height: 1.5; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; border-bottom: 3px solid #6366f1; padding-bottom: 20px; }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .logo-icon { width: 42px; height: 42px; background: linear-gradient(135deg, #6366f1, #4f46e5); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; font-weight: 700; }
        .app-name { font-size: 20px; font-weight: 700; color: #1e1b4b; }
        .header-right { text-align: right; }
        .invoice-number { font-size: 22px; font-weight: 700; color: #6366f1; margin-bottom: 4px; }
        .invoice-date { color: #6b7280; font-size: 12px; }
        .badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-draft { background: #e5e7eb; color: #374151; }
        .badge-overdue { background: #fee2e2; color: #991b1b; }
        .section-title { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; font-weight: 600; margin-bottom: 8px; }
        .info-cards { display: flex; gap: 16px; margin-bottom: 24px; }
        .info-card { flex: 1; background: #f8fafc; border-radius: 8px; padding: 14px 16px; border: 1px solid #e5e7eb; }
        .info-card .label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .info-card .value { font-weight: 600; font-size: 14px; color: #1e1b4b; }
        .info-card .sub { font-size: 11px; color: #6b7280; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f1f5f9; font-weight: 600; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; }
        th, td { padding: 10px 14px; text-align: left; }
        th:last-child, td:last-child { text-align: right; }
        td { border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        .row-subtotal td { border-bottom: none; }
        .row-total td { border-top: 3px solid #6366f1; font-weight: 700; font-size: 16px; background: #f8fafc; padding: 14px; }
        .text-green { color: #059669; }
        .text-blue { color: #0891b2; }
        .text-gray { color: #9ca3af; font-size: 11px; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        .footer p { color: #9ca3af; font-size: 10px; margin-bottom: 2px; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="logo-icon">FM</div>
            <div>
                <div class="app-name">{{ config('app.name') }}</div>
                <div style="font-size:11px;color:#6b7280">{{ __('settings.invoice') }}</div>
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-number">{{ $invoice->number }}</div>
            <div class="invoice-date">{{ $invoice->created_at->format('d/m/Y H:i') }}</div>
            <div style="margin-top:6px">
                @if($invoice->isPaid())
                    <span class="badge badge-paid">{{ __('general.paid') }}</span>
                @elseif($invoice->isOverdue())
                    <span class="badge badge-overdue">{{ __('general.overdue') }}</span>
                @else
                    <span class="badge badge-draft">{{ __('general.draft') }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- From / To --}}
    <div style="display:flex;gap:16px;margin-bottom:24px">
        <div style="flex:1;background:#f8fafc;border-radius:8px;padding:14px 16px;border:1px solid #e5e7eb">
            <div class="section-title">{{ __('settings.invoice_from') }}</div>
            <div style="font-weight:600;font-size:14px">{{ config('app.name') }}</div>
        </div>
        <div style="flex:1;background:#f8fafc;border-radius:8px;padding:14px 16px;border:1px solid #e5e7eb">
            <div class="section-title">{{ __('settings.invoice_to') }}</div>
            <div style="font-weight:600;font-size:14px">{{ $invoice->user?->name ?? '—' }}</div>
            @if($invoice->user?->email)
                <div style="font-size:11px;color:#6b7280">{{ $invoice->user->email }}</div>
            @endif
        </div>
    </div>

    {{-- Plan & Period Info --}}
    <div class="info-cards">
        <div class="info-card">
            <div class="label">{{ __('settings.invoice_plan') }}</div>
            <div class="value">{{ $invoice->subscription?->plan?->name ?? '—' }}</div>
            <div class="sub">{{ $invoice->subscription?->billing_period === 'yearly' ? __('general.yearly') : __('general.monthly') }}</div>
        </div>
        <div class="info-card">
            <div class="label">{{ __('settings.invoice_period') }}</div>
            @if($invoice->period_start && $invoice->period_end)
                <div class="value">{{ $invoice->period_start->format('d/m/Y') }}</div>
                <div class="sub">{{ __('general.until') }} {{ $invoice->period_end->format('d/m/Y') }}</div>
            @else
                <div class="value">—</div>
            @endif
        </div>
        <div class="info-card">
            <div class="label">{{ __('settings.payment_method') }}</div>
            <div class="value">{{ $invoice->subscription?->paymentMethod?->key ?? $invoice->subscription?->payment_method ?? '—' }}</div>
            @if($invoice->payment?->transaction_id)
                <div class="sub">{{ $invoice->payment->transaction_id }}</div>
            @endif
        </div>
    </div>

    {{-- Line Items --}}
    <table>
        <thead>
            <tr>
                <th>{{ __('settings.description') }}</th>
                <th>{{ __('settings.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $invoice->subscription?->plan?->name ?? '' }}</strong>
                    <span class="text-gray"> — {{ $invoice->subscription?->billing_period === 'yearly' ? __('general.yearly') : __('general.monthly') }}</span>
                </td>
                <td><strong>{{ $displayPrice($invoice->subtotal, $invoice->currency) }}</strong></td>
            </tr>
            @if((float) $invoice->discount > 0)
            <tr>
                <td class="text-green">
                    {{ __('settings.discount') }}
                    @if($invoice->coupon)
                        <span style="background:#d1fae5;padding:2px 8px;border-radius:4px;font-size:10px;margin-left:4px">{{ $invoice->coupon->code }}</span>
                    @endif
                </td>
                <td class="text-green"><strong>-{{ $displayPrice($invoice->discount, $invoice->currency) }}</strong></td>
            </tr>
            @endif
            @if((float) $invoice->proration_credit > 0)
            <tr>
                <td class="text-blue">
                    {{ __('settings.proration_credit') }}
                </td>
                <td class="text-blue"><strong>-{{ $displayPrice($invoice->proration_credit, $invoice->currency) }}</strong></td>
            </tr>
            @endif
            @if((float) $invoice->gateway_fee > 0)
            <tr>
                <td>{{ __('settings.gateway_fee') }}</td>
                <td>{{ $displayPrice($invoice->gateway_fee, $invoice->currency) }}</td>
            </tr>
            @endif
            @if((float) $invoice->tax_added > 0)
            <tr>
                <td>{{ __('settings.tax') }}</td>
                <td>{{ $displayPrice($invoice->tax_added, $invoice->currency) }}</td>
            </tr>
            @endif
            <tr class="row-total">
                <td>{{ __('settings.total_due') }}</td>
                <td style="color:#6366f1">{{ $displayPrice($invoice->total, $invoice->currency) }}</td>
            </tr>
            @if((float) $invoice->tax_disclosed > 0)
            <tr>
                <td class="text-gray">{{ __('settings.tax_disclosed_label') }}</td>
                <td class="text-gray">{{ $displayPrice($invoice->tax_disclosed, $invoice->currency) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p><strong>{{ config('app.name') }}</strong></p>
        <p>{{ $invoice->created_at->format('Y') }} — {{ __('settings.invoice_generated_at') }} {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
