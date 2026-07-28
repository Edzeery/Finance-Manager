{{-- resources/views/account/subscriptions.blade.php --}}
<x-app-layout>
    @php
        $displayPrice = function (float $usdAmount) use ($userCurrency) {
            $converted = \App\Services\CurrencyHelper::fromUsd($usdAmount, $userCurrency);
            return number_format($converted, 2) . ' ' . \App\Services\CurrencyHelper::symbol($userCurrency);
        };
        $formatAmount = function (float $amount, string $currency) {
            return number_format($amount, 2) . ' ' . \App\Services\CurrencyHelper::symbol($currency);
        };
        $getMethodLabel = function (?string $method) {
            if (!$method) {
                return '—';
            }
            $labels = [
                'chargily' => __('super-admin.chargily'),
                'baridimob' => __('super-admin.baridimob'),
                'paypal' => __('super-admin.paypal'),
                'redotpay' => __('super-admin.redotpay'),
                'cash' => __('general.cash'),
                'delivery' => __('general.delivery'),
                'wise' => 'Wise',
                'wise_manual' => 'Wise Manual',
                'stripe' => 'Stripe',
                'payoneer' => 'Payoneer',
                'noest' => 'Noest',
            ];
            return $labels[$method] ?? ucfirst($method);
        };
        $getMethodType = function (?string $method): ?\App\Models\PaymentMethod {
            if (!$method) {
                return null;
            }
            return \App\Models\PaymentMethod::where('key', $method)->first();
        };
    @endphp

    <x-slot:title>{{ __('settings.subscriptions') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('settings.subscriptions') }}</x-slot>
    <x-slot:page-description>{{ __('settings.subscriptions_desc') }}</x-slot>

    {{-- Pending Payment Alert --}}
    @if ($pendingPayment)
        @php $continueUrl = $pendingPayment->getContinueUrl(); @endphp
        <div class="alert alert-warning d-flex align-items-center gap-3 flex-wrap" role="alert">
            <x-status-icon domain="general" status="warning" set="bi" style="font-size:20px" />
            <span class="flex-grow-1">{{ __('settings.pending_payment_block') }}</span>
            <div class="d-flex gap-2 flex-shrink-0">
                @if ($continueUrl)
                    <x-button href="{{ $continueUrl }}" target="_blank" size="sm" icon="bi bi-credit-card" class="btn-warning">{{ __('settings.complete_payment') }}</x-button>
                @else
                    <x-button href="{{ route('payment.status', $pendingPayment) }}" size="sm" icon="bi bi-credit-card" class="btn-warning">{{ __('settings.complete_payment') }}</x-button>
                @endif
                <form method="POST" action="{{ route('billing.subscriptions.cancel-payment', $pendingPayment) }}"
                    onsubmit="return confirm('{{ __('settings.cancel_payment_confirm') }}')">
                    @csrf
                    <x-button submit size="sm" variant="outline" icon="bi bi-x-lg">{{ __('settings.cancel_payment') }}</x-button>
                </form>
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Current Plan Hero Card --}}
            <div class="settings-section">
                <div class="settings-card" style="overflow:hidden">
                    @if ($subscription && $subscription->plan)
                        @php $plan = $subscription->plan; @endphp
                        <div
                            style="background:linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #000));margin:-1.25rem -1.25rem 1.25rem -1.25rem;padding:1.5rem 1.75rem;position:relative">
                            <div
                                style="position:absolute;top:0;right:0;width:200px;height:200px;background:rgba(255,255,255,0.05);border-radius:50%;transform:translate(50%,-50%)">
                            </div>
                            <div
                                style="position:absolute;bottom:0;left:0;width:150px;height:150px;background:rgba(255,255,255,0.03);border-radius:50%;transform:translate(-30%,30%)">
                            </div>
                            <div class="d-flex align-items-center justify-content-between gap-3"
                                style="position:relative;z-index:1">
                                <div>
                                    <div style="font-size:13px;color:rgba(255,255,255,0.7);margin-bottom:4px">
                                        {{ __('settings.current_plan') }}</div>
                                    <h3 style="font-weight:700;color:#fff;margin-bottom:4px">{{ $plan->name }}</h3>
                                    @if ($plan->isFree())
                                        <span
                                            style="font-size:15px;color:rgba(255,255,255,0.9)">{{ __('settings.free_plan') }}</span>
                                    @else
                                        <span
                                            style="font-size:20px;font-weight:700;color:#fff">{{ $displayPrice($plan->monthly_price) }}</span>
                                        <span
                                            style="font-size:13px;color:rgba(255,255,255,0.7)">/{{ __('general.month') }}</span>
                                        @if ($plan->yearly_price > 0)
                                            <span
                                                style="font-size:13px;color:rgba(255,255,255,0.6);margin-inline-start:8px">{{ $displayPrice($plan->yearly_price) }}/{{ __('general.year') }}</span>
                                        @endif
                                    @endif
                                    @if ($plan->description)
                                        <p
                                            style="font-size:13px;color:rgba(255,255,255,0.7);margin-top:4px;margin-bottom:0">
                                            {{ $plan->description }}</p>
                                    @endif
                                </div>
                                <div class="text-end flex-shrink-0">

                                    <x-status-badge domain="subscription" status="{{ $subscription->status }}"
                                        set="bi" />
                                </div>
                            </div>
                        </div>

                        {{-- Usage Bars --}}
                        @if ($workspace)
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    @php
                                        $userCount = $workspace->userCount();
                                        $userLimit = $workspace->userLimit();
                                        $userPercent =
                                            $userLimit > 0 ? min(100, round(($userCount / $userLimit) * 100)) : 0;
                                    @endphp
                                    <div>
                                        <div class="d-flex justify-content-between text-muted-sm"
                                            style="margin-bottom:4px">
                                            <span><i class="bi bi-people"
                                                    style="margin-inline-end:4px"></i>{{ __('settings.users_usage') }}</span>
                                            <span>{{ $userCount }} / {{ $userLimit }}
                                                {{ __('general.users') }}</span>
                                        </div>
                                        <div class="progress"
                                            style="height:6px;border-radius:3px;background:var(--border)">
                                            <div class="progress-bar" role="progressbar"
                                                style="width:{{ $userPercent }}%;border-radius:3px;background:{{ $userPercent > 80 ? 'var(--danger)' : ($userPercent > 60 ? 'var(--warning)' : 'var(--accent)') }}"
                                                aria-valuenow="{{ $userPercent }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $txCount = app(\App\Services\SubscriptionService::class)->transactionsThisMonth(
                                            $workspace,
                                        );
                                        $txLimit = app(
                                            \App\Services\SubscriptionService::class,
                                        )->maxTransactionsPerMonth($workspace);
                                        $txPercent = $txLimit > 0 ? min(100, round(($txCount / $txLimit) * 100)) : 0;
                                    @endphp
                                    <div>
                                        <div class="d-flex justify-content-between text-muted-sm"
                                            style="margin-bottom:4px">
                                            <span><i class="bi bi-arrow-left-right"
                                                    style="margin-inline-end:4px"></i>{{ __('settings.transactions_usage') }}</span>
                                            <span>{{ $txCount }} / {{ $txLimit }}
                                                {{ __('general.transactions') }}</span>
                                        </div>
                                        <div class="progress"
                                            style="height:6px;border-radius:3px;background:var(--border)">
                                            <div class="progress-bar" role="progressbar"
                                                style="width:{{ $txPercent }}%;border-radius:3px;background:{{ $txPercent > 80 ? 'var(--danger)' : ($txPercent > 60 ? 'var(--warning)' : 'var(--accent)') }}"
                                                aria-valuenow="{{ $txPercent }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Plan Meta Table --}}
                        <div class="row g-0"
                            style="border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1rem">
                            @php
                                $_subMethodKey = $subscription->paymentMethod?->key ?? $subscription->payment_method;
                                $_pm = $getMethodType($_subMethodKey);
                                $_methodTypeLabel = $_pm?->type?->label();
                                $_gatewayName = $_pm?->gateway?->name ?? $getMethodLabel($_subMethodKey);
                            @endphp
                            <div class="col-4"
                                style="border-inline-end:1px solid var(--border);background:var(--bg-subtle)">
                                <div style="padding:12px 16px;text-align:center">
                                    <i class="bi bi-credit-card-2-front"
                                        style="font-size:16px;color:var(--text-muted);margin-bottom:4px;display:block"></i>
                                    <div class="text-muted-sm" style="font-size:11px;margin-bottom:2px">
                                        {{ __('settings.payment_method') }}</div>
                                    <div style="font-weight:600;font-size:13px">{{ $_gatewayName }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:1px">
                                        {{ $_methodTypeLabel }}</div>
                                </div>
                            </div>
                            <div class="col-4"
                                style="border-inline-end:1px solid var(--border);background:var(--bg-subtle)">
                                <div style="padding:12px 16px;text-align:center">
                                    <i class="bi bi-calendar-event"
                                        style="font-size:16px;color:var(--text-muted);margin-bottom:4px;display:block"></i>
                                    <div class="text-muted-sm" style="font-size:11px;margin-bottom:2px">
                                        {{ __('settings.billing_period') }}</div>
                                    <div style="font-weight:600;font-size:13px">
                                        {{ $subscription->billing_period === 'yearly' ? __('general.yearly') : __('general.monthly') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-4"
                                style="border-inline-end:1px solid var(--border);background:var(--bg-subtle)">
                                <div style="padding:12px 16px;text-align:center">
                                    <i class="bi bi-hourglass-split"
                                        style="font-size:16px;color:var(--text-muted);margin-bottom:4px;display:block"></i>
                                    <div class="text-muted-sm" style="font-size:11px;margin-bottom:2px">
                                        {{ __('settings.days_remaining') }}</div>
                                    <div style="font-weight:600;font-size:13px">
                                        {{ $subscription->daysRemaining() . ' ' . __('general.days_left') }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        @if ($isOwner && !$pendingPayment)
                            <div class="d-flex gap-2 flex-wrap">
                                @if ($subscription && $subscription->isActive())
                                    <x-button href="#available-plans" icon="bi bi-arrow-repeat">{{ __('settings.change_plan') }}</x-button>
                                    @if (!$plan->isFree() && !$subscription->canceled_at)
                                        <x-button variant="outline-danger" @click="confirmCancelSubscription()" icon="bi bi-x-circle">{{ __('settings.cancel_subscription') }}</x-button>
                                    @elseif($subscription->canceled_at && $subscription->isOnGrace())
                                        <form method="POST" action="{{ route('billing.subscriptions.resume') }}"
                                            style="display:inline">
                                            @csrf
                                            <x-button submit variant="outline-accent" icon="bi bi-arrow-repeat" onclick="return confirm('{{ __('settings.resume_confirm') }}')">{{ __('settings.resume_subscription') }}</x-button>
                                        </form>
                                    @elseif($subscription->canceled_at)
                                        <span class="text-muted-sm" style="font-size:13px;padding:8px 0">
                                            <i
                                                class="bi bi-info-circle ms-1"></i>{{ __('settings.cancel_scheduled') }}
                                        </span>
                                    @endif
                                @else
                                    <x-button href="#available-plans" icon="bi bi-rocket-takeoff">{{ __('settings.renew_subscription') }}</x-button>
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-credit-card-2-front"
                                style="font-size:48px;color:var(--text-muted);opacity:0.4"></i>
                            <p class="text-muted mt-3 mb-3">{{ __('settings.no_subscription') }}</p>
                            @if ($isOwner)
                                <x-button href="#available-plans" icon="bi bi-rocket-takeoff">{{ __('settings.choose_plan') }}</x-button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Payment History --}}
            <div class="settings-section">
                <div class="settings-card">
                    <h5 class="section-title mb-1 d-flex align-items-center gap-2"><i
                            class="bi bi-receipt text-accent"></i><span>{{ __('settings.payment_history') }}</span>
                    </h5>
                    <p class="section-desc mb-3">{{ __('settings.payment_history_desc') }}</p>

                    @if ($payments && $payments->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('settings.invoice_date') }}</th>
                                        <th>{{ __('settings.invoice_amount') }}</th>
                                        <th>{{ __('settings.payment_method') }}</th>
                                        <th>{{ __('general.status') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
                                        @php $continueUrl = $payment->isPending() ? $payment->getContinueUrl() : null; @endphp
                                        <tr>
                                            <td style="font-size:13px">{{ $payment->created_at->format('Y/m/d H:i') }}
                                            </td>
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
                                                {{ $getMethodLabel($payment->paymentMethod?->key) }}</td>
                                            <td>
                                                <x-status-badge domain="payment" :status="$payment->status->value" set="bi" />
                                                @if ($payment->isRefunded())
                                                    <div style="font-size:11px;color:var(--text-muted);margin-top:4px">
                                                        <i class="bi bi-arrow-counterclockwise"
                                                            style="margin-inline-end:2px"></i>{{ __('general.refunded') }}
                                                        @if ($payment->refund_amount > 0)
                                                            —
                                                            {{ $formatAmount($payment->refund_amount, $payment->currency ?? 'USD') }}
                                                        @endif
                                                        @if ($payment->refunded_at)
                                                            <span
                                                                style="display:block;font-size:10px">{{ $payment->refunded_at->format('Y/m/d H:i') }}</span>
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
                            <div class="mt-3 d-flex justify-content-end">
                                {{ $payments->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <x-empty-state icon="bi bi-inbox" :title="__('settings.no_payments')" />
                        </div>
                    @endif
                </div>
            </div>

            {{-- Subscription History --}}
            @if ($hasSubscriptionHistory && $allSubscriptions->isNotEmpty())
                <div class="settings-section">
                    <div class="settings-card">
                        <h5 class="section-title mb-1 d-flex align-items-center gap-2"><i
                                class="bi bi-clock-history text-accent"></i><span>{{ __('account.subscription_history') }}</span>
                        </h5>
                        <p class="section-desc mb-3">{{ __('account.subscription_history_desc') }}</p>

                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('settings.plan') }}</th>
                                        <th>{{ __('general.status') }}</th>
                                        <th>{{ __('settings.billing_period') }}</th>
                                        <th>{{ __('super-admin.started') }}</th>
                                        <th>{{ __('super-admin.ends_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allSubscriptions as $sub)
                                        @if ($subscription && $sub->id === $subscription->id && $subscription->isActive())
                                            @continue
                                        @endif
                                        <tr>
                                            <td>
                                                <span
                                                    style="font-weight:500">{{ $sub->plan?->name ?? __('general.unknown') }}</span>
                                                @if ($sub->workspace)
                                                    <div style="font-size:12px;color:var(--text-muted)">
                                                        {{ $sub->workspace->name }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <x-status-badge domain="subscription" :status="$sub->status->value"
                                                    set="bi" />
                                            </td>
                                            <td style="font-size:13px">
                                                {{ $sub->billing_period === 'yearly' ? __('general.yearly') : __('general.monthly') }}
                                            </td>
                                            <td style="font-size:13px;color:var(--text-muted)">
                                                {{ $sub->starts_at?->format('Y/m/d') ?? '—' }}</td>
                                            <td style="font-size:13px;color:var(--text-muted)">
                                                {{ $sub->ends_at?->format('Y/m/d') ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Invoices Link --}}
            <div class="settings-section">
                <div class="settings-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="section-title mb-1 d-flex align-items-center gap-2"><i
                                    class="bi bi-file-text text-accent"></i><span>{{ __('settings.invoices') }}</span>
                            </h5>
                            <p class="section-desc mb-0">{{ __('settings.invoices_desc') }}</p>
                        </div>
                        <x-button href="{{ route('billing.invoices.index') }}" icon="bi bi-receipt">{{ __('settings.view_all_invoices') }}</x-button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Available Plans Sidebar --}}
        <div class="col-lg-4" id="available-plans">
            <div class="settings-section" style="position:sticky;top:80px">
                <div class="settings-card">
                    <h5 class="section-title mb-1 d-flex align-items-center gap-2"><i
                            class="bi bi-grid-3x3-gap text-accent"></i><span>{{ __('settings.available_plans') }}</span>
                    </h5>
                    <p class="section-desc mb-3">{{ __('settings.available_plans_desc') }}</p>

                    @if ($pendingPayment)
                        <div class="text-center py-4">
                            <x-empty-state icon="bi bi-exclamation-triangle" :title="__('settings.pending_payment_block')" />
                        </div>
                    @elseif($isOwner)
                        @php $currentPlan = $subscription?->plan; @endphp
                        @php $hasActive = $subscription && $subscription->isActive(); @endphp

                        @forelse($plans as $plan)
                            @php $isCurrent = $currentPlan && $currentPlan->slug === $plan->slug && $hasActive; @endphp
                            <div class="plan-card mb-3 {{ $isCurrent ? 'plan-current' : '' }}"
                                style="border:1px solid {{ $isCurrent ? 'var(--accent)' : 'var(--border)' }};border-radius:12px;padding:16px;transition:all 0.2s"
                                x-data="{ showAll: false }">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 style="font-weight:600;margin-bottom:2px;font-size:14px">
                                            {{ $plan->name }}</h6>
                                        @if ($plan->is_free)
                                            <span
                                                style="font-size:13px;color:var(--text-muted)">{{ __('settings.free') }}</span>
                                        @else
                                            <span
                                                style="font-size:20px;font-weight:700">{{ $displayPrice($plan->monthly_price) }}
                                                <span
                                                    style="font-size:12px;font-weight:400;color:var(--text-muted)">/{{ __('general.month') }}</span>
                                            </span>
                                            @if ($plan->yearly_price > 0)
                                                <div style="font-size:12px;color:var(--text-muted);margin-top:2px">
                                                    {{ $displayPrice($plan->yearly_price) }}/{{ __('general.year') }}
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    @if ($isCurrent)
                                        <x-status-badge domain="general" status="active" set="bi"
                                            size="xs" />
                                    @endif
                                </div>

                                @php $features = $plan->planFeatures; @endphp
                                @if ($features->isNotEmpty())
                                    <div style="margin-bottom:12px">
                                        @foreach ($features as $index => $feature)
                                            @php $fName = $feature->{'name_' . app()->getLocale()} ?? $feature->name_en; @endphp
                                            <div style="font-size:12px;color:var(--text-muted);padding:2px 0"
                                                x-show="showAll || {{ $index < 5 ? 'true' : 'false' }}"
                                                x-transition:enter.duration.200ms>
                                                <x-status-icon domain="general" status="success" set="bi"
                                                    style="margin-inline-end:6px;font-size:11px" />
                                                {{ $fName }}{{ $feature->pivot->value ? ': ' . $feature->pivot->value : '' }}
                                            </div>
                                        @endforeach
                                        @if ($features->count() > 5)
                                            <button type="button" class="btn btn-link p-0"
                                                @click="showAll = !showAll"
                                                style="font-size:12px;color:var(--accent);text-decoration:none">
                                                <span x-show="!showAll">{{ __('general.show_more') }}
                                                    ({{ $features->count() - 5 }})</span>
                                                <span x-show="showAll">{{ __('general.show_less') }}</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif

                                <div style="font-size:12px;color:var(--text-muted);margin-bottom:12px">
                                    <i class="bi bi-people"></i> {{ __('admin.users_count') }}:
                                    {{ $plan->max_users }}
                                </div>

                                @if (!$isCurrent)
                                    <form action="{{ route('billing.subscriptions.change-plan') }}" method="POST"
                                        class="plan-form" onsubmit="return handlePlanSubmit(this)">
                                        @csrf
                                        <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
                                        <input type="hidden" name="billing" value="monthly">

                                        @if (!$plan->is_free)
                                            <div class="mb-2">
                                                <label
                                                    style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:2px">
                                                    <i
                                                        class="bi bi-calendar-check ms-1"></i>{{ __('settings.billing_period') }}
                                                </label>
                                                <select name="billing" class="form-select billing-select"
                                                    style="font-size:12px;padding:5px 8px;width:100%">
                                                    <option value="monthly">{{ $displayPrice($plan->monthly_price) }}
                                                        / {{ __('general.month') }}</option>
                                                    @if ($plan->yearly_price > 0)
                                                        <option value="yearly">
                                                            {{ $displayPrice($plan->yearly_price) }} /
                                                            {{ __('general.year') }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label
                                                    style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:2px">
                                                    <i
                                                        class="bi bi-credit-card ms-1"></i>{{ __('settings.payment_method') }}
                                                </label>
                                                <select name="payment_method" class="form-select" required
                                                    style="font-size:12px;padding:5px 8px;width:100%">
                                                    <option value="">{{ __('payment.select_method') }}</option>
                                                    @foreach ($paymentMethods as $pm)
                                                        <option value="{{ $pm['id'] }}">{{ $pm['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <input type="text" name="coupon" class="form-custom coupon-input"
                                                    placeholder="{{ __('payment.coupon_placeholder') }}"
                                                    data-plan-price="{{ $plan->monthly_price }}"
                                                    data-plan-slug="{{ $plan->slug }}"
                                                    style="font-size:12px;padding:5px 8px;width:100%">
                                            </div>

                                            <div class="fee-breakdown-{{ $plan->id }}" style="display:none">
                                            </div>
                                        @endif

                                        <div class="d-grid gap-2">
                                            @if (!$hasActive)
                                                <button type="submit"
                                                    class="btn btn-accent btn-custom btn-sm renew-btn">
                                                    <span class="btn-text"><i
                                                            class="bi bi-rocket-takeoff ms-1"></i>{{ __('settings.renew_subscription') }}</span>
                                                    <span class="btn-loading d-none"><span
                                                            class="spinner-border spinner-border-sm ms-1"></span><span
                                                            class="btn-redirect-text">{{ __('settings.redirecting_to_payment') }}</span></span>
                                                </button>
                                            @elseif($plan->sort_order > ($currentPlan->sort_order ?? -1))
                                                <button type="button" class="btn btn-accent btn-custom btn-sm"
                                                    onclick="showUpgradeModal(this)">
                                                    <span class="btn-text"><i
                                                            class="bi bi-arrow-up-circle ms-1"></i>{{ __('settings.upgrade') }}</span>
                                                    <span class="btn-loading d-none"><span
                                                            class="spinner-border spinner-border-sm ms-1"></span>{{ __('general.processing') }}</span>
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-outline-secondary btn-custom btn-sm"
                                                    onclick="showUpgradeModal(this)">
                                                    <span class="btn-text"><i
                                                            class="bi bi-arrow-down-circle ms-1"></i>{{ __('settings.downgrade') }}</span>
                                                    <span class="btn-loading d-none"><span
                                                            class="spinner-border spinner-border-sm ms-1"></span>{{ __('general.processing') }}</span>
                                                </button>
                                            @endif
                                        </div>
                                    </form>
                                @else
                                    <div class="d-grid">
                                        <x-button variant="outline-accent" size="sm" icon="bi bi-check-lg" disabled>{{ __('settings.current_plan_label') }}</x-button>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted text-center mb-0 py-3">{{ __('settings.no_plans_available') }}</p>
                        @endforelse
                    @else
                        <p class="text-muted mb-0">{{ __('settings.only_owner_can_change') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .price-breakdown {
                background: var(--bg);
                border: 1px solid var(--border);
                border-radius: 10px;
                padding: 14px 16px;
                margin-bottom: 16px;
            }

            .price-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 13px;
                padding: 4px 0;
                color: var(--text-muted);
            }

            .price-row.total {
                font-weight: 700;
                font-size: 15px;
                color: var(--text);
            }

            .price-row.total.free {
                color: var(--accent);
            }

            .price-row.discount {
                color: var(--success, #28a745);
            }

            .price-divider {
                height: 1px;
                background: var(--border);
                margin: 6px 0;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function confirmCancelSubscription() {
                showConfirmModal(
                    '{{ __('general.confirm') }}',
                    '{{ __('settings.cancel_confirm') }}',
                    (confirmed) => {
                        if (confirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route('billing.subscriptions.cancel') }}';
                            form.innerHTML = '@csrf';
                            document.body.appendChild(form);
                            form.submit();
                        }
                    },
                    '{{ __('settings.cancel_subscription') }}',
                    'btn-danger'
                );
            }

            function handlePlanSubmit(form) {
                const btn = form.querySelector('button[type="submit"]');
                const text = btn.querySelector('.btn-text');
                const loading = btn.querySelector('.btn-loading');
                text.classList.add('d-none');
                loading.classList.remove('d-none');
                btn.disabled = true;
                if (loading.querySelector('.btn-redirect-text')) {
                    loading.querySelector('.btn-redirect-text').textContent = '{{ __('settings.redirecting_to_payment') }}';
                }
                setTimeout(function() {
                    if (btn.disabled) {
                        text.classList.remove('d-none');
                        loading.classList.add('d-none');
                        btn.disabled = false;
                    }
                }, 30000);
                return true;
            }

            function initSubscriptions() {
                document.querySelectorAll('.billing-select').forEach(function(sel) {
                    sel.addEventListener('change', function() {
                        var form = this.closest('.plan-form');
                        var couponInput = form.querySelector('.coupon-input');
                        if (!couponInput) return;
                        var selectedOption = this.options[this.selectedIndex];
                        var priceMatch = selectedOption.text.match(/[\d.]+/);
                        couponInput.dataset.planPrice = priceMatch ? parseFloat(priceMatch[0]) : 0;
                        if (couponInput.value) {
                            validateCoupon(couponInput);
                        }
                        updateFeeBreakdown(form);
                    });
                });

                document.querySelectorAll('.coupon-input').forEach(function(input) {
                    var debounceTimer;
                    input.addEventListener('input', function() {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(function() {
                            validateCoupon(input);
                            updateFeeBreakdown(input.closest('form'));
                        }, 400);
                    });
                });

                document.querySelectorAll('.plan-form select[name="payment_method"]').forEach(function(sel) {
                    sel.addEventListener('change', function() {
                        updateFeeBreakdown(this.closest('form'));
                    });
                });


            }
            initSubscriptions();

            function updateFeeBreakdown(form) {
                if (!form) return;
                var pm = form.querySelector('select[name="payment_method"]');
                if (!pm || !pm.value) return;
                var billing = form.querySelector('select[name="billing"]');
                var coupon = form.querySelector('.coupon-input');
                var planSlug = form.querySelector('input[name="plan_slug"]');
                if (!planSlug) return;

                var params = new URLSearchParams();
                params.set('plan_slug', planSlug.value);
                params.set('billing', billing ? billing.value : 'monthly');
                params.set('payment_method', pm.value);
                if (coupon && coupon.value) params.set('coupon', coupon.value);

                var container = form.querySelector('[class^="fee-breakdown-"]');
                if (!container) return;

                fetch('{{ route('billing.subscriptions.fee-breakdown') }}?' + params.toString())
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(data) {
                        if (!data) return;
                        var fmt = function(amount) {
                            return parseFloat(amount).toFixed(2) + ' ' + data.currency;
                        };
                        var html = '<div class="price-breakdown mb-3">';
                        html += '<div class="price-row original"><span>{{ __('onboarding.plan_price') }}</span><span>' +
                            fmt(data.original) + '</span></div>';
                        if (parseFloat(data.discount_usd) > 0) {
                            html +=
                                '<div class="price-row discount"><span>{{ __('onboarding.coupon_discount') }}</span><span>-' +
                                fmt(data.discount) + '</span></div>';
                        }
                        if (parseFloat(data.gateway_fee_usd) > 0) {
                            html += '<div class="price-row fee"><span>{{ __('onboarding.gateway_fee') }}</span><span>+' +
                                fmt(data.gateway_fee) + '</span></div>';
                        }
                        if (parseFloat(data.tax_added_usd) > 0) {
                            html += '<div class="price-row fee"><span>{{ __('onboarding.tax_added') }}</span><span>+' +
                                fmt(data.tax_added) + '</span></div>';
                        }
                        if (parseFloat(data.tax_disclosed_usd) > 0) {
                            html += '<div class="price-row"><span>{{ __('onboarding.tax_disclosed') }}</span><span>' +
                                fmt(data.tax_disclosed) + '</span></div>';
                        }
                        html += '<div class="price-divider"></div>';
                        html += '<div class="price-row total' + (parseFloat(data.total_usd) <= 0 ? ' free' : '') +
                            '"><span>{{ __('onboarding.total') }}</span><span>';
                        if (parseFloat(data.total_usd) <= 0) {
                            html += '{{ __('onboarding.free') }}';
                        } else {
                            html += fmt(data.total);
                        }
                        html += '</span></div></div>';
                        container.innerHTML = html;
                        container.style.display = 'block';
                    })
                    .catch(function() {});
            }

            function validateCoupon(input) {
                const code = input.value.trim();
                const price = input.dataset.planPrice || 0;
                const feedback = input.nextElementSibling?.classList?.contains('coupon-feedback') ?
                    input.nextElementSibling :
                    (() => {
                        const el = document.createElement('div');
                        el.className = 'coupon-feedback';
                        el.style.cssText = 'font-size:11px;margin-top:2px';
                        input.parentNode.appendChild(el);
                        return el;
                    })();

                if (!code) {
                    feedback.textContent = '';
                    feedback.style.color = '';
                    return;
                }

                fetch('{{ route('coupon.validate', ['code' => '__CODE__', 'amount' => '__AMOUNT__']) }}'
                        .replace('__CODE__', code)
                        .replace('__AMOUNT__', price))
                    .then(r => r.json())
                    .then(data => {
                        if (data.valid) {
                            const label = data.type === 'percentage' ? data.value + '%' :
                                '{{ config('finance.currency_symbol') }}' + data.discount;
                            feedback.textContent = '{{ __('messages.coupon_applied') }} (' + label + ')';
                            feedback.style.color = 'var(--success)';
                        } else {
                            feedback.textContent = data.message || '{{ __('messages.coupon_invalid') }}';
                            feedback.style.color = 'var(--danger)';
                        }
                    })
                    .catch(() => {
                        feedback.textContent = '{{ __('messages.coupon_check_error') }}';
                        feedback.style.color = 'var(--danger)';
                    });
            }
        </script>
    @endpush

    {{-- Upgrade Confirmation Modal --}}
    <div class="modal fade" id="upgradeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-custom">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-semibold" id="upgradeModalTitle">{{ __('settings.upgrade') }}</h5>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                        aria-label="{{ __('general.close') }}"></button>
                </div>
                <div class="modal-body pt-2">
                    <div id="upgradeModalLoading" class="text-center py-4">
                        <div class="spinner-border text-accent" role="status"></div>
                        <p class="text-muted mt-2 mb-0" style="font-size:13px">{{ __('general.processing') }}</p>
                    </div>
                    <div id="upgradeModalContent" style="display:none">
                        {{-- Current Plan --}}
                        <div
                            style="background:var(--bg-subtle);border-radius:10px;padding:14px 16px;margin-bottom:12px">
                            <div
                                style="font-size:11px;color:var(--text-muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;font-weight:600">
                                {{ __('settings.current_plan') }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span style="font-weight:600;font-size:15px" id="upgradeCurrentPlanName">—</span>
                                    <span style="font-size:12px;color:var(--text-muted);margin-inline-start:6px"
                                        id="upgradeCurrentBilling">—</span>
                                </div>
                                <div style="font-size:13px;color:var(--text-muted)" id="upgradeRemainingDays">—</div>
                            </div>
                        </div>

                        {{-- Arrow --}}
                        <div class="text-center" style="margin:8px 0">
                            <i class="bi bi-arrow-down" style="font-size:20px;color:var(--accent)"></i>
                        </div>

                        {{-- New Plan --}}
                        <div
                            style="background:color-mix(in srgb, var(--accent) 8%, transparent);border:1px solid color-mix(in srgb, var(--accent) 25%, transparent);border-radius:10px;padding:14px 16px;margin-bottom:16px">
                            <div
                                style="font-size:11px;color:var(--accent);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;font-weight:600">
                                {{ __('settings.new_plan') }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span style="font-weight:600;font-size:15px" id="upgradeNewPlanName">—</span>
                                    <span style="font-size:12px;color:var(--text-muted);margin-inline-start:6px"
                                        id="upgradeNewBilling">—</span>
                                </div>
                                <div style="font-weight:600;font-size:15px" id="upgradeNewPlanPrice">—</div>
                            </div>
                        </div>

                        {{-- Proration Details (shown only for plan changes) --}}
                        <div id="upgradeProrationSection" style="display:none">
                            <div
                                style="border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:16px">
                                <div
                                    style="background:var(--bg-subtle);padding:10px 16px;font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px">
                                    <i class="bi bi-calculator"
                                        style="margin-inline-end:4px"></i>{{ __('settings.proration_breakdown') }}
                                </div>
                                <div style="padding:12px 16px">
                                    <div class="d-flex justify-content-between" style="padding:6px 0;font-size:13px">
                                        <span style="color:var(--text-muted)" id="upgradeProrationLabel">—</span>
                                        <span style="font-weight:500" id="upgradeProrationRemainingValue">—</span>
                                    </div>
                                    <div class="d-flex justify-content-between" style="padding:6px 0;font-size:13px">
                                        <span
                                            style="color:var(--text-muted)">{{ __('settings.cost_at_new_rate') }}</span>
                                        <span style="font-weight:500" id="upgradeProrationCostAtNewRate">—</span>
                                    </div>
                                    <div style="height:1px;background:var(--border);margin:6px 0"></div>
                                    <div class="d-flex justify-content-between"
                                        style="padding:6px 0;font-size:14px;font-weight:700">
                                        <span id="upgradeProrationDiffLabel">{{ __('settings.amount_due') }}</span>
                                        <span id="upgradeProrationAmountDue" style="color:var(--accent)">—</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Totals --}}
                        <div id="upgradeTotalsSection" style="display:none">
                            <div
                                style="border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:16px">
                                <div style="padding:12px 16px">
                                    <div id="upgradeDiscountRow" class="d-flex justify-content-between"
                                        style="padding:4px 0;font-size:13px;display:none">
                                        <span
                                            style="color:var(--success, #28a745)">{{ __('onboarding.coupon_discount') }}</span>
                                        <span style="font-weight:500;color:var(--success, #28a745)"
                                            id="upgradeDiscountAmount">—</span>
                                    </div>
                                    <div id="upgradeGatewayFeeRow" class="d-flex justify-content-between"
                                        style="padding:4px 0;font-size:13px;display:none">
                                        <span
                                            style="color:var(--text-muted)">{{ __('onboarding.gateway_fee') }}</span>
                                        <span style="font-weight:500" id="upgradeGatewayFee">—</span>
                                    </div>
                                    <div id="upgradeTaxRow" class="d-flex justify-content-between"
                                        style="padding:4px 0;font-size:13px;display:none">
                                        <span style="color:var(--text-muted)">{{ __('settings.tax') }}</span>
                                        <span style="font-weight:500" id="upgradeTaxAmount">—</span>
                                    </div>
                                    <div style="height:1px;background:var(--border);margin:6px 0"></div>
                                    <div class="d-flex justify-content-between" style="padding:8px 0">
                                        <span
                                            style="font-weight:700;font-size:16px">{{ __('onboarding.total') }}</span>
                                        <span style="font-weight:700;font-size:18px;color:var(--accent)"
                                            id="upgradeTotalAmount">—</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- New End Date --}}
                        <div
                            style="background:var(--bg-subtle);border-radius:10px;padding:12px 16px;margin-bottom:16px">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:13px;color:var(--text-muted)"><i class="bi bi-calendar-check"
                                        style="margin-inline-end:4px"></i>{{ __('settings.new_end_date') }}</span>
                                <span style="font-weight:600;font-size:13px" id="upgradeNewEndDate">—</span>
                            </div>
                        </div>

                        {{-- Warning for same billing period --}}
                        <div id="upgradePeriodWarning"
                            style="display:none;background:color-mix(in srgb, var(--warning) 10%, transparent);border:1px solid color-mix(in srgb, var(--warning) 30%, transparent);border-radius:10px;padding:12px 16px;margin-bottom:16px">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle"
                                    style="color:var(--warning);font-size:16px;margin-top:2px"></i>
                                <span style="font-size:13px;color:var(--text)" id="upgradePeriodWarningText">—</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0" id="upgradeModalFooter" style="display:none">
                    <x-button variant="outline" class="px-4" data-bs-dismiss="modal">{{ __('general.cancel') }}</x-button>
                    <button type="button" class="btn btn-accent btn-custom px-4" id="upgradeModalConfirmBtn"
                        onclick="confirmUpgrade()">
                        <span class="btn-text"><i
                                class="bi bi-check-lg ms-1"></i>{{ __('settings.confirm_upgrade') }}</span>
                        <span class="btn-loading d-none"><span
                                class="spinner-border spinner-border-sm ms-1"></span>{{ __('general.processing') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                let upgradeTargetForm = null;
                let upgradeModalData = null;

                window.showUpgradeModal = showUpgradeModal;
                window.confirmUpgrade = confirmUpgrade;

                function showUpgradeModal(btn) {
                    const form = btn.closest('form');
                    const paymentMethod = form.querySelector('select[name="payment_method"]');
                    if (!paymentMethod || !paymentMethod.value) {
                        if (paymentMethod) {
                            paymentMethod.focus();
                            paymentMethod.classList.add('is-invalid');
                            setTimeout(() => paymentMethod.classList.remove('is-invalid'), 3000);
                        }
                        return;
                    }
                    upgradeTargetForm = form;
                    const modal = new bootstrap.Modal(document.getElementById('upgradeModal'));
                    modal.show();

                    document.getElementById('upgradeModalLoading').style.display = '';
                    document.getElementById('upgradeModalContent').style.display = 'none';
                    document.getElementById('upgradeModalFooter').style.display = 'none';

                    const planSlug = form.querySelector('input[name="plan_slug"]').value;
                    const billing = form.querySelector('select[name="billing"]').value;
                    const coupon = form.querySelector('.coupon-input')?.value || '';

                    const params = new URLSearchParams({
                        plan_slug: planSlug,
                        billing: billing,
                        payment_method: paymentMethod.value
                    });
                    if (coupon) params.set('coupon', coupon);

                    fetch('{{ route('billing.subscriptions.fee-breakdown') }}?' + params.toString())
                        .then(r => r.json())
                        .then(data => {
                            upgradeModalData = data;
                            renderUpgradeModal(data, form);
                        })
                        .catch(() => {
                            document.getElementById('upgradeModalLoading').style.display = 'none';
                            document.getElementById('upgradeModalContent').style.display = '';
                            document.getElementById('upgradeModalFooter').style.display = 'none';
                            document.getElementById('upgradeProrationSection').style.display = 'none';
                            document.getElementById('upgradeTotalsSection').style.display = 'none';
                            document.getElementById('upgradeNewEndDate').textContent = '—';
                            document.getElementById('upgradeCurrentPlanName').textContent = '—';
                            document.getElementById('upgradeNewPlanName').textContent = '—';
                            document.getElementById('upgradePeriodWarning').style.display = 'none';
                        });
                }

                function renderUpgradeModal(data, form) {
                    const fmt = (amount) => parseFloat(amount).toFixed(2) + ' ' + data.currency;

                    const planSlug = form.querySelector('input[name="plan_slug"]').value;
                    const billing = form.querySelector('select[name="billing"]').value;
                    const planCard = form.closest('.plan-card');
                    const planName = planCard?.querySelector('h6')?.textContent || planSlug;

                    @if ($subscription && $subscription->plan)
                        document.getElementById('upgradeCurrentPlanName').textContent =
                            '{{ addslashes($subscription->plan->name) }}';
                        document.getElementById('upgradeCurrentBilling').textContent =
                            '{{ $subscription->billing_period === 'yearly' ? __('general.yearly') : __('general.monthly') }}';
                        const daysRemaining = {{ $subscription->daysRemaining() }};
                        document.getElementById('upgradeRemainingDays').textContent = daysRemaining +
                            ' {{ __('general.days_left') }}';
                        const currentEndsAt = @json($subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : null);
                    @else
                        document.getElementById('upgradeCurrentPlanName').textContent = '{{ __('settings.no_plan') }}';
                        document.getElementById('upgradeCurrentBilling').textContent = '—';
                        const daysRemaining = 0;
                        document.getElementById('upgradeCurrentEndDate').textContent = '—';
                        const currentEndsAt = null;
                    @endif

                    document.getElementById('upgradeNewPlanName').textContent = planName;
                    document.getElementById('upgradeNewBilling').textContent = billing === 'yearly' ?
                        '{{ __('general.yearly') }}' : '{{ __('general.monthly') }}';
                    document.getElementById('upgradeNewPlanPrice').textContent = fmt(data.original);

                    const proration = data.proration;
                    const prorationSection = document.getElementById('upgradeProrationSection');
                    const totalsSection = document.getElementById('upgradeTotalsSection');
                    const periodWarning = document.getElementById('upgradePeriodWarning');

                    if (data.is_plan_change && proration && proration.remaining_days > 0) {
                        prorationSection.style.display = '';
                        document.getElementById('upgradeProrationLabel').textContent =
                            '{{ __('settings.remaining_value') }} (' + proration.remaining_days +
                            ' {{ __('general.days') }} / ' + proration.total_days + ')';
                        document.getElementById('upgradeProrationRemainingValue').textContent = '-' + fmt(proration
                            .remaining_value);
                        document.getElementById('upgradeProrationCostAtNewRate').textContent = fmt(proration
                            .cost_at_new_rate);

                        if (proration.is_upgrade) {
                            document.getElementById('upgradeModalTitle').textContent = '{{ __('settings.upgrade') }}';
                            document.getElementById('upgradeProrationDiffLabel').textContent =
                                '{{ __('settings.amount_due') }}';
                            document.getElementById('upgradeProrationAmountDue').style.color = 'var(--accent)';
                            document.getElementById('upgradeModalConfirmBtn').querySelector('.btn-text i').className = 'bi bi-arrow-up-circle ms-1';
                        } else {
                            document.getElementById('upgradeModalTitle').textContent = '{{ __('settings.downgrade') }}';
                            document.getElementById('upgradeProrationDiffLabel').textContent =
                                '{{ __('settings.credit') }}';
                            document.getElementById('upgradeProrationAmountDue').style.color = 'var(--success, #28a745)';
                            document.getElementById('upgradeModalConfirmBtn').querySelector('.btn-text i').className = 'bi bi-arrow-down-circle ms-1';
                        }
                        document.getElementById('upgradeProrationAmountDue').textContent = fmt(proration.amount_due);
                    } else {
                        prorationSection.style.display = 'none';
                    }

                    const discountRow = document.getElementById('upgradeDiscountRow');
                    const gatewayFeeRow = document.getElementById('upgradeGatewayFeeRow');
                    const taxRow = document.getElementById('upgradeTaxRow');

                    if (parseFloat(data.discount_usd) > 0) {
                        discountRow.style.display = '';
                        document.getElementById('upgradeDiscountAmount').textContent = '-' + fmt(data.discount);
                    } else {
                        discountRow.style.display = 'none';
                    }
                    if (parseFloat(data.gateway_fee_usd) > 0) {
                        gatewayFeeRow.style.display = '';
                        document.getElementById('upgradeGatewayFee').textContent = '+' + fmt(data.gateway_fee);
                    } else {
                        gatewayFeeRow.style.display = 'none';
                    }
                    if (parseFloat(data.tax_added_usd) > 0) {
                        taxRow.style.display = '';
                        document.getElementById('upgradeTaxAmount').textContent = '+' + fmt(data.tax_added);
                    } else {
                        taxRow.style.display = 'none';
                    }

                    totalsSection.style.display = '';
                    document.getElementById('upgradeTotalAmount').textContent = fmt(data.total);

                    if (currentEndsAt) {
                        document.getElementById('upgradeNewEndDate').textContent = currentEndsAt;
                        periodWarning.style.display = 'none';
                    } else {
                        const newEnd = billing === 'yearly' ?
                            new Date(new Date().setFullYear(new Date().getFullYear() + 1)) :
                            new Date(new Date().setMonth(new Date().getMonth() + 1));
                        document.getElementById('upgradeNewEndDate').textContent = newEnd.toISOString().split('T')[0];
                        periodWarning.style.display = '';
                        document.getElementById('upgradePeriodWarningText').textContent =
                            '{{ __('settings.upgrade_period_starts_now') }}';
                    }

                    document.getElementById('upgradeModalLoading').style.display = 'none';
                    document.getElementById('upgradeModalContent').style.display = '';
                    document.getElementById('upgradeModalFooter').style.display = '';
                }

                function confirmUpgrade() {
                    if (!upgradeTargetForm) return;
                    const btn = document.getElementById('upgradeModalConfirmBtn');
                    const text = btn.querySelector('.btn-text');
                    const loading = btn.querySelector('.btn-loading');
                    text.classList.add('d-none');
                    loading.classList.remove('d-none');
                    btn.disabled = true;
                    upgradeTargetForm.querySelector('button[type="submit"],button[onclick]')?.removeAttribute('onclick');
                    const submitBtn = document.createElement('input');
                    submitBtn.type = 'hidden';
                    submitBtn.name = '_upgrade_confirmed';
                    submitBtn.value = '1';
                    upgradeTargetForm.appendChild(submitBtn);
                    upgradeTargetForm.submit();
                }
            })();
        </script>
    @endpush
</x-app-layout>
