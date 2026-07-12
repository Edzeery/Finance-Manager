@props([
    'route' => null,
    'filters' => [],
    'label' => null,
])

@php $hasFilters = collect($filters)->filter(fn($v) => request()->filled($v))->isNotEmpty(); @endphp

@if($hasFilters)
    <a href="{{ $route ?? url()->current() }}" class="btn btn-sm btn-outline-secondary btn-custom" title="{{ __('general.clear') }}">
        <i class="bi bi-x-lg"></i>
        @if($label)
            <span class="ms-1">{{ $label }}</span>
        @endif
    </a>
@endif
