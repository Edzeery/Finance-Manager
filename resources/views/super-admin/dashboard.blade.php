<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.super_dashboard') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.super_dashboard') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.dashboard_desc') }}</x-slot>

    {{-- KPI Grid --}}
    <div class="kpi-grid stagger-fade-in mb-4">
        <div class="kpi-card kpi-green">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-green">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <span class="kpi-card-trend up">
                    <i class="bi bi-arrow-up"></i>{{ number_format(($kpis['total_revenue'] ? $kpis['revenue_this_month'] / max($kpis['total_revenue'], 1) * 100 : 0), 1) }}%
                </span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.total_revenue') }}</div>
            <div class="kpi-card-value">{{ number_format($kpis['total_revenue'], 0) }} {{ config('finance.currency_symbol') }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-calendar3"></i> {{ __('super-admin.this_month') }}: {{ number_format($kpis['revenue_this_month'], 0) }} {{ config('finance.currency_symbol') }}
            </div>
        </div>

        <div class="kpi-card kpi-indigo">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-indigo">
                    <i class="bi bi-people-fill"></i>
                </div>
                <span class="kpi-card-trend up">
                    <i class="bi bi-person-check"></i>{{ $kpis['active_users'] }} {{ __('general.active') }}
                </span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.users') }}</div>
            <div class="kpi-card-value">{{ $kpis['total_users'] }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-shield-shaded"></i> {{ $kpis['super_admins'] }} {{ __('super-admin.super_admins') }}
            </div>
        </div>

        <div class="kpi-card kpi-blue">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-blue">
                    <i class="bi bi-building"></i>
                </div>
                <span class="kpi-card-trend up">
                    <i class="bi bi-check-circle"></i>{{ $kpis['active_workspaces'] }} {{ __('general.active') }}
                </span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.workspaces') }}</div>
            <div class="kpi-card-value">{{ $kpis['total_workspaces'] }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-building"></i> {{ __('super-admin.total') }}
            </div>
        </div>

        <div class="kpi-card kpi-green">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-green">
                    <i class="bi bi-credit-card"></i>
                </div>
                <span class="kpi-card-trend up">
                    <i class="bi bi-check-circle"></i>{{ $kpis['active_subscriptions'] }} {{ __('super-admin.active') }}
                </span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.active_subscriptions') }}</div>
            <div class="kpi-card-value">{{ $kpis['active_subscriptions'] }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-x-circle"></i> {{ $kpis['canceled_subscriptions'] }} {{ __('super-admin.canceled') }}
            </div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-amber">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <span class="kpi-card-trend {{ $kpis['pending_amount'] > 0 ? 'down' : 'up' }}">
                    <i class="bi bi-cash"></i>{{ number_format($kpis['pending_amount'], 0) }} {{ config('finance.currency_symbol') }}
                </span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.pending_payments') }}</div>
            <div class="kpi-card-value">{{ $kpis['pending_payments'] }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-check-circle"></i> {{ $kpis['completed_payments'] }} {{ __('super-admin.completed') }}
            </div>
        </div>

        <div class="kpi-card kpi-purple">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-purple">
                    <i class="bi bi-tags-fill"></i>
                </div>
                <span class="kpi-card-trend up">{{ $kpis['total_coupon_uses'] }} {{ __('super-admin.total_uses') }}</span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.coupon_stats') }}</div>
            <div class="kpi-card-value">{{ $kpis['active_coupons'] }} / {{ $kpis['total_coupons'] }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-clock"></i> {{ $kpis['expired_coupons'] }} {{ __('super-admin.expired') }}
            </div>
        </div>
    </div>

    {{-- Analytics Section --}}
    <div class="analytics-grid mb-4">
        {{-- Subscriptions by Plan --}}
        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-pie-chart-fill"></i><span>{{ __('super-admin.subscriptions_by_plan') }}</span></h5>
            </div>
            <div class="section-card-body">
                @forelse($kpis['subscriptions_by_plan'] as $slug => $count)
                    @php
                        $total = max(array_sum($kpis['subscriptions_by_plan']->toArray()), 1);
                        $percent = round($count / $total * 100, 1);
                        $colors = ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];
                        $color = $colors[$loop->index % count($colors)];
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:10px;height:10px;border-radius:50%;background:{{ $color }};flex-shrink:0"></div>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;font-weight:500;color:var(--text)">{{ ucfirst($slug) }}</span>
                                <span style="font-size:12px;font-weight:600;color:var(--text-muted)">{{ $count }} ({{ $percent }}%)</span>
                            </div>
                            <div style="height:6px;background:var(--border);border-radius:3px;overflow:hidden">
                                <div style="height:100%;width:{{ $percent }}%;background:{{ $color }};border-radius:3px;transition:width 0.6s ease"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--border);color:var(--text-muted)">
                            <i class="bi bi-pie-chart"></i>
                        </div>
                        <h4>{{ __('general.no_data') }}</h4>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Revenue by Gateway --}}
        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart-fill"></i><span>{{ __('super-admin.revenue_by_gateway') }}</span></h5>
            </div>
            <div class="section-card-body">
                @forelse($kpis['revenue_by_gateway'] as $gateway => $total)
                    @php
                        $grandTotal = max(array_sum($kpis['revenue_by_gateway']->toArray()), 1);
                        $pct = round($total / $grandTotal * 100, 1);
                        $colors = ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#8B5CF6', '#EF4444'];
                        $color = $colors[$loop->index % count($colors)];
                        $icons = ['chargily' => 'bi-credit-card-2-front', 'baridimob' => 'bi-phone', 'redotpay' => 'bi-currency-bitcoin', 'wise_manual' => 'bi-bank', 'cash' => 'bi-cash', 'paypal' => 'bi-paypal'];
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:14px;color:{{ $color }};flex-shrink:0">
                            <i class="bi {{ $icons[$gateway] ?? 'bi-credit-card' }}"></i>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;font-weight:500;color:var(--text)">{{ __("super-admin.{$gateway}") }}</span>
                                <span style="font-size:12px;font-weight:600;color:var(--text-muted)">{{ number_format($total, 0) }} {{ config('finance.currency_symbol') }}</span>
                            </div>
                            <div style="height:5px;background:var(--border);border-radius:3px;overflow:hidden">
                                <div style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:3px;transition:width 0.6s ease"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--border);color:var(--text-muted)">
                            <i class="bi bi-bar-chart"></i>
                        </div>
                        <h4>{{ __('general.no_data') }}</h4>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Activity & Quick Actions --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-clock-history"></i><span>{{ __('super-admin.recent_payments') }}</span></h5>
                </div>
                <div class="section-card-body p-0">
                    @if($kpis['recent_payments']->count())
                        @foreach($kpis['recent_payments'] as $payment)
                            <div class="activity-item px-4">
                                <div class="activity-icon" style="background:var(--accent-light);color:var(--accent)">
                                    <i class="bi bi-arrow-down-left"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-text">
                                        <strong>{{ $payment->workspace?->name ?? '—' }}</strong>
                                        {{ __('general.paid') }}
                                        <strong>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</strong>
                                    </div>
                                    <div class="activity-meta">
                                        <span>{{ $payment->subscription?->plan?->name ?? '—' }}</span>
                                        <span class="activity-dot"></span>
                                        <span>{{ $payment->paid_at?->diffForHumans() ?? $payment->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon" style="background:var(--border);color:var(--text-muted)">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <h4>{{ __('general.no_data') }}</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-box-arrow-in-right"></i><span>{{ __('super-admin.quick_actions') }}</span></h5>
                </div>
                <div class="section-card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('super.admin.users.index') }}" class="quick-action-card">
                            <i class="bi bi-people-fill"></i>
                            <span>{{ __('super-admin.users') }}</span>
                        </a>
                        <a href="{{ route('super.admin.workspaces.index') }}" class="quick-action-card">
                            <i class="bi bi-building"></i>
                            <span>{{ __('super-admin.workspaces') }}</span>
                        </a>
                        <a href="{{ route('super.admin.subscriptions.index') }}" class="quick-action-card">
                            <i class="bi bi-credit-card"></i>
                            <span>{{ __('super-admin.subscriptions') }}</span>
                        </a>
                        <a href="{{ route('super.admin.payments.index') }}" class="quick-action-card">
                            <i class="bi bi-cash-coin"></i>
                            <span>{{ __('super-admin.payments') }}</span>
                        </a>
                        <a href="{{ route('super.admin.plans.index') }}" class="quick-action-card">
                            <i class="bi bi-box"></i>
                            <span>{{ __('super-admin.plans') }}</span>
                        </a>
                        <a href="{{ route('super.admin.settings.index') }}" class="quick-action-card">
                            <i class="bi bi-gear-fill"></i>
                            <span>{{ __('super-admin.settings') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
