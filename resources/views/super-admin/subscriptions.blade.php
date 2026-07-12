<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.subscriptions') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.subscriptions') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.subscriptions_desc') }}</x-slot>

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                @php
                    $planOptions = $plans->pluck('name', 'id')->toArray();
                @endphp

                <form method="GET" action="{{ route('super.admin.subscriptions.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('super-admin.search_invoice') }}..." value="{{ request('search') }}" min-width="200px" />
                    <x-select-filter name="status" :options="[
                        'active' => __('general.active'),
                        'trialing' => __('super-admin.trialing'),
                        'past_due' => __('super-admin.past_due'),
                        'canceled' => __('super-admin.canceled'),
                        'expired' => __('super-admin.expired'),
                    ]" placeholder="{{ __('general.all_status') }}" min-width="120px" />
                    <x-select-filter name="plan_id" :options="$planOptions" placeholder="{{ __('general.all_plans') }}" min-width="120px" />
                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">{{ __('general.filter') }}</button>
                    <x-clear-filters :filters="['search','status','plan_id']" :route="route('super.admin.subscriptions.index')" />
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <x-per-page :current="(int) request('per_page', 15)" :route="route('super.admin.subscriptions.index')" :preserve="['search','status','plan_id']" :options="[10, 15, 25, 50]" />
            </div>
        </div>

        <div class="data-grid-body">
            @if($subscriptions->count())
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('super-admin.workspace') }}</th>
                            <th>{{ __('settings.plan') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th>{{ __('super-admin.started') }}</th>
                            <th>{{ __('super-admin.ends_at') }}</th>
                            <th>{{ __('super-admin.auto_renew') }}</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $sub)
                            <tr>
                                <td>
                                    <span style="font-weight:500">{{ $sub->workspace?->name ?? __('general.unknown') }}</span>
                                </td>
                                <td>
                                    <span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:2px 10px;border-radius:6px">{{ $sub->plan?->name ?? '—' }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = ['active' => 'var(--success)', 'trialing' => 'var(--info)', 'past_due' => 'var(--warning)', 'canceled' => 'var(--text-muted)', 'expired' => 'var(--danger)'];
                                        $statusBg = ['active' => 'var(--success-light)', 'trialing' => 'var(--info-light)', 'past_due' => 'var(--warning-light)', 'canceled' => 'var(--border)', 'expired' => 'var(--danger-light)'];
                                    @endphp
                                    <span class="badge" style="font-size:10px;background:{{ $statusBg[$sub->status->value] ?? 'var(--border)' }};color:{{ $statusColors[$sub->status->value] ?? 'var(--text-muted)' }};padding:3px 10px;border-radius:6px;font-weight:600">
                                        {{ $sub->status->label() }}
                                    </span>
                                </td>
                                <td class="cell-muted">{{ $sub->starts_at?->format('Y/m/d') ?? '—' }}</td>
                                <td class="cell-muted">{{ $sub->ends_at?->format('Y/m/d') ?? '—' }}</td>
                                <td>
                                    @if($sub->auto_renew)
                                        <span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.yes') }}</span>
                                    @else
                                        <span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.no') }}</span>
                                    @endif
                                </td>
                                <td class="col-actions">
                                    <a href="{{ route('super.admin.subscriptions.show', $sub) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.view') }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <h4>{{ __('general.no_data') }}</h4>
                    <p>{{ __('messages.no_results') }}</p>
                </div>
            @endif
        </div>

        @if($subscriptions->count())
            <div class="data-grid-footer">
                <x-pagination-info :items="$subscriptions" />
                <div>{{ $subscriptions->appends(request()->except('page'))->links() }}</div>
            </div>
        @endif
    </div>
</x-super-admin-layout>
