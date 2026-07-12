@props([
    'periods' => [],
    'currentPeriod' => 'this_month',
    'startDate' => null,
    'endDate' => null,
    'route' => null,
    'preserve' => [],
])

@php $baseUrl = $route ?? request()->url(); @endphp

<div class="date-filter-bar" x-data="dateFilterBar(@js($currentPeriod), @js($startDate), @js($endDate))">
    <div class="d-flex flex-wrap align-items-center gap-2">
        <div class="filter-periods d-flex gap-1 flex-wrap">
            @foreach($periods as $key => $config)
                @if ($key === 'custom')
                    <button type="button"
                        class="filter-period-btn"
                        :class="{ 'active': period === 'custom' }"
                        x-on:click="setCustom()"
                    >
                        {{ __("filters.{$key}") }}
                    </button>
                @else
                    <a href="{{ $baseUrl }}?period={{ $key }}"
                        class="filter-period-btn {{ $currentPeriod === $key ? 'active' : '' }}"
                        role="tab"
                        aria-selected="{{ $currentPeriod === $key ? 'true' : 'false' }}"
                    >
                        {{ __("filters.{$key}") }}
                    </a>
                @endif
            @endforeach
        </div>

        <div class="filter-custom-range d-flex align-items-center gap-2"
            :class="{ 'd-none': period !== 'custom' }" x-cloak>
            <div class="filter-date-field">
                <svg class="filter-date-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <input type="date" x-model="startDate" class="form-custom">
            </div>
            <span class="filter-date-sep">{{ __('general.to') }}</span>
            <div class="filter-date-field">
                <svg class="filter-date-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <input type="date" x-model="endDate" class="form-custom">
            </div>
            <template x-if="startDate && endDate">
                <a x-bind:href="'{{ $baseUrl }}?period=custom&start_date=' + startDate + '&end_date=' + endDate"
                    class="btn btn-accent btn-sm px-3">
                    <i class="bi bi-check-lg"></i>
                    <span>{{ __('filters.apply') }}</span>
                </a>
            </template>
        </div>
    </div>

</div>
