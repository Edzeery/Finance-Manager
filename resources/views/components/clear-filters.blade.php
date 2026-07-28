@props([
    'route' => null,
    'filters' => [],
    'label' => null,
])

@php $hasFilters = collect($filters)->filter(fn($v) => request()->filled($v))->isNotEmpty(); @endphp

@if($hasFilters)
    <x-button href="{{ $route ?? url()->current() }}" size="sm" variant="outline" icon="bi bi-x-lg" title="{{ __('general.clear') }}">
        @if($label)
            <span class="ms-1">{{ $label }}</span>
        @endif
    </x-button>
@endif
