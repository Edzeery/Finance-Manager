<x-app-layout>
    @php
        $displayPrice = function (float $amount, ?string $currency = null) use ($userCurrency) {
            $cur = $currency ?: $userCurrency;
            return number_format($amount, 2) . ' ' . \App\Services\CurrencyHelper::symbol($cur);
        };
    @endphp
    <x-slot:title>{{ __('settings.invoices') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('settings.invoices') }}</x-slot>
    <x-slot:page-description>{{ __('settings.invoices_desc') }}</x-slot>

    @if(!$hasSubscriptions)
            <div class="settings-card">
            <div class="text-center py-5">
                <x-empty-state icon="bi bi-receipt" :title="__('settings.no_subscription')" />
            </div>
        </div>
    @else
    <x-filter-tabs :tabs="[
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-list-ul'],
        'paid' => ['label' => __('general.paid'), 'count' => $countPaid, 'icon' => 'bi-check-circle'],
        'overdue' => ['label' => __('general.overdue'), 'count' => $countOverdue, 'icon' => 'bi-exclamation-triangle'],
        'draft' => ['label' => __('general.draft'), 'count' => $countDraft, 'icon' => 'bi-pencil'],
        'cancelled' => ['label' => __('general.cancelled'), 'count' => $countCancelled, 'icon' => 'bi-x-circle'],
    ]" current="{{ request('status', 'all') }}" keyParam="status" />

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div></div>
        <div class="d-flex gap-2 align-items-center">
            <x-per-page :current="(int) request('per_page', 15)" />
        </div>
    </div>

    <div class="card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('settings.invoice_number') }}</th>
                            <th>{{ __('settings.invoice_plan') }}</th>
                            <th>{{ __('settings.invoice_amount') }}</th>
                            <th>{{ __('settings.invoice_period') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th>{{ __('general.date') }}</th>
                            <th class="text-end">{{ __('general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>
                                    <code style="font-size:13px;font-weight:600">{{ $invoice->number }}</code>
                                </td>
                                <td>{{ $invoice->subscription?->plan?->name ?? '—' }}</td>
                                <td style="font-weight:600">{{ $displayPrice($invoice->total, $invoice->currency) }}</td>
                                <td style="font-size:13px">
                                    @if($invoice->period_start && $invoice->period_end)
                                        {{ $invoice->period_start->format('M Y') }} — {{ $invoice->period_end->format('M Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <x-status-badge domain="invoice" :status="$invoice->status->value" set="bi" />
                                </td>
                                <td style="font-size:13px;color:var(--text-muted)">{{ $invoice->created_at->format('Y/m/d') }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('account.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary btn-custom" title="{{ __('general.view') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-receipt" style="font-size:40px;color:var(--text-muted);opacity:0.3"></i>
                                    <p class="text-muted mt-2 mb-0">{{ __('settings.no_invoices') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($invoices) && $invoices->hasPages())
                <div class="p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <x-pagination-info :items="$invoices" />
                    <div>
                        {{ $invoices->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif
</x-app-layout>
