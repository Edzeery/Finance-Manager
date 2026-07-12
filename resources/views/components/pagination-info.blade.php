@props([
    'items' => null,
])

@if($items && $items->count())
    <div style="font-size:13px; color:var(--text-muted)">
        {{ __('general.showing') }} {{ $items->firstItem() }}&ndash;{{ $items->lastItem() }} {{ __('general.of') }} {{ $items->total() }}
    </div>
@endif
