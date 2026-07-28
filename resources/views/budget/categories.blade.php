<x-app-layout>
    <x-slot:title>{{ __('budget.categories') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('budget.categories') }}</x-slot>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div></div>
        <div class="d-flex gap-2 align-items-center">
            <x-button href="{{ route('budget.create') }}" icon="bi bi-plus-lg" wire:navigate>{{ __('budget.add') }}</x-button>
        </div>
    </div>

    @if($categories->count())
        <div class="card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>{{ __('expense.category') }}</th>
                                <th>{{ __('expense.type') }}</th>
                                <th>{{ __('budget.categories') }}</th>
                                <th class="text-end">{{ __('budget.allocated_amount') }}</th>
                                <th class="text-end">{{ __('budget.spent_amount') }}</th>
                                <th class="text-end">{{ __('budget.remaining') }}</th>
                                <th style="width:130px">{{ __('budget.adherence') }}</th>
                                <th class="text-end" style="width:100px">{{ __('general.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $cat)
                                @php
                                    $info = $cat->budgetInfo;
                                    $hasBudget = $info !== null;
                                    $pct = $hasBudget && $info['allocated'] > 0
                                        ? round(($info['spent'] / $info['allocated']) * 100, 1)
                                        : 0;
                                    $barColor = $pct > 100 ? 'var(--danger)' : ($pct > 80 ? 'var(--warning)' : 'var(--success)');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="{{ $cat->icon ?? 'bi-tag' }}" style="color:{{ $cat->color ?? 'var(--text-muted)' }}; font-size:16px"></i>
                                            <span>{{ locale_name($cat) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-custom" style="background:var(--bg); color:var(--text-muted); font-size:11px">
                                            {{ __("expense.{$cat->type}") }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($hasBudget)
                                            <span class="d-flex align-items-center gap-1" style="color:var(--success); font-size:13px">
                                                <i class="bi bi-check-circle-fill"></i>
                                                {{ $info['budget_name'] }}
                                            </span>
                                        @else
                                            <span class="d-flex align-items-center gap-1" style="color:var(--text-muted); font-size:13px">
                                                <i class="bi bi-dash-circle"></i>
                                                {{ __('budget.no_budgets') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end" style="font-size:13px">
                                        @if($hasBudget)
                                            {{ number_format($info['allocated'], 2) }}
                                        @else
                                            <span style="color:var(--text-muted)">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end" style="font-size:13px">
                                        @if($hasBudget)
                                            <span style="color:var(--danger)">{{ number_format($info['spent'], 2) }}</span>
                                        @else
                                            <span style="color:var(--text-muted)">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end" style="font-size:13px">
                                        @if($hasBudget)
                                            <span style="color:{{ $info['spent'] > $info['allocated'] ? 'var(--danger)' : 'var(--success)' }}">
                                                {{ number_format($info['remaining'], 2) }}
                                            </span>
                                        @else
                                            <span style="color:var(--text-muted)">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($hasBudget)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress" style="flex:1; height:6px; background:var(--border); border-radius:3px">
                                                    <div class="progress-bar" style="width:{{ min($pct, 100) }}%; background:{{ $barColor }}; border-radius:3px"></div>
                                                </div>
                                                <span style="font-size:12px; font-weight:600; color:{{ $barColor }}">{{ $pct }}%</span>
                                            </div>
                                        @else
                                            <span style="color:var(--text-muted); font-size:12px">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($hasBudget)
                                            <a href="{{ route('budget.show', $info['budget_id']) }}" class="action-btn" title="{{ __('general.details') }}" wire:navigate>
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @else
                                            <x-button href="{{ route('budget.create') }}?category_id={{ $cat->id }}" size="sm" icon="bi bi-plus-lg" wire:navigate
                                                style="background:var(--accent); color:#fff; font-size:11px; padding:3px 10px; border-radius:6px">{{ __('budget.add') }}</x-button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @php
            $withBudget = $categories->filter(fn ($c) => $c->budgetInfo !== null)->count();
            $withoutBudget = $categories->filter(fn ($c) => $c->budgetInfo === null)->count();
        @endphp
        <div class="d-flex gap-4 mt-3" style="font-size:13px; color:var(--text-muted)">
            <span><i class="bi bi-check-circle-fill ms-1" style="color:var(--success)"></i> {{ __('budget.with_budget', ['count' => $withBudget]) }}</span>
            <span><i class="bi bi-dash-circle ms-1" style="color:var(--text-muted)"></i> {{ __('budget.without_budget', ['count' => $withoutBudget]) }}</span>
            <span><i class="bi bi-tags-fill ms-1" style="color:var(--accent)"></i> {{ __('budget.total_categories', ['count' => $categories->count()]) }}</span>
        </div>
    @else
        <x-empty-state
            icon="bi-tags"
            :title="__('expense.no_categories')"
            :message="__('expense.create_first_category')"
            :action="route('expense.categories.index')"
            :actionText="__('expense.add_category')"
        />
    @endif
</x-app-layout>
