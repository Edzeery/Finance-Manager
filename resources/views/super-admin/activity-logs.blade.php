<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.activity_log') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.activity_log') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.activity_log') }}</x-slot>

    @php
        $subjectLabels = [
            'App\Models\Income' => __('income.title'),
            'App\Models\Expense' => __('expense.title'),
            'App\Models\Debt' => __('debt.title'),
            'App\Models\Asset' => __('asset.title'),
            'App\Models\Budget' => __('budget.title'),
            'App\Models\FinancialGoal' => __('goal.title'),
            'App\Models\ZakatRecord' => __('zakat.title'),
            'App\Models\IncomeCategory' => __('income.categories'),
            'App\Models\ExpenseCategory' => __('expense.categories'),
            'App\Models\User' => __('general.user'),
            'App\Models\Workspace' => __('settings.workspace'),
            'App\Models\Subscription' => __('settings.subscription'),
            'App\Models\Invoice' => __('settings.invoice'),
            'App\Models\Payment' => __('payment.title'),
        ];
        $actionConfigs = [
            'created' => ['icon' => 'bi-plus-circle-fill', 'color' => 'var(--success)', 'bg' => 'var(--success-light)'],
            'updated' => ['icon' => 'bi-pencil-fill', 'color' => 'var(--info)', 'bg' => 'var(--info-light)'],
            'deleted' => ['icon' => 'bi-trash-fill', 'color' => 'var(--danger)', 'bg' => 'var(--danger-light)'],
            'restored' => ['icon' => 'bi-arrow-counterclockwise', 'color' => 'var(--warning)', 'bg' => 'var(--warning-light)'],
        ];
    @endphp

    <x-filter-tabs :tabs="[
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-clock-history'],
        'created' => ['label' => __('general.activity_created'), 'count' => $countCreated, 'icon' => 'bi-plus-circle'],
        'updated' => ['label' => __('general.activity_updated'), 'count' => $countUpdated, 'icon' => 'bi-pencil'],
        'deleted' => ['label' => __('general.activity_deleted'), 'count' => $countDeleted, 'icon' => 'bi-trash'],
        'restored' => ['label' => __('general.activity_restored'), 'count' => $countRestored, 'icon' => 'bi-arrow-counterclockwise'],
    ]" current="{{ request('action', 'all') }}" keyParam="action" defaultKey="all"
        :preserve="['search', 'per_page', 'date_from', 'date_to']" />

    <div class="data-grid" x-data>
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.activity-log') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('super-admin.search_activity') }}..." value="{{ request('search') }}" />
                    <input type="date" name="date_from" class="form-control grid-filter-sm" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)" value="{{ request('date_from') }}">
                    <input type="date" name="date_to" class="form-control grid-filter-sm" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)" value="{{ request('date_to') }}">
                    @if (request('action') && request('action') !== 'all')
                        <input type="hidden" name="action" value="{{ request('action') }}">
                    @endif
                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:var(--primary);font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
                    <x-clear-filters :filters="['search','action','date_from','date_to']" :route="route('super.admin.activity-log')" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <x-per-page :current="(int) request('per_page', 10)" :route="route('super.admin.activity-log')" :preserve="['search','action','date_from','date_to']" :options="[5, 10, 20, 35, 50]" />
            </div>
        </div>

        <div class="data-grid-body">
            @if($logs->count())
                <div class="activity-feed" style="padding:16px 24px">
                    @foreach($logs as $log)
                        @php
                            $actionConfig = $actionConfigs[$log->action] ?? ['icon' => 'bi-circle-fill', 'color' => 'var(--text-muted)', 'bg' => 'var(--border)'];
                            $subjectLabel = $subjectLabels[$log->subject_type] ?? class_basename($log->subject_type);
                            $hasProps = $log->properties && (is_array($log->properties) || is_object($log->properties));
                        @endphp
                        <div class="timeline-item" x-data="{ expanded: false }">
                            <div class="timeline-marker">
                                <div class="timeline-dot" style="background:{{ $actionConfig['bg'] }};color:{{ $actionConfig['color'] }}">
                                    <i class="bi {{ $actionConfig['icon'] }}"></i>
                                </div>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                            <span class="badge" style="font-size:10px;background:{{ $actionConfig['bg'] }};color:{{ $actionConfig['color'] }};padding:2px 10px;border-radius:6px;font-weight:600">{{ $log->action }}</span>
                                            <span class="badge timeline-subject" style="font-size:9px;background:var(--bg-subtle);color:var(--text-muted);padding:2px 8px;border-radius:4px;font-weight:500">{{ $subjectLabel }}</span>
                                            @if($log->subject_id)
                                                <span style="font-size:11px;color:var(--text-muted)">#{{ $log->subject_id }}</span>
                                            @endif
                                        </div>
                                        <div style="font-size:13px;color:var(--text)">{{ $log->description ?: '—' }}</div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px">
                                            <i class="bi bi-person"></i> {{ $log->user?->name ?? __('general.deleted_user') }}
                                            @if($log->workspace)
                                                <i class="bi bi-building ms-2"></i> {{ $log->workspace->name }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1" style="flex-shrink:0">
                                        <span class="timeline-meta">{{ $log->created_at->diffForHumans() }}</span>
                                        @if($hasProps)
                                            <button type="button" @click="expanded = !expanded" class="btn" style="padding:2px 6px;font-size:10px;border-radius:4px;border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer;line-height:1">
                                                <i class="bi" :class="expanded ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @if($hasProps)
                                    <div x-show="expanded" x-collapse>
                                        <pre style="font-size:11px;color:var(--text-muted);background:var(--bg-subtle);padding:8px 12px;border-radius:6px;margin-top:6px;overflow-x:auto;max-width:100%">{{ json_encode($log->properties, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                    <div x-show="!expanded">
                                        @php $propsStr = json_encode($log->properties, JSON_UNESCAPED_UNICODE); @endphp
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;cursor:pointer" @click="expanded = true" title="{{ __('general.click_to_expand') }}">
                                            {{ \Illuminate\Support\Str::limit($propsStr, 80) }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="data-grid-footer">
                    <x-pagination-info :items="$logs" />
                    <div>{{ $logs->appends(request()->except('page'))->links() }}</div>
                </div>
            @else
                <x-empty-state icon="bi bi-clock-history" :title="__('general.no_data')" />
            @endif
        </div>
    </div>
</x-super-admin-layout>
