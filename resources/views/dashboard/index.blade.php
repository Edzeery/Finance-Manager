<x-app-layout>
    <x-slot:title>{{ __('dashboard.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('dashboard.title') }}</x-slot>
    <x-slot:page-description>{{ __("filters.{$period}") }}: <strong>{{ config('finance.currency_symbol') }} {{ number_format($kpi->netBalance, 2) }}</strong></x-slot>

    <x-date-filter-bar
        :periods="$periods"
        :currentPeriod="$period"
        :startDate="$startDate"
        :endDate="$endDate"
        :preserve="[]"
    />

    <div class="row g-3 mb-4 animate-fade-in">
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(34,197,94,0.12); color: var(--success)">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="kpi-label">
                    @if ($period === 'all_time')
                        {{ __('dashboard.total_income') }}
                    @elseif ($period === 'this_month')
                        {{ __('dashboard.all_time') }} — {{ __('dashboard.total_income') }}
                    @else
                        {{ __('filters.filtered_by') }}: {{ __("filters.{$period}") }}
                    @endif
                </div>
                <div class="kpi-value">{{ number_format($kpi->totalIncome, 2) }} {{ config('finance.currency_symbol') }}</div>
                <div class="kpi-trend up">
                    <i class="bi bi-calendar3"></i>
                    {{ __('dashboard.all_time') }}: {{ number_format($kpi->totalIncomeAllTime, 2) }}
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(239,68,68,0.12); color: var(--danger)">
                    <i class="bi bi-cart"></i>
                </div>
                <div class="kpi-label">{{ __('dashboard.total_expense') }}</div>
                <div class="kpi-value">{{ number_format($kpi->totalExpense, 2) }} {{ config('finance.currency_symbol') }}</div>
                <div class="kpi-trend {{ $kpi->expenseChange <= 0 ? 'up' : 'down' }}">
                    <i class="bi {{ $kpi->expenseChange <= 0 ? 'bi-arrow-down' : 'bi-arrow-up' }}"></i>
                    {{ __('dashboard.all_time') }}: {{ number_format($kpi->totalExpenseAllTime, 2) }}
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(59,130,246,0.12); color: var(--info)">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="kpi-label">{{ __('dashboard.net_balance') }} ({{ __("filters.{$period}") }})</div>
                <div class="kpi-value {{ $kpi->netBalance >= 0 ? '' : 'text-danger' }}">{{ number_format($kpi->netBalance, 2) }} {{ config('finance.currency_symbol') }}</div>
                <div class="kpi-trend {{ $kpi->netBalance >= 0 ? 'up' : 'down' }}">
                    <i class="bi {{ $kpi->netBalance >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                    {{ __('dashboard.total_savings') }}: {{ number_format($kpi->totalSavings, 2) }}
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(139,92,246,0.12); color:#8B5CF6">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <div class="kpi-label">{{ __('dashboard.total_savings') }} ({{ __('dashboard.all_time') }})</div>
                <div class="kpi-value {{ $kpi->totalSavings >= 0 ? '' : 'text-danger' }}">
                    {{ number_format($kpi->totalSavings, 2) }} {{ config('finance.currency_symbol') }}
                </div>
                <div class="kpi-trend up">
                    <i class="bi bi-clock-history"></i>
                    {{ __('filters.all_time') }}
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(34,197,94,0.12); color: var(--success)">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
                <div class="kpi-label">{{ __('dashboard.total_assets') }} ({{ __('dashboard.all_time') }})</div>
                <div class="kpi-value">{{ number_format($kpi->totalAssets, 2) }} {{ config('finance.currency_symbol') }}</div>
                <div class="kpi-trend up">{{ __('dashboard.all_time') }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(245,158,11,0.12); color: var(--warning)">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
                <div class="kpi-label">{{ __('dashboard.total_debts') }} ({{ __('dashboard.all_time') }})</div>
                <div class="kpi-value">{{ number_format($kpi->totalDebts, 2) }} {{ config('finance.currency_symbol') }}</div>
                <div class="kpi-trend down">
                    <x-status-icon domain="general" status="failed" set="bi" />
                    {{ $kpi->overdueDebts }} {{ __('dashboard.overdue_debts') }}
                </div>
            </div>
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
                        data-labels='@json($incomeExpense['labels'] ?? [])'
                        data-income='@json($incomeExpense['incomeData'] ?? [])'
                        data-expense='@json($incomeExpense['expenseData'] ?? [])'></canvas>
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
                    <span style="font-size:12px;color:var(--text-muted)">{{ __('dashboard.this_month') }}</span>
                </div>
                <div style="min-height:300px" id="expenseCategoriesContainer">
                    <canvas id="expenseCategoriesChart" height="280"
                        data-labels='@json($expenseCategories['labels'] ?? [])'
                        data-values='@json($expenseCategories['data'] ?? [])'
                        data-colors='@json($expenseCategories['colors'] ?? [])'></canvas>
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
                        data-labels='@json($incomeExpense['labels'] ?? [])'
                        data-income='@json($incomeExpense['incomeData'] ?? [])'
                        data-expense='@json($incomeExpense['expenseData'] ?? [])'></canvas>
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
                        data-labels='@json($growth['labels'] ?? [])'
                        data-values='@json($growth['data'] ?? [])'></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @if ($goals->isNotEmpty())
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
                    @foreach ($goals as $goal)
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

        @if ($budgetAlerts->isNotEmpty())
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
                    @foreach ($budgetAlerts as $budget)
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

        @if ($debtReminders->isNotEmpty())
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
                    @foreach ($debtReminders as $debt)
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
                                · {{ $debt->type === \App\Enums\DebtType::Owed ? __('general.you_owe') : __('general.owed_to_you') }}
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
                            @forelse($recentTransactions as $txn)
                                <tr>
                                    <td style="white-space:nowrap">{{ $txn['date']->format('Y/m/d') }}</td>
                                    <td>{{ $txn['description'] ?: '—' }}</td>
                                    <td>{{ $txn['category'] }}</td>
                                    <td text-start fw-bold style="color:{{ $txn['type'] === 'income' ? 'var(--success)' : 'var(--danger)' }}">
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
    initDashboardCharts();
    </script>
</x-app-layout>
