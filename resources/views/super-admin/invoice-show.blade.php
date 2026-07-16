<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.invoices') }} {{ $invoice->number }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.invoice') }} {{ $invoice->number }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.invoices_desc') }}</x-slot>

    <div class="detail-grid">
        <div class="detail-main">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-file-text"></i>{{ __('super-admin.invoice_details') }}</h5>
                    <x-status-badge domain="invoice" :status="$invoice->status->value" set="bi" />
                </div>
                <div class="section-card-body">
                    <table class="info-table">
                        <tr>
                            <td class="info-label">{{ __('super-admin.invoice_workspace') }}</td>
                            <td class="info-value">{{ $invoice->workspace?->name ?? __('general.unknown') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('super-admin.invoice_plan') }}</td>
                            <td class="info-value">{{ $invoice->subscription?->plan?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('settings.invoice_period') }}</td>
                            <td class="info-value">{{ $invoice->period_start?->format('Y/m/d') ?? '—' }} &rarr; {{ $invoice->period_end?->format('Y/m/d') ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('super-admin.payer') }}</td>
                            <td class="info-value">{{ $invoice->user?->name ?? '—' }} ({{ $invoice->user?->email ?? '—' }})</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('general.date') }}</td>
                            <td class="info-value">{{ $invoice->created_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        @if($invoice->paid_at)
                        <tr>
                            <td class="info-label">{{ __('general.paid') }}</td>
                            <td class="info-value">{{ $invoice->paid_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        @endif
                        @if($invoice->due_at)
                        <tr>
                            <td class="info-label">{{ __('settings.invoice_due') }}</td>
                            <td class="info-value">{{ $invoice->due_at->format('Y/m/d') }}</td>
                        </tr>
                        @endif
                    </table>

                    <hr style="margin:20px 0;border-color:var(--border-light);border-style:solid">

                    <table class="info-table">
                        <tr>
                            <td class="info-label" style="color:var(--text-muted)">{{ __('settings.invoice_subscription') }}</td>
                            <td class="info-value text-end">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</td>
                        </tr>
                        @if($invoice->discount > 0)
                        <tr>
                            <td class="info-label" style="color:var(--text-muted)">{{ __('settings.invoice_discount') }}</td>
                            <td class="info-value text-end" style="color:var(--danger)">-{{ number_format($invoice->discount, 2) }} {{ $invoice->currency }}</td>
                        </tr>
                        @endif
                        @if($invoice->proration_credit > 0)
                        <tr>
                            <td class="info-label" style="color:var(--info)">{{ __('super-admin.proration_credit') ?? 'براتا' }}</td>
                            <td class="info-value text-end" style="color:var(--info)">-{{ number_format($invoice->proration_credit, 2) }} {{ $invoice->currency }}</td>
                        </tr>
                        @endif
                        @if($invoice->gateway_fee > 0)
                        <tr>
                            <td class="info-label" style="color:var(--text-muted)">{{ __('super-admin.gateway_fee') ?? 'رسم بوابة' }}</td>
                            <td class="info-value text-end">{{ number_format($invoice->gateway_fee, 2) }} {{ $invoice->currency }}</td>
                        </tr>
                        @endif
                        @if($invoice->tax_added > 0)
                        <tr>
                            <td class="info-label" style="color:var(--text-muted)">{{ __('settings.invoice_tax') }}</td>
                            <td class="info-value text-end">{{ number_format($invoice->tax_added, 2) }} {{ $invoice->currency }}</td>
                        </tr>
                        @endif
                        @if($invoice->tax_disclosed > 0)
                        <tr>
                            <td class="info-label" style="font-size:11px;color:var(--text-muted);font-style:italic">
                                <i class="bi bi-info-circle"></i> {{ __('super-admin.tax_disclosed') ?? 'ضريبة إفصاح (غير مضافة)' }}
                            </td>
                            <td class="info-value text-end" style="font-size:12px;color:var(--text-muted)">{{ number_format($invoice->tax_disclosed, 2) }} {{ $invoice->currency }}</td>
                        </tr>
                        @endif
                        <tr style="border-top:1px solid var(--border-light)">
                            <td class="info-label" style="font-weight:600;color:var(--text)">{{ __('settings.invoice_total') }}</td>
                            <td class="info-value text-end" style="font-weight:700;font-size:18px;color:var(--text)">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="detail-sidebar">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-info-circle"></i>{{ __('general.details') }}</h5>
                </div>
                <div class="section-card-body">
                    <table class="info-table">
                        <tr>
                            <td class="info-label">{{ __('settings.currency') }}</td>
                            <td class="info-value">{{ $invoice->currency }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('settings.invoice_period') }}</td>
                            <td class="info-value">{{ $invoice->billing_period ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('settings.invoice_auto') }}</td>
                            <td class="info-value">{{ $invoice->subscription?->auto_renew ? __('general.yes') : __('general.no') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('super.admin.invoices.index') }}" class="btn" style="padding:8px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="bi bi-arrow-left"></i>{{ __('general.back') }}
        </a>
    </div>
</x-super-admin-layout>
