<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.subscriptions') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.subscriptions') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.subscriptions_desc') }}</x-slot>

    @php $showPlanSubTabs = true; @endphp

    <x-filter-tabs :tabs="[
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-credit-card'],
        'active' => ['label' => __('general.active'), 'count' => $countActive, 'icon' => 'bi-check-circle'],
        'trialing' => ['label' => __('super-admin.trialing'), 'count' => $countTrialing, 'icon' => 'bi-star'],
        'past_due' => ['label' => __('super-admin.past_due'), 'count' => $countPastDue, 'icon' => 'bi-exclamation-triangle'],
        'canceled' => ['label' => __('super-admin.canceled'), 'count' => $countCanceled, 'icon' => 'bi-slash-circle'],
        'expired' => ['label' => __('super-admin.expired'), 'count' => $countExpired, 'icon' => 'bi-hourglass-split'],
    ]" current="{{ request('status', 'all') }}" keyParam="status" defaultKey="all"
        :preserve="['search', 'per_page']"
        subParam="plan_id"
        subCurrent="{{ request('plan_id', '') }}"
        :subTabs="$planSubTabs" />

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="{{ route('super.admin.subscriptions.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <x-search-filter name="search" placeholder="{{ __('super-admin.search_invoice') }}..." value="{{ request('search') }}" min-width="200px" />
                    @if (request('status') && request('status') !== 'all')
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if (request('plan_id'))
                        <input type="hidden" name="plan_id" value="{{ request('plan_id') }}">
                    @endif
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
                                    <x-status-badge domain="subscription" :status="$sub->status->value" set="bi" />
                                </td>
                                <td class="cell-muted">{{ $sub->starts_at?->format('Y/m/d') ?? '—' }}</td>
                                <td class="cell-muted">{{ $sub->ends_at?->format('Y/m/d') ?? '—' }}</td>
                                <td>
                                    <x-status-badge domain="general" :status="$sub->auto_renew ? 'yes' : 'no'" set="bi" />
                                </td>
                                <td class="col-actions">
                                    <a href="{{ route('super.admin.subscriptions.show', $sub) }}" class="btn btn-icon" title="{{ __('general.view') }}">
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
