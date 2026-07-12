<x-app-layout>
    <x-slot:title>{{ __('report.yearly') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('report.yearly') }}</x-slot>

    <form method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-auto">
            <label class="form-label-custom">{{ __('report.select_year') }}</label>
            <select name="year" class="form-select form-custom">
                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-accent btn-custom">
                <i class="bi bi-search me-1"></i>{{ __('general.filter') }}
            </button>
        </div>
    </form>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:rgba(34,197,94,0.12); color:var(--success)">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="kpi-label">{{ __('report.total_income') }}</div>
                <div class="kpi-value">{{ Number::currency($report['totalIncome'], config('finance.currency_symbol')) }}</div>
                <div class="kpi-sub">{{ $report['incomeCount'] }} {{ __('report.transactions') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:rgba(239,68,68,0.12); color:var(--danger)">
                    <i class="bi bi-cart"></i>
                </div>
                <div class="kpi-label">{{ __('report.total_expense') }}</div>
                <div class="kpi-value">{{ Number::currency($report['totalExpense'], config('finance.currency_symbol')) }}</div>
                <div class="kpi-sub">{{ $report['expenseCount'] }} {{ __('report.transactions') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:rgba(59,130,246,0.12); color:var(--info)">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <div class="kpi-label">{{ __('report.net_savings') }}</div>
                <div class="kpi-value {{ $report['netSavings'] >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ Number::currency($report['netSavings'], config('finance.currency_symbol')) }}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:rgba(139,92,246,0.12); color:#8B5CF6">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="kpi-label">{{ __('report.monthly_trend') }}</div>
                <div class="kpi-value">{{ $report['monthlyIncome']->count() }}/12</div>
            </div>
        </div>
    </div>

    <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-bar-chart-line" style="color:var(--info)"></i>
                        <span>{{ __('report.monthly_trend') }}</span>
                    </h5>
                </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('general.month') }}</th>
                            <th class="text-end">{{ __('report.monthly_income') }}</th>
                            <th class="text-end">{{ __('report.monthly_expense') }}</th>
                            <th class="text-end">{{ __('report.net_savings') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (range(1, 12) as $m)
                            @php
                                $mIncome = $report['monthlyIncome']->get(str_pad($m, 2, '0', STR_PAD_LEFT), 0);
                                $mExpense = $report['monthlyExpense']->get(str_pad($m, 2, '0', STR_PAD_LEFT), 0);
                                $mNet = $mIncome - $mExpense;
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::createFromDate($year, $m, 1)->translatedFormat('F') }}</td>
                                <td class="text-end text-success">{{ Number::currency($mIncome, config('finance.currency_symbol')) }}</td>
                                <td class="text-end text-danger">{{ Number::currency($mExpense, config('finance.currency_symbol')) }}</td>
                                <td class="text-end {{ $mNet >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ Number::currency($mNet, config('finance.currency_symbol')) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>{{ __('general.total') }}</td>
                            <td class="text-end">{{ Number::currency($report['totalIncome'], config('finance.currency_symbol')) }}</td>
                            <td class="text-end">{{ Number::currency($report['totalExpense'], config('finance.currency_symbol')) }}</td>
                            <td class="text-end {{ $report['netSavings'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ Number::currency($report['netSavings'], config('finance.currency_symbol')) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-cash-stack" style="color:var(--success)"></i>
                        <span>{{ __('report.income_by_category') }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if ($report['incomeByCategory']->isNotEmpty())
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('income.category') }}</th>
                                    <th class="text-end">{{ __('income.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report['incomeByCategory'] as $catId => $amount)
                                    <tr>
                                        <td>{{ $report['incomeCategories']->has($catId) ? $report['incomeCategories'][$catId]->name : __('general.unknown') }}</td>
                                        <td class="text-end">{{ Number::currency($amount, config('finance.currency_symbol')) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <x-empty-state icon="bi-cash-stack" :title="__('general.no_data')" />
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-cart" style="color:var(--danger)"></i>
                        <span>{{ __('report.expense_by_category') }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if ($report['expenseByCategory']->isNotEmpty())
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('expense.category') }}</th>
                                    <th class="text-end">{{ __('expense.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report['expenseByCategory'] as $catId => $amount)
                                    <tr>
                                        <td>{{ $report['expenseCategories']->has($catId) ? $report['expenseCategories'][$catId]->name : __('general.unknown') }}</td>
                                        <td class="text-end">{{ Number::currency($amount, config('finance.currency_symbol')) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <x-empty-state icon="bi-cart" :title="__('general.no_data')" />
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
