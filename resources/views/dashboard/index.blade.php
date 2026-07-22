<x-app-layout>
    <x-slot:title>{{ __('dashboard.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('dashboard.title') }}</x-slot>
    <x-slot:page-description>{{ __("filters.{$period}") }}: <strong>{{ config('finance.currency_symbol') }} {{ number_format($kpi->netBalance, 2) }}</strong></x-slot>

    <x-filter-tabs :tabs="[
        'overview' => ['label' => __('dashboard.overview'), 'icon' => 'bi-grid-1x2-fill'],
        'debts' => ['label' => __('dashboard.debts'), 'icon' => 'bi-credit-card-2-front'],
        'transactions' => ['label' => __('dashboard.transactions'), 'icon' => 'bi-clock-history'],
        'assets' => ['label' => __('dashboard.assets'), 'icon' => 'bi-pie-chart-fill'],
        'budgets' => ['label' => __('dashboard.budgets'), 'icon' => 'bi-cash-stack'],
        'goals' => ['label' => __('dashboard.goals'), 'icon' => 'bi-trophy'],
    ]" current="{{ $currentTab }}" keyParam="tab" :preserve="['period','start_date','end_date','debt_type','debt_status','txn_type','category_id','asset_type','budget_status','goal_status','search']" />

    <div class="mb-4">
        <x-date-filter-bar
            :periods="$periods"
            :currentPeriod="$period"
            :startDate="$startDate"
            :endDate="$endDate"
            :preserve="['tab','debt_type','debt_status','txn_type','category_id','asset_type','budget_status','goal_status','search']"
        />
    </div>

    {{-- ==================== TAB: OVERVIEW ==================== --}}
    @if($currentTab === 'overview')
    <div class="row g-3 mb-4 animate-fade-in">
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <x-kpi-card
                icon="bi-cash-stack"
                iconBg="rgba(34,197,94,0.12)"
                iconColor="var(--success)"
                :label="__('dashboard.total_income').' ('.__('filters.'.$period).')'"
                :value="number_format($kpi->totalIncome, 2)"
                :currency="config('finance.currency_symbol')"
                :trendIcon="'bi-calendar3'"
                :trendDir="'up'"
                :trend="__('dashboard.all_time').': '.number_format($kpi->totalIncomeAllTime, 2)"
            />
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <x-kpi-card
                icon="bi-cart"
                iconBg="rgba(239,68,68,0.12)"
                iconColor="var(--danger)"
                :label="__('dashboard.total_expense').' ('.__('filters.'.$period).')'"
                :value="number_format($kpi->totalExpense, 2)"
                :currency="config('finance.currency_symbol')"
                :trendIcon="$kpi->expenseChange <= 0 ? 'bi-arrow-down' : 'bi-arrow-up'"
                :trendDir="$kpi->expenseChange <= 0 ? 'up' : 'down'"
                :trend="__('dashboard.all_time').': '.number_format($kpi->totalExpenseAllTime, 2)"
            />
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <x-kpi-card
                icon="bi-wallet2"
                iconBg="rgba(59,130,246,0.12)"
                iconColor="var(--info)"
                :label="__('dashboard.net_balance').' ('.  __('filters.'.$period)  .')'"
                :value="number_format($kpi->netBalance, 2)"
                :currency="config('finance.currency_symbol')"
                :valueClass="$kpi->netBalance >= 0 ? '' : 'text-danger'"
                :trendIcon="$kpi->netBalance >= 0 ? 'bi-arrow-up' : 'bi-arrow-down'"
                :trendDir="$kpi->netBalance >= 0 ? 'up' : 'down'"
                :trend="__('dashboard.total_savings').': '.number_format($kpi->totalSavings, 2)"
            />
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <x-kpi-card
                icon="bi-piggy-bank"
                iconBg="rgba(139,92,246,0.12)"
                iconColor="#8B5CF6"
                :label="__('dashboard.total_savings').' ('.__('filters.'.$period).')'"
                :value="number_format($kpi->totalSavings, 2)"
                :currency="config('finance.currency_symbol')"
                :valueClass="$kpi->totalSavings >= 0 ? '' : 'text-danger'"
                :trendIcon="'bi-clock-history'"
                :trendDir="'up'"
                :trend="__('filters.all_time')"
            />
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <x-kpi-card
                icon="bi-pie-chart-fill"
                iconBg="rgba(34,197,94,0.12)"
                iconColor="var(--success)"
                :label="__('dashboard.total_assets').' ('.__('filters.'.$period).')'"
                :value="number_format($kpi->totalAssets, 2)"
                :currency="config('finance.currency_symbol')"
                :trendDir="'up'"
                :trend="__('dashboard.all_time')"
            />
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <x-kpi-card
                icon="bi-credit-card-2-front"
                iconBg="rgba(245,158,11,0.12)"
                iconColor="var(--warning)"
                :label="__('dashboard.total_debts').' ('.__('filters.'.$period).')'"
                :value="number_format($kpi->totalDebts, 2)"
                :currency="config('finance.currency_symbol')"
                :trendDir="'down'"
                :trend="$kpi->overdueDebts.' '.__('dashboard.overdue_debts')"
            />
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-6 col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-bar-chart-fill" style="color:var(--success)"></i>
                        <span>{{ __('dashboard.income_vs_expenses') }}</span>
                    </h5>
                    <span style="font-size:12px;color:var(--text-muted)">{{ __('dashboard.monthly_summary') }}</span>
                </div>
                <div style="min-height:300px" id="incomeExpenseContainer">
                    <canvas id="incomeExpenseChart" height="280"
                        data-labels='@json($data["incomeExpense"]["labels"] ?? [])'
                        data-income='@json($data["incomeExpense"]["incomeData"] ?? [])'
                        data-expense='@json($data["incomeExpense"]["expenseData"] ?? [])'></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-pie-chart-fill" style="color:var(--danger)"></i>
                        <span>{{ __('dashboard.expense_categories') }}</span>
                    </h5>
                    <span style="font-size:12px;color:var(--text-muted)">{{ __('filters.'.$period) }}</span>
                </div>
                <div style="min-height:300px" id="expenseCategoriesContainer">
                    <canvas id="expenseCategoriesChart" height="280"
                        data-labels='@json($data["expenseCategories"]["labels"] ?? [])'
                        data-values='@json($data["expenseCategories"]["data"] ?? [])'
                        data-colors='@json($data["expenseCategories"]["colors"] ?? [])'></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up-arrow" style="color:var(--info)"></i>
                        <span>{{ __('dashboard.monthly_cash_flow') }}</span>
                    </h5>
                    <span style="font-size:12px;color:var(--text-muted)">{{ __('dashboard.all_time') }}</span>
                </div>
                <div style="min-height:300px" id="cashFlowContainer">
                    <canvas id="cashFlowChart" height="280"
                        data-labels='@json($data["incomeExpense"]["labels"] ?? [])'
                        data-income='@json($data["incomeExpense"]["incomeData"] ?? [])'
                        data-expense='@json($data["incomeExpense"]["expenseData"] ?? [])'></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-currency-exchange" style="color:var(--accent)"></i>
                        <span>{{ __('dashboard.financial_growth') }}</span>
                    </h5>
                    <span style="font-size:12px;color:var(--text-muted)">6 {{ __('general.monthly') }}</span>
                </div>
                <div style="min-height:300px" id="growthContainer">
                    <canvas id="financialGrowthChart" height="280"
                        data-labels='@json($data["growth"]["labels"] ?? [])'
                        data-values='@json($data["growth"]["data"] ?? [])'></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @if ($data['goals']->isNotEmpty())
        <div class="col-xl-4 col-12">
            <div class="dashboard-chart-card h-100">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-trophy" style="color:var(--accent)"></i>
                        <span>{{ __('dashboard.goals_progress') }}</span>
                    </h5>
                    <a href="{{ route('goal.index') }}" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none">{{ __('dashboard.view_all') }}</a>
                </div>
                <div style="padding:20px">
                    @foreach ($data['goals'] as $goal)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:13px; font-weight:500">{{ $goal->{'name_' . app()->getLocale()} }}</span>
                            <span style="font-size:12px; color:var(--text-muted)">{{ $goal->progress }}%</span>
                        </div>
                        <div class="progress" style="height:6px; border-radius:3px; background:var(--border)">
                            <div class="progress-bar" role="progressbar" style="width:{{ $goal->progress }}%; border-radius:3px; background:{{ $goal->color ?: 'var(--accent)' }}" aria-valuenow="{{ $goal->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:2px">
                            {{ number_format($goal->current_amount, 0) }} / {{ number_format($goal->target_amount, 0) }} {{ config('finance.currency_symbol') }}
                            @if ($goal->days_remaining !== null)
                                · {{ $goal->days_remaining }} {{ __('general.days_left') }}
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if ($data['budgetAlerts']->isNotEmpty())
        <div class="col-xl-4 col-12">
            <div class="dashboard-chart-card h-100">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <x-status-icon domain="general" status="warning" set="bi" />
                        <span>{{ __('dashboard.budget_alerts') }}</span>
                    </h5>
                    <a href="{{ route('budget.index') }}" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none">{{ __('dashboard.view_all') }}</a>
                </div>
                <div style="padding:20px">
                    @foreach ($data['budgetAlerts'] as $budget)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="flex-shrink-0" style="width:36px; height:36px; border-radius:8px; background:rgba(245,158,11,0.12); display:flex; align-items:center; justify-content:center; color:var(--warning)">
                            <i class="bi bi-cash"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width:0">
                            <div style="font-size:13px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $budget->{'name_' . app()->getLocale()} }}</div>
                            <div style="font-size:11px; color:var(--text-muted)">
                                {{ number_format($budget->totalSpent, 0) }} / {{ number_format($budget->total_amount, 0) }} {{ config('finance.currency_symbol') }}
                            </div>
                            <div class="progress" style="height:4px; border-radius:2px; background:var(--border); margin-top:4px">
                                <div class="progress-bar bg-warning" role="progressbar" style="width:{{ min(100, $budget->adherence_rate) }}%; border-radius:2px" aria-valuenow="{{ $budget->adherence_rate }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <span style="font-size:12px; font-weight:600; color:var(--warning)">+{{ round($budget->adherence_rate - 100) }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if ($data['debtReminders']->isNotEmpty())
        <div class="col-xl-4 col-12">
            <div class="dashboard-chart-card h-100">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-bell" style="color:var(--info)"></i>
                        <span>{{ __('dashboard.debt_reminders') }}</span>
                    </h5>
                    <a href="{{ route('debt.index') }}" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none">{{ __('dashboard.view_all') }}</a>
                </div>
                <div style="padding:20px">
                    @foreach ($data['debtReminders'] as $debt)
                    @php
                        $daysLeft = now()->startOfDay()->diffInDays($debt->due_date, false);
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="flex-shrink-0" style="width:36px; height:36px; border-radius:8px; background:{{ $daysLeft <= 0 ? 'rgba(239,68,68,0.12)' : 'rgba(59,130,246,0.12)' }}; display:flex; align-items:center; justify-content:center; color:{{ $daysLeft <= 0 ? 'var(--danger)' : 'var(--info)' }}">
                            <i class="bi bi-{{ $debt->type === \App\Enums\DebtType::Owed ? 'person-up' : 'person-down' }}"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width:0">
                            <div style="font-size:13px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $debt->counterparty_name }}</div>
                            <div style="font-size:11px; color:var(--text-muted)">
                                {{ number_format($debt->remaining_amount, 2) }} {{ config('finance.currency_symbol') }}
                                · {{ $debt->type === \App\Enums\DebtType::Owed ? __('general.owed_to_you') : __('general.you_owe') }}
                            </div>
                        </div>
                        <span style="font-size:12px; font-weight:500; white-space:nowrap; color:{{ $daysLeft <= 0 ? 'var(--danger)' : ($daysLeft <= 7 ? 'var(--warning)' : 'var(--text-muted)') }}">
                            @if ($daysLeft <= 0)
                                {{ abs($daysLeft) }}d overdue
                            @else
                                {{ $daysLeft }}d
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @php
            $zakatUser = auth()->user();
            $zakatHaulStarted = $zakatUser->hasZakatHaulStarted();
            $zakatDue = $zakatUser->isZakatDue();
            $zakatDaysLeft = $zakatUser->daysUntilNextZakat();
            $zakatNextDate = $zakatUser->nextZakatDate();
            $zakatNextDateDisplay = $zakatNextDate
                ? ($zakatUser->calendar_type === 'hijri'
                    ? \App\Services\HijriDateService::formatHijriDate(
                        \App\Services\HijriDateService::gregorianToHijri($zakatNextDate),
                        app()->getLocale()
                    )
                    : $zakatNextDate->format('Y/m/d'))
                : null;
        @endphp
        @if($zakatHaulStarted)
        <div class="col-xl-4 col-12">
            <div class="dashboard-chart-card h-100">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-heart-fill" style="color:{{ $zakatDue ? 'var(--success)' : '#6366F1' }}"></i>
                        <span>{{ __('zakat.zakat_haul') }}</span>
                    </h5>
                    <a href="{{ route('zakat.calculator') }}" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none">{{ __('zakat.calculate') }}</a>
                </div>
                <div style="padding:20px; text-align:center">
                    @if($zakatDue)
                        <div style="font-size:36px; font-weight:700; color:var(--success); line-height:1">✓</div>
                        <div style="font-size:14px; font-weight:600; color:var(--success); margin-top:8px">{{ __('zakat.haul_complete') }}</div>
                        <div style="font-size:12px; color:var(--text-muted); margin-top:4px">{{ __('zakat.calculate_first') }}</div>
                    @else
                        <div style="font-size:36px; font-weight:700; color:#6366F1; line-height:1">{{ $zakatDaysLeft }}</div>
                        <div style="font-size:14px; font-weight:600; color:var(--text); margin-top:8px">{{ __('dashboard.days_until_zakat', ['days' => $zakatDaysLeft]) }}</div>
                        <div style="font-size:12px; color:var(--text-muted); margin-top:4px">{{ __('zakat.next_zakat_date') }}: {{ $zakatNextDateDisplay }}</div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history" style="color:var(--accent)"></i>
                        <span>{{ __('dashboard.recent_transactions') }}</span>
                    </h5>
                    <a href="{{ route('transactions.index') }}" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none">{{ __('dashboard.view_all') }}</a>
                </div>
                <div style="padding:0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <thead>
                            <tr>
                                <th>{{ __('general.date') }}</th>
                                <th>{{ __('general.description') }}</th>
                                <th>{{ __('general.category') }}</th>
                                <th class="text-end">{{ __('general.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['recentTransactions'] as $txn)
                                <tr>
                                    <td style="white-space:nowrap">{{ $txn['date']->format('Y/m/d') }}</td>
                                    <td>{{ $txn['description'] ?: '—' }}</td>
                                    <td>{{ $txn['category'] }}</td>
                                    <td class="text-start fw-bold" style="color:{{ $txn['type'] === 'income' ? 'var(--success)' : 'var(--danger)' }}">
                                        {{ $txn['type'] === 'income' ? '+' : '-' }}{{ number_format($txn['amount'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <x-empty-state icon="bi bi-inbox" :title="__('general.no_data')" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    var chartBaseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                align: 'end',
                labels: { padding: 16, usePointStyle: true, pointStyle: 'circle', font: { size: 12, weight: '500' }, color: '#94A3B8' }
            },
            tooltip: {
                backgroundColor: 'rgba(15,23,42,0.94)',
                titleColor: '#F8FAFC',
                bodyColor: '#CBD5E1',
                padding: 12,
                cornerRadius: 10,
                titleFont: { size: 13, weight: '600' },
                bodyFont: { size: 12 },
                boxPadding: 6,
                usePointStyle: true,
                callbacks: {
                    label: function(ctx) { return ctx.dataset.label + ': ' + formatCurrency(ctx.parsed.y); }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(148,163,184,0.08)', drawBorder: false },
                ticks: {
                    font: { size: 11 },
                    color: '#64748B',
                    padding: 8,
                    callback: function(v) {
                        if (v >= 1e6) return (v / 1e6).toFixed(1) + 'M';
                        if (v >= 1e3) return (v / 1e3).toFixed(0) + 'K';
                        return v.toLocaleString();
                    }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 }, color: '#64748B', maxRotation: 30 }
            }
        },
        interaction: { intersect: false, mode: 'index' }
    };

    function getChartData(id) {
        var el = document.getElementById(id);
        if (!el) return null;
        var data = {};
        ['labels', 'income', 'expense', 'values', 'colors'].forEach(function(key) {
            if (el.dataset[key]) {
                try { data[key] = JSON.parse(el.dataset[key]); } catch(e) { data[key] = []; }
            }
        });
        return data;
    }

    function initDashboardCharts() {
        if (!document.getElementById('incomeExpenseChart')) return;
        try {
            destroyExistingCharts();

            var ieData = getChartData('incomeExpenseChart');
            var incomeExpenseLabels = ieData.labels || [];
            var incomeData = ieData.income || [];
            var expenseData = ieData.expense || [];

            var expCatData = getChartData('expenseCategoriesChart');
            var expenseCatLabels = expCatData.labels || [];
            var expenseCatValues = expCatData.values || [];
            var expenseCatColors = expCatData.colors || [];

            var growthChartData = getChartData('financialGrowthChart');
            var growthLabels = growthChartData.labels || [];
            var growthValues = growthChartData.values || [];

            var cssVar = function(name) { return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || '#64748B'; };
            var success = cssVar('--success') || '#22C55E';
            var danger = cssVar('--danger') || '#EF4444';

            if (incomeExpenseLabels.length) {
                new Chart(document.getElementById('incomeExpenseChart'), {
                    type: 'bar',
                    data: {
                        labels: incomeExpenseLabels,
                        datasets: [{
                            label: '{{ __("dashboard.total_income") }}',
                            data: incomeData,
                            backgroundColor: success + 'E6',
                            borderColor: success,
                            borderWidth: { top: 0, right: 0, bottom: 0, left: 0 },
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.5,
                            categoryPercentage: 0.7
                        }, {
                            label: '{{ __("dashboard.total_expense") }}',
                            data: expenseData,
                            backgroundColor: danger + 'E6',
                            borderColor: danger,
                            borderWidth: { top: 0, right: 0, bottom: 0, left: 0 },
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.5,
                            categoryPercentage: 0.7
                        }]
                    },
                    options: {...chartBaseOptions}
                });
            } else {
                showEmptyChart('incomeExpenseContainer', '{{ __("dashboard.no_chart_data") }}');
            }

            if (expenseCatLabels.length) {
                new Chart(document.getElementById('expenseCategoriesChart'), {
                    type: 'doughnut',
                    data: {
                        labels: expenseCatLabels,
                        datasets: [{
                            data: expenseCatValues,
                            backgroundColor: expenseCatColors,
                            borderWidth: 2,
                            borderColor: 'var(--card-bg)',
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        ...chartBaseOptions,
                        cutout: '72%',
                        plugins: {
                            ...chartBaseOptions.plugins,
                            legend: { ...chartBaseOptions.plugins.legend, position: 'bottom' }
                        }
                    }
                });
            } else {
                showEmptyChart('expenseCategoriesContainer', '{{ __("dashboard.no_chart_data") }}');
            }

            var cfData = getChartData('cashFlowChart');
            if ((cfData.labels || []).length) {
                new Chart(document.getElementById('cashFlowChart'), {
                    type: 'line',
                    data: {
                        labels: cfData.labels || incomeExpenseLabels,
                        datasets: [{
                            label: '{{ __("dashboard.total_income") }}',
                            data: cfData.income || incomeData,
                            borderColor: success,
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: success,
                            pointHoverBorderWidth: 3,
                            borderWidth: 2.5,
                            borderCapStyle: 'round',
                            borderJoinStyle: 'round'
                        }, {
                            label: '{{ __("dashboard.total_expense") }}',
                            data: cfData.expense || expenseData,
                            borderColor: danger,
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: danger,
                            pointHoverBorderWidth: 3,
                            borderWidth: 2.5,
                            borderCapStyle: 'round',
                            borderJoinStyle: 'round'
                        }]
                    },
                    options: {...chartBaseOptions}
                });
            } else {
                showEmptyChart('cashFlowContainer', '{{ __("dashboard.no_chart_data") }}');
            }

            if (growthLabels.length) {
                var lastVal = growthValues[growthValues.length - 1];
                var growthColor = parseFloat(lastVal) >= 0 ? success : danger;

                new Chart(document.getElementById('financialGrowthChart'), {
                    type: 'line',
                    data: {
                        labels: growthLabels,
                        datasets: [{
                            label: '{{ __("dashboard.net_balance") }}',
                            data: growthValues,
                            borderColor: growthColor,
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: growthColor,
                            pointHoverBorderWidth: 3,
                            borderWidth: 3,
                            borderCapStyle: 'round',
                            borderJoinStyle: 'round'
                        }]
                    },
                    options: {
                        ...chartBaseOptions,
                        scales: {
                            ...chartBaseOptions.scales,
                            y: { ...chartBaseOptions.scales.y, beginAtZero: false }
                        }
                    }
                });
            } else {
                showEmptyChart('growthContainer', '{{ __("dashboard.no_chart_data") }}');
            }
        } catch (e) {
            console.warn('Chart init error:', e);
        }
    }

    if (!window._dashboardNavListener) {
        document.addEventListener('livewire:navigated', initDashboardCharts);
        window._dashboardNavListener = true;
    }
    document.addEventListener('DOMContentLoaded', function() {
        initDashboardCharts();
    });
    </script>

    {{-- ==================== TAB: DEBTS ==================== --}}
    @elseif($currentTab === 'debts')
    <div class="row g-3 mb-4 animate-fade-in">
        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-person-up"
                iconBg="rgba(34,197,94,0.12)"
                iconColor="var(--success)"
                :label="__('dashboard.owed_to_you')"
                :value="number_format($data['totalOwed'], 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-person-down"
                iconBg="rgba(239,68,68,0.12)"
                iconColor="var(--danger)"
                :label="__('dashboard.you_owe')"
                :value="number_format($data['totalOwing'], 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-exclamation-triangle"
                iconBg="rgba(245,158,11,0.12)"
                iconColor="var(--warning)"
                :label="__('dashboard.overdue_debts')"
                :value="$data['overdueCount']"
                :trendIcon="'bi-clock-history'"
                :trendDir="'down'"
                :trend="$data['activeCount'].' '.__('dashboard.active_debts')"
            />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-check-circle"
                iconBg="rgba(139,92,246,0.12)"
                iconColor="#8B5CF6"
                :label="__('dashboard.collection_rate')"
                :value="$data['collectionRate'].'%'"
                :trendIcon="'bi-cash'"
                :trendDir="'up'"
                :trend="__('dashboard.total_paid').': '.number_format($data['totalPaidAll'], 2)"
            />
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="debts">
            <input type="hidden" name="period" value="{{ $period }}">
            <select name="debt_type" class="form-control" style="width:auto;min-width:150px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value="">{{ __('dashboard.all_debts') }}</option>
                <option value="owed" {{ request('debt_type') === 'owed' ? 'selected' : '' }}>{{ __('dashboard.owed_to_you') }}</option>
                <option value="owing" {{ request('debt_type') === 'owing' ? 'selected' : '' }}>{{ __('dashboard.you_owe') }}</option>
            </select>
            <select name="debt_status" class="form-control" style="width:auto;min-width:150px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value="">{{ __('dashboard.debt_status') }}</option>
                <option value="active" {{ request('debt_status') === 'active' ? 'selected' : '' }}>{{ __('dashboard.active_debts') }}</option>
                <option value="partial" {{ request('debt_status') === 'partial' ? 'selected' : '' }}>{{ __('dashboard.partial_debts') }}</option>
                <option value="paid" {{ request('debt_status') === 'paid' ? 'selected' : '' }}>{{ __('dashboard.paid_debts') }}</option>
                <option value="overdue" {{ request('debt_status') === 'overdue' ? 'selected' : '' }}>{{ __('dashboard.overdue_debts_status') }}</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('general.search') }}" class="form-control" style="width:auto;min-width:180px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-credit-card-2-front" style="color:var(--warning)"></i>
                        <span>{{ __('dashboard.debts') }}</span>
                    </h5>
                    <a href="{{ route('debt.index') }}" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none">{{ __('dashboard.view_all') }}</a>
                </div>
                <div style="padding:0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <thead>
                            <tr>
                                <th>{{ __('dashboard.counterparty') }}</th>
                                <th>{{ __('debt.type') }}</th>
                                <th>{{ __('debt.total_amount') }}</th>
                                <th>{{ __('debt.paid_amount') }}</th>
                                <th>{{ __('dashboard.remaining') }}</th>
                                <th>{{ __('dashboard.progress') }}</th>
                                <th>{{ __('dashboard.due_date') }}</th>
                                <th>{{ __('general.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['debts'] as $debt)
                                <tr>
                                    <td style="font-weight:500">{{ $debt->counterparty_name }}</td>
                                    <td>
                                        <span class="badge" style="font-size:10px;background:{{ $debt->type->value === 'owed' ? 'rgba(34,197,94,0.12)' : 'rgba(239,68,68,0.12)' }};color:{{ $debt->type->value === 'owed' ? 'var(--success)' : 'var(--danger)' }};padding:3px 8px;border-radius:4px">
                                            {{ $debt->type->value === 'owed' ? __('general.owed_to_you') : __('general.you_owe') }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($debt->total_amount, 2) }} {{ config('finance.currency_symbol') }}</td>
                                    <td>{{ number_format($debt->paid_amount, 2) }} {{ config('finance.currency_symbol') }}</td>
                                    <td style="font-weight:600;color:var(--danger)">{{ number_format($debt->remaining_amount, 2) }} {{ config('finance.currency_symbol') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:6px;border-radius:3px;background:var(--border);min-width:60px">
                                                <div class="progress-bar" role="progressbar" style="width:{{ $debt->progress }}%;border-radius:3px;background:{{ $debt->status->color() }}"></div>
                                            </div>
                                            <span style="font-size:11px;color:var(--text-muted)">{{ $debt->progress }}%</span>
                                        </div>
                                    </td>
                                    <td style="white-space:nowrap">{{ $debt->due_date ? $debt->due_date->format('Y/m/d') : '—' }}</td>
                                    <td>
                                        <span class="badge" style="font-size:10px;background:{{ $debt->status->color() }}20;color:{{ $debt->status->color() }};padding:3px 8px;border-radius:4px">
                                            {{ $debt->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <x-empty-state icon="bi bi-inbox" :title="__('debt.no_debts')" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                    @if($data['debts']->hasPages())
                    <div class="d-flex justify-content-center py-3">
                        {{ $data['debts']->withQueryString() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB: TRANSACTIONS ==================== --}}
    @elseif($currentTab === 'transactions')
    <div class="row g-3 mb-4 animate-fade-in">
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-list-check"
                iconBg="rgba(59,130,246,0.12)"
                iconColor="var(--info)"
                :label="__('dashboard.total_count')"
                :value="$data['totalCount']"
            />
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-cash-stack"
                iconBg="rgba(34,197,94,0.12)"
                iconColor="var(--success)"
                :label="__('dashboard.total_income')"
                :value="number_format($data['totalIncome'], 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-cart"
                iconBg="rgba(239,68,68,0.12)"
                iconColor="var(--danger)"
                :label="__('dashboard.total_expense')"
                :value="number_format($data['totalExpense'], 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="transactions">
            <input type="hidden" name="period" value="{{ $period }}">
            <select name="txn_type" class="form-control" style="width:auto;min-width:150px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value="">{{ __('dashboard.all_transactions') }}</option>
                <option value="income" {{ request('txn_type') === 'income' ? 'selected' : '' }}>{{ __('dashboard.income_only') }}</option>
                <option value="expense" {{ request('txn_type') === 'expense' ? 'selected' : '' }}>{{ __('dashboard.expense_only') }}</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('general.search') }}" class="form-control" style="width:auto;min-width:180px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history" style="color:var(--accent)"></i>
                        <span>{{ __('dashboard.transactions') }}</span>
                    </h5>
                </div>
                <div style="padding:0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <thead>
                            <tr>
                                <th>{{ __('general.date') }}</th>
                                <th>{{ __('general.description') }}</th>
                                <th>{{ __('general.category') }}</th>
                                <th>{{ __('general.type') }}</th>
                                <th class="text-end">{{ __('general.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['transactions'] as $txn)
                                <tr>
                                    <td style="white-space:nowrap">{{ $txn['date']->format('Y/m/d') }}</td>
                                    <td>{{ $txn['description'] ?: '—' }}</td>
                                    <td>{{ $txn['category'] }}</td>
                                    <td>
                                        <span class="badge" style="font-size:10px;background:{{ $txn['type'] === 'income' ? 'rgba(34,197,94,0.12)' : 'rgba(239,68,68,0.12)' }};color:{{ $txn['type'] === 'income' ? 'var(--success)' : 'var(--danger)' }};padding:3px 8px;border-radius:4px">
                                            {{ $txn['type'] === 'income' ? __('general.income') : __('general.expense') }}
                                        </span>
                                    </td>
                                    <td class="text-start fw-bold" style="color:{{ $txn['type'] === 'income' ? 'var(--success)' : 'var(--danger)' }}">
                                        {{ $txn['type'] === 'income' ? '+' : '-' }}{{ number_format($txn['amount'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <x-empty-state icon="bi bi-inbox" :title="__('general.no_data')" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                    @if($data['transactions']->hasPages())
                    <div class="d-flex justify-content-center py-3">
                        {{ $data['transactions']->withQueryString() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB: ASSETS ==================== --}}
    @elseif($currentTab === 'assets')
    <div class="row g-3 mb-4 animate-fade-in">
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-pie-chart-fill"
                iconBg="rgba(34,197,94,0.12)"
                iconColor="var(--success)"
                :label="__('dashboard.total_assets')"
                :value="number_format($data['totalAssets'], 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-cash"
                iconBg="rgba(59,130,246,0.12)"
                iconColor="var(--info)"
                :label="__('dashboard.liquid_assets')"
                :value="number_format($data['liquidAssets'], 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-house"
                iconBg="rgba(245,158,11,0.12)"
                iconColor="var(--warning)"
                :label="__('dashboard.non_liquid_assets')"
                :value="number_format($data['nonLiquid'], 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="assets">
            <select name="asset_type" class="form-control" style="width:auto;min-width:150px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value="">{{ __('dashboard.all_assets') }}</option>
                @foreach(\App\Enums\AssetType::cases() as $type)
                    <option value="{{ $type->value }}" {{ request('asset_type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        @if($data['byType']->isNotEmpty())
        <div class="col-lg-5">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-pie-chart-fill"></i><span>{{ __('dashboard.by_type') }}</span></h5>
                </div>
                <div class="section-card-body">
                    @php $grandTotal = max($data['byType']->sum('total_value'), 1); @endphp
                    @foreach($data['byType'] as $item)
                        @php $assetType = $item->type instanceof \App\Enums\AssetType ? $item->type : \App\Enums\AssetType::from($item->type); @endphp
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:32px;height:32px;border-radius:8px;background:{{ $assetType->color() }}15;display:flex;align-items:center;justify-content:center;font-size:14px;color:{{ $assetType->color() }};flex-shrink:0">
                                <i class="bi {{ $assetType->icon() }}"></i>
                            </div>
                            <div style="flex:1;min-width:0">
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="font-size:13px;font-weight:500;color:var(--text)">{{ $assetType->label() }}</span>
                                    <span style="font-size:12px;font-weight:600;color:var(--text-muted)">{{ number_format($item->total_value, 2) }} {{ config('finance.currency_symbol') }}</span>
                                </div>
                                <div style="height:6px;background:var(--border);border-radius:3px;overflow:hidden">
                                    <div style="height:100%;width:{{ $item->total_value / $grandTotal * 100 }}%;background:{{ $assetType->color() }};border-radius:3px"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="{{ $data['byType']->isNotEmpty() ? 'col-lg-7' : 'col-12' }}">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-box-seam"></i><span>{{ __('asset.portfolio') }}</span></h5>
                </div>
                <div class="section-card-body p-0">
                    <div class="table-responsive">
                        <table class="table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('asset.name') }}</th>
                                    <th>{{ __('asset.type') }}</th>
                                    <th>{{ __('asset.quantity') }}</th>
                                    <th>{{ __('asset.total_value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['assets'] as $asset)
                                    @php $aType = $asset->type instanceof \App\Enums\AssetType ? $asset->type : \App\Enums\AssetType::from($asset->type); @endphp
                                    <tr>
                                        <td style="font-weight:500">{{ $asset->name }}</td>
                                        <td>
                                            <span class="badge" style="font-size:10px;background:{{ $aType->color() }}15;color:{{ $aType->color() }};padding:3px 8px;border-radius:4px">
                                                {{ $aType->label() }}
                                            </span>
                                        </td>
                                        <td>{{ $asset->quantity }}</td>
                                        <td style="font-weight:600">{{ number_format($asset->total_value, 2) }} {{ config('finance.currency_symbol') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <x-empty-state icon="bi bi-inbox" :title="__('asset.no_assets')" />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($data['assets']->hasPages())
                    <div class="d-flex justify-content-center py-3">
                        {{ $data['assets']->withQueryString() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB: BUDGETS ==================== --}}
    @elseif($currentTab === 'budgets')
    <div class="row g-3 mb-4 animate-fade-in">
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-cash-stack"
                iconBg="rgba(59,130,246,0.12)"
                iconColor="var(--info)"
                :label="__('dashboard.active_budgets')"
                :value="$data['activeCount']"
            />
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-exclamation-triangle"
                iconBg="rgba(239,68,68,0.12)"
                iconColor="var(--danger)"
                :label="__('dashboard.exceeded_budgets')"
                :value="$data['exceededCount']"
            />
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-bar-chart"
                iconBg="rgba(34,197,94,0.12)"
                iconColor="var(--success)"
                :label="__('dashboard.total_count')"
                :value="number_format($data['totalSpent'], 2).' / '.number_format($data['totalAllocated'], 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="budgets">
            <select name="budget_status" class="form-control" style="width:auto;min-width:150px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value="">{{ __('dashboard.all_budgets') }}</option>
                <option value="active" {{ request('budget_status') === 'active' ? 'selected' : '' }}>{{ __('dashboard.active_budgets') }}</option>
                <option value="exceeded" {{ request('budget_status') === 'exceeded' ? 'selected' : '' }}>{{ __('dashboard.exceeded_budgets') }}</option>
                <option value="inactive" {{ request('budget_status') === 'inactive' ? 'selected' : '' }}>{{ __('dashboard.inactive_budgets') }}</option>
            </select>
            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-cash-stack" style="color:var(--info)"></i>
                        <span>{{ __('dashboard.budgets') }}</span>
                    </h5>
                    <a href="{{ route('budget.index') }}" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none">{{ __('dashboard.view_all') }}</a>
                </div>
                <div style="padding:0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <thead>
                            <tr>
                                <th>{{ __('general.name') }}</th>
                                <th>{{ __('dashboard.allocated') }}</th>
                                <th>{{ __('dashboard.spent') }}</th>
                                <th>{{ __('dashboard.progress') }}</th>
                                <th>{{ __('general.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['budgets'] as $budget)
                                <tr>
                                    <td style="font-weight:500">{{ $budget->{'name_' . app()->getLocale()} }}</td>
                                    <td>{{ number_format($budget->total_amount, 2) }} {{ config('finance.currency_symbol') }}</td>
                                    <td>{{ number_format($budget->totalSpent, 2) }} {{ config('finance.currency_symbol') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:6px;border-radius:3px;background:var(--border);min-width:60px">
                                                <div class="progress-bar {{ $budget->is_exceeded ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width:{{ min(100, $budget->adherence_rate) }}%;border-radius:3px"></div>
                                            </div>
                                            <span style="font-size:11px;color:var(--text-muted)">{{ $budget->adherence_rate }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($budget->is_exceeded)
                                            <span class="badge" style="font-size:10px;background:rgba(239,68,68,0.12);color:var(--danger);padding:3px 8px;border-radius:4px">{{ __('dashboard.exceeded_budgets') }}</span>
                                        @else
                                            <span class="badge" style="font-size:10px;background:rgba(34,197,94,0.12);color:var(--success);padding:3px 8px;border-radius:4px">{{ __('general.active') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <x-empty-state icon="bi bi-inbox" :title="__('general.no_data')" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                    @if($data['budgets']->hasPages())
                    <div class="d-flex justify-content-center py-3">
                        {{ $data['budgets']->withQueryString() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB: GOALS ==================== --}}
    @elseif($currentTab === 'goals')
    <div class="row g-3 mb-4 animate-fade-in">
        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-trophy"
                iconBg="rgba(59,130,246,0.12)"
                iconColor="var(--info)"
                :label="__('dashboard.active_goals')"
                :value="$data['activeGoals']"
            />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-check-circle"
                iconBg="rgba(34,197,94,0.12)"
                iconColor="var(--success)"
                :label="__('dashboard.completed_goals')"
                :value="$data['completedGoals']"
            />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-bullseye"
                iconBg="rgba(139,92,246,0.12)"
                iconColor="#8B5CF6"
                :label="__('dashboard.target')"
                :value="number_format($data['totalTarget'], 2)"
                :currency="config('finance.currency_symbol')"
            />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <x-kpi-card
                icon="bi-graph-up-arrow"
                iconBg="rgba(245,158,11,0.12)"
                iconColor="var(--warning)"
                :label="__('dashboard.avg_progress')"
                :value="$data['avgProgress'].'%'"
            />
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="goals">
            <select name="goal_status" class="form-control" style="width:auto;min-width:150px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value="">{{ __('dashboard.all_goals') }}</option>
                <option value="in_progress" {{ request('goal_status') === 'in_progress' ? 'selected' : '' }}>{{ __('dashboard.active_goals') }}</option>
                <option value="completed" {{ request('goal_status') === 'completed' ? 'selected' : '' }}>{{ __('dashboard.completed_goals') }}</option>
                <option value="cancelled" {{ request('goal_status') === 'cancelled' ? 'selected' : '' }}>{{ __('dashboard.cancelled_goals') }}</option>
            </select>
            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-trophy" style="color:var(--accent)"></i>
                        <span>{{ __('dashboard.goals') }}</span>
                    </h5>
                    <a href="{{ route('goal.index') }}" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none">{{ __('dashboard.view_all') }}</a>
                </div>
                <div style="padding:0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <thead>
                            <tr>
                                <th>{{ __('general.name') }}</th>
                                <th>{{ __('dashboard.target') }}</th>
                                <th>{{ __('dashboard.current') }}</th>
                                <th>{{ __('dashboard.progress') }}</th>
                                <th>{{ __('general.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['goals'] as $goal)
                                <tr>
                                    <td style="font-weight:500">{{ $goal->{'name_' . app()->getLocale()} }}</td>
                                    <td>{{ number_format($goal->target_amount, 2) }} {{ config('finance.currency_symbol') }}</td>
                                    <td>{{ number_format($goal->current_amount, 2) }} {{ config('finance.currency_symbol') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:6px;border-radius:3px;background:var(--border);min-width:60px">
                                                <div class="progress-bar" role="progressbar" style="width:{{ $goal->progress }}%;border-radius:3px;background:{{ $goal->color ?: 'var(--accent)' }}"></div>
                                            </div>
                                            <span style="font-size:11px;color:var(--text-muted)">{{ $goal->progress }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($goal->status->value === 'completed')
                                            <span class="badge" style="font-size:10px;background:rgba(34,197,94,0.12);color:var(--success);padding:3px 8px;border-radius:4px">{{ __('dashboard.completed_goals') }}</span>
                                        @elseif($goal->status->value === 'cancelled')
                                            <span class="badge" style="font-size:10px;background:rgba(239,68,68,0.12);color:var(--danger);padding:3px 8px;border-radius:4px">{{ __('dashboard.cancelled_goals') }}</span>
                                        @else
                                            <span class="badge" style="font-size:10px;background:rgba(59,130,246,0.12);color:var(--info);padding:3px 8px;border-radius:4px">{{ __('dashboard.active_goals') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <x-empty-state icon="bi bi-inbox" :title="__('general.no_data')" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                    @if($data['goals']->hasPages())
                    <div class="d-flex justify-content-center py-3">
                        {{ $data['goals']->withQueryString() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
