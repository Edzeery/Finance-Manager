<x-app-layout>
    @php
        $displayPrice = function (float $amount, ?string $currency = null) use ($userCurrency) {
            $cur = $currency ?: $userCurrency;
            return number_format($amount, 2) . ' ' . \App\Services\CurrencyHelper::symbol($cur);
        };
    @endphp
    <x-slot:title>{{ $invoice->number }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ $invoice->number }}</x-slot>
    <x-slot:page-description>{{ __('settings.invoice_details') }}</x-slot>

    <div class="settings-card" id="invoice-print-area">
        <div class="d-flex justify-content-between align-items-start mb-4 no-print">
            <div>
                <h4 class="mb-1" style="font-weight:700">{{ $invoice->number }}</h4>
                <p class="text-muted mb-0">{{ $invoice->created_at->format('F d, Y') }}</p>
            </div>
            <x-status-badge domain="invoice" :status="$invoice->status->value" set="bi" />
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div style="background:var(--bg-subtle);border-radius:8px;padding:16px">
                    <div class="text-muted-sm mb-1" style="font-size:12px">{{ __('settings.invoice_plan') }}</div>
                    <div style="font-weight:600">{{ $invoice->subscription?->plan?->name ?? '—' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:var(--bg-subtle);border-radius:8px;padding:16px">
                    <div class="text-muted-sm mb-1" style="font-size:12px">{{ __('settings.invoice_period') }}</div>
                    <div style="font-weight:600">
                        @if($invoice->period_start && $invoice->period_end)
                            {{ $invoice->period_start->format('M Y') }} — {{ $invoice->period_end->format('M Y') }}
                        @else
                            —
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:var(--bg-subtle);border-radius:8px;padding:16px">
                    <div class="text-muted-sm mb-1" style="font-size:12px">{{ __('settings.payment_method') }}</div>
                    <div style="font-weight:600">{{ $invoice->subscription?->payment_method ?? '—' }}</div>
                </div>
            </div>
        </div>

        <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1rem">
            <table style="width:100%;border-collapse:collapse;font-size:14px">
                <thead>
                    <tr style="background:var(--bg-subtle)">
                        <th style="padding:12px 16px;text-align:start;font-weight:600;font-size:13px">{{ __('settings.description') }}</th>
                        <th style="padding:12px 16px;text-align:end;font-weight:600;font-size:13px">{{ __('settings.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border)">{{ __('settings.subtotal') }} ({{ $invoice->subscription?->plan?->name ?? '' }})</td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500">{{ $displayPrice($invoice->subtotal, $invoice->currency) }}</td>
                    </tr>
                    @if((float) $invoice->discount > 0)
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);color:var(--danger)">
                            {{ __('settings.discount') }}
                            @if($invoice->coupon)
                                <span style="font-size:12px;color:var(--text-muted)">({{ $invoice->coupon->code }})</span>
                            @endif
                        </td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500;color:var(--danger)">-{{ $displayPrice($invoice->discount, $invoice->currency) }}</td>
                    </tr>
                    @endif
                    @if((float) $invoice->proration_credit > 0)
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);color:var(--info)">
                            {{ __('settings.proration_credit') ?? 'رصيد براتا' }}
                        </td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500;color:var(--info)">-{{ $displayPrice($invoice->proration_credit, $invoice->currency) }}</td>
                    </tr>
                    @endif
                    @if((float) $invoice->gateway_fee > 0)
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border)">{{ __('settings.gateway_fee') ?? 'رسم بوابة دفع' }}</td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500">{{ $displayPrice($invoice->gateway_fee, $invoice->currency) }}</td>
                    </tr>
                    @endif
                    @if((float) $invoice->tax_added > 0)
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border)">{{ __('settings.tax') }}</td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500">{{ $displayPrice($invoice->tax_added, $invoice->currency) }}</td>
                    </tr>
                    @endif
                    <tr style="background:var(--bg-subtle)">
                        <td style="padding:12px 16px;border-top:2px solid var(--border);font-weight:700">{{ __('settings.total_due') }}</td>
                        <td style="padding:12px 16px;border-top:2px solid var(--border);text-align:end;font-weight:700;font-size:16px">{{ $displayPrice($invoice->total, $invoice->currency) }}</td>
                    </tr>
                    @if((float) $invoice->tax_disclosed > 0)
                    <tr>
                        <td style="padding:8px 16px;border-top:1px dashed var(--border);font-size:12px;color:var(--text-muted)">
                            <i class="bi bi-info-circle"></i>
                            {{ __('settings.tax_disclosed_label') ?? 'ضريبة/زكاة إفصاح (غير مضافة للمبلغ)' }}
                        </td>
                        <td style="padding:8px 16px;border-top:1px dashed var(--border);text-align:end;font-size:12px;color:var(--text-muted)">{{ $displayPrice($invoice->tax_disclosed, $invoice->currency) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex gap-2 mt-4 no-print">
            <a href="{{ route('account.invoices.index') }}" class="btn btn-outline-secondary btn-custom">
                <i class="bi bi-arrow-leftms-1"></i>{{ __('general.back') }}
            </a>
            @if($invoice->isPaid())
            <a href="{{ route('account.invoices.pdf', $invoice) }}" class="btn btn-accent btn-custom">
                <i class="bi bi-file-earmark-pdfms-1"></i>{{ __('general.download_pdf') ?? 'PDF' }}
            </a>
            <button type="button" class="btn btn-accent btn-custom" onclick="window.print()">
                <i class="bi bi-printerms-1"></i>{{ __('general.print') ?? 'طباعة' }}
            </button>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
    @media print {
        body { background: #fff !important; }
        .no-print { display: none !important; }
        #invoice-print-area {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .settings-card {
            background: #fff !important;
            border: none !important;
            box-shadow: none !important;
        }
        table { page-break-inside: avoid; }
    }
    </style>
    @endpush
</x-app-layout>