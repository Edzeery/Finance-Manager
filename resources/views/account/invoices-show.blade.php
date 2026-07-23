<x-app-layout>
    @php
        $displayPrice = function (float $amount, ?string $currency = null) use ($userCurrency) {
            $cur = $currency ?: $userCurrency;
            return number_format($amount, 2) . ' ' . \App\Services\CurrencyHelper::symbol($cur);
        };
        $sub = $invoice->subscription;
        $plan = $sub?->plan;
    @endphp
    <x-slot:title>{{ $invoice->number }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ $invoice->number }}</x-slot>
    <x-slot:page-description>{{ __('settings.invoice_details') }}</x-slot>

    <div class="settings-card" id="invoice-print-area">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="{{ config('app.name') }}" style="height:40px" onerror="this.style.display='none'">
                <div>
                    <h4 class="mb-0" style="font-weight:700">{{ config('app.name') }}</h4>
                    <p class="text-muted mb-0" style="font-size:13px">{{ __('settings.invoice') }}</p>
                </div>
            </div>
            <div class="text-end">
                <div style="font-weight:700;font-size:18px;margin-bottom:4px">{{ $invoice->number }}</div>
                <p class="text-muted mb-1" style="font-size:13px">{{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                <x-status-badge domain="invoice" :status="$invoice->status->value" set="bi" />
            </div>
        </div>

        {{-- From / To --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div style="background:var(--bg-subtle);border-radius:8px;padding:16px">
                    <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;margin-bottom:8px">{{ __('settings.invoice_from') }}</div>
                    <div style="font-weight:600;font-size:15px">{{ config('app.name') }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div style="background:var(--bg-subtle);border-radius:8px;padding:16px">
                    <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;margin-bottom:8px">{{ __('settings.invoice_to') }}</div>
                    <div style="font-weight:600;font-size:15px">{{ $invoice->user?->name ?? '—' }}</div>
                    <div style="font-size:13px;color:var(--text-muted)">{{ $invoice->user?->email ?? '' }}</div>
                </div>
            </div>
        </div>

        {{-- Plan & Period Info --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div style="background:var(--bg-subtle);border-radius:8px;padding:16px">
                    <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;margin-bottom:6px">{{ __('settings.invoice_plan') }}</div>
                    <div style="font-weight:600">{{ $plan?->name ?? '—' }}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:2px">{{ $sub?->billing_period === 'yearly' ? __('general.yearly') : __('general.monthly') }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:var(--bg-subtle);border-radius:8px;padding:16px">
                    <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;margin-bottom:6px">{{ __('settings.invoice_period') }}</div>
                    @if($invoice->period_start && $invoice->period_end)
                        <div style="font-weight:600">{{ $invoice->period_start->format('d/m/Y') }}</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:2px">{{ __('general.until') }} {{ $invoice->period_end->format('d/m/Y') }}</div>
                    @else
                        <div style="font-weight:600">—</div>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:var(--bg-subtle);border-radius:8px;padding:16px">
                    <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;margin-bottom:6px">{{ __('settings.payment_method') }}</div>
                    <div style="font-weight:600">{{ $invoice->subscription?->paymentMethod?->key ?? $invoice->subscription?->payment_method ?? '—' }}</div>
                    @if($invoice->payment?->transaction_id)
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px">{{ $invoice->payment->transaction_id }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Line Items --}}
        <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1rem">
            <table style="width:100%;border-collapse:collapse;font-size:14px">
                <thead>
                    <tr style="background:var(--bg-subtle)">
                        <th style="padding:12px 16px;text-align:start;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:0.5px">{{ __('settings.description') }}</th>
                        <th style="padding:12px 16px;text-align:end;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:0.5px">{{ __('settings.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border)">
                            <div style="font-weight:500">{{ __('settings.plan_price') }} — {{ $plan?->name ?? '' }}</div>
                            <div style="font-size:12px;color:var(--text-muted)">{{ $sub?->billing_period === 'yearly' ? __('general.yearly') : __('general.monthly') }}</div>
                        </td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500">{{ $displayPrice($invoice->subtotal, $invoice->currency) }}</td>
                    </tr>
                    @if((float) $invoice->discount > 0)
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);color:var(--success, #28a745)">
                            <i class="bi bi-tag" style="margin-inline-end:4px"></i>{{ __('settings.discount') }}
                            @if($invoice->coupon)
                                <span style="font-size:11px;background:color-mix(in srgb, var(--success, #28a745) 12%, transparent);padding:2px 6px;border-radius:4px;margin-inline-start:4px">{{ $invoice->coupon->code }}</span>
                            @endif
                        </td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500;color:var(--success, #28a745)">-{{ $displayPrice($invoice->discount, $invoice->currency) }}</td>
                    </tr>
                    @endif
                    @if((float) $invoice->proration_credit > 0)
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);color:var(--info)">
                            <i class="bi bi-arrow-counterclockwise" style="margin-inline-end:4px"></i>{{ __('settings.proration_credit') }}
                            <div style="font-size:11px;color:var(--text-muted);margin-top:2px">{{ __('settings.remaining_value') }} ({{ $sub?->billing_period === 'yearly' ? __('general.yearly') : __('general.monthly') }})</div>
                        </td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500;color:var(--info)">-{{ $displayPrice($invoice->proration_credit, $invoice->currency) }}</td>
                    </tr>
                    @endif
                    @if((float) $invoice->gateway_fee > 0)
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border)">
                            <i class="bi bi-credit-card" style="margin-inline-end:4px"></i>{{ __('settings.gateway_fee') }}
                        </td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500">{{ $displayPrice($invoice->gateway_fee, $invoice->currency) }}</td>
                    </tr>
                    @endif
                    @if((float) $invoice->tax_added > 0)
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border)">
                            <i class="bi bi-receipt" style="margin-inline-end:4px"></i>{{ __('settings.tax') }}
                        </td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500">{{ $displayPrice($invoice->tax_added, $invoice->currency) }}</td>
                    </tr>
                    @endif
                    <tr style="background:var(--bg-subtle)">
                        <td style="padding:14px 16px;border-top:2px solid var(--border);font-weight:700;font-size:15px">{{ __('settings.total_due') }}</td>
                        <td style="padding:14px 16px;border-top:2px solid var(--border);text-align:end;font-weight:700;font-size:18px;color:var(--accent)">{{ $displayPrice($invoice->total, $invoice->currency) }}</td>
                    </tr>
                    @if((float) $invoice->tax_disclosed > 0)
                    <tr>
                        <td style="padding:8px 16px;border-top:1px dashed var(--border);font-size:12px;color:var(--text-muted)">
                            <i class="bi bi-info-circle"></i>
                            {{ __('settings.tax_disclosed_label') }}
                        </td>
                        <td style="padding:8px 16px;border-top:1px dashed var(--border);text-align:end;font-size:12px;color:var(--text-muted)">{{ $displayPrice($invoice->tax_disclosed, $invoice->currency) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="text-center" style="padding:16px 0;border-top:1px solid var(--border);margin-top:8px">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="{{ config('app.name') }}" style="height:24px;opacity:0.4;margin-bottom:8px" onerror="this.style.display='none'">
            <p style="font-size:12px;color:var(--text-muted);margin-bottom:0">{{ config('app.name') }} — {{ $invoice->created_at->format('Y') }}</p>
            <p style="font-size:11px;color:var(--text-muted);margin-bottom:0">{{ __('settings.invoice_generated_at') }} {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="d-flex gap-2 mt-4 no-print">
            <a href="{{ route('account.invoices.index') }}" class="btn btn-outline-secondary btn-custom">
                <i class="bi bi-arrow-left ms-1"></i>{{ __('general.back') }}
            </a>
            @if($invoice->isPaid())
            <a href="{{ route('account.invoices.pdf', $invoice) }}" class="btn btn-accent btn-custom">
                <i class="bi bi-file-earmark-pdf ms-1"></i>{{ __('general.download_pdf') ?? 'PDF' }}
            </a>
            <button type="button" class="btn btn-accent btn-custom" onclick="window.print()">
                <i class="bi bi-printer ms-1"></i>{{ __('general.print') ?? 'طباعة' }}
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
