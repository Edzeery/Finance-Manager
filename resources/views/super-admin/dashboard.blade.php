<x-super-admin-layout>
    @php
        $baseCur = $data['base_currency'] ?? \App\Services\CurrencyHelper::baseCurrency();
        $baseSym = \App\Services\CurrencyHelper::symbol($baseCur);
        $periods = [
            'all_time' => ['label_key' => 'filters.all_time'],
            'this_month' => ['label_key' => 'filters.this_month'],
            'last_month' => ['label_key' => 'filters.last_month'],
            'last_7_days' => ['label_key' => 'filters.last_7_days'],
            'custom' => ['label_key' => 'filters.custom'],
        ];
    @endphp
    <x-slot:title>{{ __('super-admin.super_dashboard') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.super_dashboard') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.dashboard_desc') }}</x-slot>

    <x-filter-tabs :tabs="[
        'overview' => ['label' => __('super-admin.overview'), 'icon' => 'bi-grid-1x2-fill'],
        'revenue' => ['label' => __('super-admin.revenue'), 'icon' => 'bi-currency-dollar'],
        'subscriptions' => ['label' => __('super-admin.subscriptions'), 'icon' => 'bi-credit-card'],
        'team' => ['label' => __('super-admin.team_performance'), 'icon' => 'bi-people-fill'],
    ]" current="{{ $data['current_tab'] }}" keyParam="tab" :preserve="['period','start_date','end_date','gateway','plan_id','member_id']" />

    <div class="mb-4">
        <x-date-filter-bar :periods="$periods" currentPeriod="{{ $data['period'] }}" startDate="{{ $data['start_date'] }}" endDate="{{ $data['end_date'] }}" />
    </div>

    {{-- ==================== TAB: OVERVIEW ==================== --}}
    @if($data['current_tab'] === 'overview')
    <div class="kpi-grid stagger-fade-in mb-4">
        <div class="kpi-card kpi-green">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-green"><i class="bi bi-currency-dollar"></i></div>
                <span class="kpi-card-trend up">
                    <i class="bi bi-arrow-up"></i>{{ number_format(($data['total_revenue'] ? $data['revenue_this_month'] / max($data['total_revenue'], 1) * 100 : 0), 1) }}%
                </span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.total_revenue') }}</div>
            <div class="kpi-card-value">{{ number_format($data['total_revenue'], 0) }} {{ $baseSym }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-cash-coin"></i> {{ __('super-admin.net_revenue') }}: {{ number_format($data['net_revenue'], 0) }} {{ $baseSym }}
            </div>
        </div>

        <div class="kpi-card kpi-indigo">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-indigo"><i class="bi bi-people-fill"></i></div>
                <span class="kpi-card-trend up"><i class="bi bi-person-check"></i>{{ $data['active_users'] }} {{ __('general.active') }}</span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.users') }}</div>
            <div class="kpi-card-value">{{ $data['total_users'] }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-circle-fill text-success" style="font-size:0.5rem"></i> {{ $data['online_users'] }} {{ __('general.online') }}
                &nbsp;&middot;&nbsp;
                <i class="bi bi-shield-shaded"></i> {{ $data['super_admins'] ?? '—' }} {{ __('super-admin.super_admins') }}
            </div>
        </div>

        <div class="kpi-card kpi-blue">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-blue"><i class="bi bi-building"></i></div>
                <span class="kpi-card-trend up"><i class="bi bi-check-circle"></i>{{ $data['active_workspaces'] }} {{ __('general.active') }}</span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.workspaces') }}</div>
            <div class="kpi-card-value">{{ $data['total_workspaces'] }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-building"></i> {{ __('super-admin.total') }}
            </div>
        </div>

        <div class="kpi-card kpi-green">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-green"><i class="bi bi-credit-card"></i></div>
                <span class="kpi-card-trend up"><i class="bi bi-check-circle"></i>{{ $data['active_subscriptions'] }} {{ __('super-admin.active') }}</span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.active_subscriptions') }}</div>
            <div class="kpi-card-value">{{ $data['active_subscriptions'] }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-x-circle"></i> {{ $data['canceled_subscriptions'] }} {{ __('super-admin.canceled') }}
            </div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-amber"><i class="bi bi-hourglass-split"></i></div>
                <span class="kpi-card-trend {{ $data['pending_amount'] > 0 ? 'down' : 'up' }}">
                    <i class="bi bi-cash"></i>{{ number_format($data['pending_amount'], 0) }} {{ $baseSym }}
                </span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.pending_payments') }}</div>
            <div class="kpi-card-value">{{ $data['pending_payments'] }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-check-circle"></i> {{ $data['completed_payments'] }} {{ __('super-admin.completed') }}
            </div>
        </div>

        <div class="kpi-card kpi-purple">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-purple"><i class="bi bi-tags-fill"></i></div>
                <span class="kpi-card-trend up">{{ $data['total_coupon_uses'] ?? '—' }} {{ __('super-admin.total_uses') }}</span>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.coupon_stats') }}</div>
            <div class="kpi-card-value">{{ $data['active_coupons'] ?? '—' }} / {{ $data['total_coupons'] ?? '—' }}</div>
            <div class="kpi-card-compare">
                <i class="bi bi-clock"></i> {{ $data['expired_coupons'] ?? '—' }} {{ __('super-admin.expired') }}
            </div>
        </div>
    </div>

    <div class="analytics-grid mb-4">
        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-pie-chart-fill"></i><span>{{ __('super-admin.subscriptions_by_plan') }}</span></h5>
            </div>
            <div class="section-card-body">
                @forelse(($data['subscriptions_by_plan'] ?? []) as $slug => $count)
                    @php
                        $total = max(array_sum($data['subscriptions_by_plan'] ?? []), 1);
                        $colors = ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:10px;height:10px;border-radius:50%;background:{{ $colors[$loop->index % count($colors)] }};flex-shrink:0"></div>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;font-weight:500;color:var(--text)">{{ ucfirst($slug) }}</span>
                                <span style="font-size:12px;font-weight:600;color:var(--text-muted)">{{ $count }} ({{ round($count / $total * 100, 1) }}%)</span>
                            </div>
                            <div style="height:6px;background:var(--border);border-radius:3px;overflow:hidden">
                                <div style="height:100%;width:{{ $count / $total * 100 }}%;background:{{ $colors[$loop->index % count($colors)] }};border-radius:3px"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state"><div class="empty-icon" style="background:var(--border);color:var(--text-muted)"><i class="bi bi-pie-chart"></i></div><h4>{{ __('general.no_data') }}</h4></div>
                @endforelse
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart-fill"></i><span>{{ __('super-admin.revenue_by_gateway') }}</span></h5>
            </div>
            <div class="section-card-body">
                @forelse(($data['revenue_by_gateway'] ?? []) as $gateway => $total)
                    @php
                        $grandTotal = max(array_sum($data['revenue_by_gateway'] ?? []), 1);
                        $colors = ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#8B5CF6', '#EF4444'];
                        $icons = ['chargily' => 'bi-credit-card-2-front', 'baridimob' => 'bi-phone', 'redotpay' => 'bi-currency-bitcoin', 'wise_manual' => 'bi-bank', 'cash' => 'bi-cash', 'paypal' => 'bi-paypal'];
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:14px;color:{{ $colors[$loop->index % count($colors)] }};flex-shrink:0">
                            <i class="bi {{ $icons[$gateway] ?? 'bi-credit-card' }}"></i>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;font-weight:500;color:var(--text)">{{ __("super-admin.{$gateway}") }}</span>
                                <span style="font-size:12px;font-weight:600;color:var(--text-muted)">{{ number_format($total, 0) }} {{ $baseSym }}</span>
                            </div>
                            <div style="height:5px;background:var(--border);border-radius:3px;overflow:hidden">
                                <div style="height:100%;width:{{ $total / $grandTotal * 100 }}%;background:{{ $colors[$loop->index % count($colors)] }};border-radius:3px"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state"><div class="empty-icon" style="background:var(--border);color:var(--text-muted)"><i class="bi bi-bar-chart"></i></div><h4>{{ __('general.no_data') }}</h4></div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-clock-history"></i><span>{{ __('super-admin.recent_payments') }}</span></h5>
                </div>
                <div class="section-card-body p-0">
                    @if(($data['recent_payments'] ?? collect())->count())
                        @foreach($data['recent_payments'] as $payment)
                            <div class="activity-item px-4">
                                <div class="activity-icon" style="background:var(--accent-light);color:var(--accent)"><i class="bi bi-arrow-down-left"></i></div>
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
                        <div class="empty-state"><div class="empty-icon" style="background:var(--border);color:var(--text-muted)"><i class="bi bi-clock-history"></i></div><h4>{{ __('general.no_data') }}</h4></div>
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
                        <a href="{{ route('super.admin.users.index') }}" class="quick-action-card"><i class="bi bi-people-fill"></i><span>{{ __('super-admin.users') }}</span></a>
                        <a href="{{ route('super.admin.workspaces.index') }}" class="quick-action-card"><i class="bi bi-building"></i><span>{{ __('super-admin.workspaces') }}</span></a>
                        <a href="{{ route('super.admin.subscriptions.index') }}" class="quick-action-card"><i class="bi bi-credit-card"></i><span>{{ __('super-admin.subscriptions') }}</span></a>
                        <a href="{{ route('super.admin.payments.index') }}" class="quick-action-card"><i class="bi bi-cash-coin"></i><span>{{ __('super-admin.payments') }}</span></a>
                        <a href="{{ route('super.admin.plans.index') }}" class="quick-action-card"><i class="bi bi-box"></i><span>{{ __('super-admin.plans') }}</span></a>
                        <a href="{{ route('super.admin.settings.index') }}" class="quick-action-card"><i class="bi bi-gear-fill"></i><span>{{ __('super-admin.settings') }}</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB: REVENUE ==================== --}}
    @elseif($data['current_tab'] === 'revenue')
    @php
        $revGW = $data['gateway_keys'] ?? [];
        $revPlans = $data['plan_options'] ?? [];
    @endphp
    <div class="kpi-grid stagger-fade-in mb-4">
        <div class="kpi-card kpi-green">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-green"><i class="bi bi-currency-dollar"></i></div>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.total_revenue') }}</div>
            <div class="kpi-card-value">{{ number_format($data['gross'], 0) }} {{ $baseSym }}</div>
        </div>
        <div class="kpi-card kpi-blue">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-blue"><i class="bi bi-cash-coin"></i></div>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.net_revenue') }}</div>
            <div class="kpi-card-value">{{ number_format($data['net'], 0) }} {{ $baseSym }}</div>
            <div class="kpi-card-compare">{{ __('super-admin.total_fees') }}: {{ number_format($data['fees'], 0) }} {{ $baseSym }}</div>
        </div>
        <div class="kpi-card kpi-amber">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-amber"><i class="bi bi-arrow-uturn-left"></i></div>
            </div>
            <div class="kpi-card-label">{{ __('super-admin.refunded_amount') }}</div>
            <div class="kpi-card-value">{{ number_format($data['refunded'], 0) }} {{ $baseSym }}</div>
            <div class="kpi-card-compare">{{ __('super-admin.refund_rate') }}: {{ $data['refund_rate'] }}%</div>
        </div>
        <div class="kpi-card kpi-purple">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-purple"><i class="bi bi-graph-up-arrow"></i></div>
            </div>
            <div class="kpi-card-label">MRR</div>
            <div class="kpi-card-value">{{ number_format($data['mrr'], 0) }} {{ $baseSym }}</div>
            <div class="kpi-card-compare">ARR: {{ number_format($data['arr'], 0) }} {{ $baseSym }}</div>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <form method="GET" action="{{ route('super.admin.dashboard') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="revenue">
            <input type="hidden" name="period" value="{{ $data['period'] }}">
            @if(!empty($revGW))
            <select name="gateway" class="form-control" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value="">{{ __('general.all_methods') }}</option>
                @foreach($revGW as $gw)
                    <option value="{{ $gw }}" {{ request('gateway') === $gw ? 'selected' : '' }}>{{ __("super-admin.{$gw}") }}</option>
                @endforeach
            </select>
            @endif
            @if(!empty($revPlans))
            <select name="plan_id" class="form-control" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value="">{{ __('general.all_plans') }}</option>
                @foreach($revPlans as $id => $name)
                    <option value="{{ $id }}" {{ request('plan_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
        </form>
    </div>

    <div class="analytics-grid mb-4">
        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart-fill"></i><span>{{ __('super-admin.monthly_revenue') }}</span></h5>
            </div>
            <div class="section-card-body">
                <div x-data="superAdminChart('revenueChart', 'line', {
                    series: [
                        { name: '{{ __("super-admin.total_revenue") }}', data: {{ json_encode($data['monthly_gross'] ?? []) }} },
                        { name: '{{ __("super-admin.net_revenue") }}', data: {{ json_encode($data['monthly_net'] ?? []) }} },
                    ],
                    xaxis: { categories: {{ json_encode($data['monthly_labels'] ?? []) }} },
                    colors: ['#15b76c', '#3B82F6'],
                })">
                    <div style="min-height:300px"></div>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-pie-chart-fill"></i><span>{{ __('super-admin.revenue_by_gateway') }}</span></h5>
            </div>
            <div class="section-card-body">
                <div x-data="superAdminChart('revGatewayChart', 'donut', {
                    series: {{ json_encode(array_values($data['by_gateway'] ?? [])) }},
                    labels: {{ json_encode(collect($data['by_gateway'] ?? [])->keys()->map(fn($k) => __("super-admin.{$k}"))->toArray()) }},
                    colors: ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'],
                })">
                    <div style="min-height:280px"></div>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-layers-fill"></i><span>{{ __('super-admin.revenue_by_plan') }}</span></h5>
            </div>
            <div class="section-card-body">
                <div x-data="superAdminChart('revPlanChart', 'bar', {
                    series: [{ name: '{{ __("super-admin.revenue") }}', data: {{ json_encode(array_values($data['by_plan'] ?? [])) }} }],
                    xaxis: { categories: {{ json_encode(array_keys($data['by_plan'] ?? [])) }} },
                    colors: ['#6366F1'],
                })">
                    <div style="min-height:280px"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB: SUBSCRIPTIONS ==================== --}}
    @elseif($data['current_tab'] === 'subscriptions')
    @php $subPlans = $data['plan_options'] ?? []; @endphp
    <div class="kpi-grid stagger-fade-in mb-4">
        <div class="kpi-card kpi-green">
            <div class="kpi-card-header"><div class="kpi-card-icon kpi-icon-green"><i class="bi bi-check-circle"></i></div></div>
            <div class="kpi-card-label">{{ __('super-admin.active') }}</div>
            <div class="kpi-card-value">{{ $data['active'] }}</div>
        </div>
        <div class="kpi-card kpi-amber">
            <div class="kpi-card-header"><div class="kpi-card-icon kpi-icon-amber"><i class="bi bi-hourglass-split"></i></div></div>
            <div class="kpi-card-label">{{ __('super-admin.suspended') }}</div>
            <div class="kpi-card-value">{{ $data['suspended'] }}</div>
        </div>
        <div class="kpi-card kpi-red" style="--kpi-accent:#EF4444">
            <div class="kpi-card-header"><div class="kpi-card-icon" style="background:rgba(239,68,68,0.12);color:#EF4444"><i class="bi bi-x-circle"></i></div></div>
            <div class="kpi-card-label">{{ __('super-admin.canceled') }}</div>
            <div class="kpi-card-value">{{ $data['canceled'] }}</div>
        </div>
        <div class="kpi-card" style="--kpi-accent:#9ca3af">
            <div class="kpi-card-header"><div class="kpi-card-icon" style="background:rgba(156,163,175,0.12);color:#9ca3af"><i class="bi bi-clock"></i></div></div>
            <div class="kpi-card-label">{{ __('super-admin.expired') }}</div>
            <div class="kpi-card-value">{{ $data['expired'] }}</div>
        </div>
        <div class="kpi-card kpi-purple">
            <div class="kpi-card-header"><div class="kpi-card-icon kpi-icon-purple"><i class="bi bi-percent"></i></div></div>
            <div class="kpi-card-label">{{ __('super-admin.churn_rate') }}</div>
            <div class="kpi-card-value">{{ $data['churn_rate'] }}%</div>
        </div>
        <div class="kpi-card kpi-blue">
            <div class="kpi-card-header"><div class="kpi-card-icon kpi-icon-blue"><i class="bi bi-clock-history"></i></div></div>
            <div class="kpi-card-label">{{ __('super-admin.avg_lifetime') }}</div>
            <div class="kpi-card-value">{{ $data['avg_lifetime_days'] }}</div>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <form method="GET" action="{{ route('super.admin.dashboard') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="subscriptions">
            <input type="hidden" name="period" value="{{ $data['period'] }}">
            @if(!empty($subPlans))
            <select name="plan_id" class="form-control" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value="">{{ __('general.all_plans') }}</option>
                @foreach($subPlans as $id => $name)
                    <option value="{{ $id }}" {{ request('plan_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-pie-chart-fill"></i><span>{{ __('super-admin.subscriptions_by_plan') }}</span></h5>
                </div>
                <div class="section-card-body">
                    @forelse(($data['by_plan'] ?? []) as $slug => $count)
                        @php
                            $total = max(array_sum($data['by_plan'] ?? []), 1);
                            $colors = ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];
                        @endphp
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:10px;height:10px;border-radius:50%;background:{{ $colors[$loop->index % count($colors)] }};flex-shrink:0"></div>
                            <div style="flex:1;min-width:0">
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="font-size:13px;font-weight:500;color:var(--text)">{{ ucfirst($slug) }}</span>
                                    <span style="font-size:12px;font-weight:600;color:var(--text-muted)">{{ $count }} ({{ round($count / $total * 100, 1) }}%)</span>
                                </div>
                                <div style="height:6px;background:var(--border);border-radius:3px;overflow:hidden">
                                    <div style="height:100%;width:{{ $count / $total * 100 }}%;background:{{ $colors[$loop->index % count($colors)] }};border-radius:3px"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state"><div class="empty-icon" style="background:var(--border);color:var(--text-muted)"><i class="bi bi-pie-chart"></i></div><h4>{{ __('general.no_data') }}</h4></div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart-fill"></i><span>{{ __('super-admin.subscription_status') }}</span></h5>
                </div>
                <div class="section-card-body">
                    <div x-data="superAdminChart('subStatusChart', 'donut', {
                        series: [{{ $data['active'] }}, {{ $data['canceled'] }}, {{ $data['expired'] }}, {{ $data['suspended'] }}],
                        labels: ['{{ __("super-admin.active") }}', '{{ __("super-admin.canceled") }}', '{{ __("super-admin.expired") }}', '{{ __("super-admin.suspended") }}'],
                        colors: ['#15b76c', '#EF4444', '#9ca3af', '#F59E0B'],
                    })">
                        <div style="min-height:280px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB: TEAM ==================== --}}
    @elseif($data['current_tab'] === 'team')
    @php
        $memberOpts = $data['member_options'] ?? [];
        $members = $data['members'] ?? [];
    @endphp

    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <form method="GET" action="{{ route('super.admin.dashboard') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="team">
            <input type="hidden" name="period" value="{{ $data['period'] }}">
            @if(!empty($memberOpts))
            <select name="member_id" class="form-control" style="width:auto;min-width:180px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value="">{{ __('super-admin.all_team') }}</option>
                @foreach($memberOpts as $id => $name)
                    <option value="{{ $id }}" {{ request('member_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
        </form>
    </div>

    @if(!empty($members))
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-people-fill"></i><span>{{ __('super-admin.team_members_performance') }}</span></h5>
                </div>
                <div class="section-card-body p-0">
                    <div class="table-responsive">
                        <table class="data-table mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('super-admin.member') }}</th>
                                    <th>{{ __('general.roles') }}</th>
                                    <th>{{ __('super-admin.verifications') }}</th>
                                    <th>{{ __('super-admin.verified_amount') }}</th>
                                    <th>{{ __('super-admin.refunds') }}</th>
                                    <th>{{ __('super-admin.refunded_amount_short') }}</th>
                                    <th>{{ __('super-admin.last_active') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:8px">
                                            <div style="width:32px;height:32px;border-radius:50%;background:var(--accent-light);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:600;font-size:13px">
                                                {{ substr($member['name'], 0, 2) }}
                                            </div>
                                            <div>
                                                <div style="font-weight:500;font-size:13px">{{ $member['name'] }}</div>
                                                <div style="font-size:11px;color:var(--text-muted)">{{ $member['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge" style="font-size:10px;background:var(--bg-subtle);color:var(--text);padding:2px 8px;border-radius:4px">{{ $member['role'] }}</span></td>
                                    <td style="font-weight:600">{{ $member['verifications_count'] }}</td>
                                    <td>{{ number_format($member['verifications_total'], 0) }} {{ $baseSym }}</td>
                                    <td>{{ $member['refunds_count'] }}</td>
                                    <td>{{ number_format($member['refunds_total'], 0) }} {{ $baseSym }}</td>
                                    <td style="font-size:12px;color:var(--text-muted)">
                                        {{ $member['last_activity'] ? $member['last_activity']->diffForHumans() : '—' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart-fill"></i><span>{{ __('super-admin.team_comparison') }}</span></h5>
                </div>
                <div class="section-card-body">
                    <div x-data="superAdminChart('teamChart', 'bar', {
                        series: [
                            { name: '{{ __("super-admin.verifications") }}', data: {{ json_encode(collect($members)->pluck('verifications_count')->toArray()) }} },
                            { name: '{{ __("super-admin.refunds") }}', data: {{ json_encode(collect($members)->pluck('refunds_count')->toArray()) }} },
                        ],
                        xaxis: { categories: {{ json_encode(collect($members)->pluck('name')->toArray()) }} },
                        colors: ['#15b76c', '#EF4444'],
                    })">
                        <div style="min-height:300px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($data['roles']))
    <div class="section-card">
        <div class="section-card-header">
            <h5 class="d-flex align-items-center gap-2"><i class="bi bi-shield-check"></i><span>{{ __('super-admin.team_roles') }}</span></h5>
        </div>
        <div class="section-card-body">
            <div class="d-flex flex-wrap gap-3">
                @foreach($data['roles'] as $role)
                <div style="flex:1;min-width:140px;padding:16px;background:var(--bg);border-radius:var(--radius-sm);text-align:center">
                    <div style="font-size:24px;font-weight:700;color:var(--text)">{{ $role['members_count'] }}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:4px">{{ $role['name'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @else
    <div class="empty-state">
        <div class="empty-icon" style="background:var(--border);color:var(--text-muted)"><i class="bi bi-people"></i></div>
        <h4>{{ __('general.no_data') }}</h4>
    </div>
    @endif
    @endif
</x-super-admin-layout>