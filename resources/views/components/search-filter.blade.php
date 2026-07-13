@props([
    'wireModel' => null,       // Livewire property name (e.g. 'search', 'couponSearch')
    'placeholder' => 'ابحث......',
    'size' => '',              // '' | 'sm' | 'xs'  => grid-filter / grid-filter-sm / grid-filter-xs
    'debounce' => '300ms',
    'icon' => 'bi-search',
    'name' => null,            // Form input name (for non-Livewire forms)
    'value' => null,           // Initial value (for non-Livewire forms)
    'minWidth' => '180px',     // Container min-width
])

@php
    $sizeClass = $size ? 'grid-filter-' . $size : '';
    $id = $attributes->get('id', $name ? 'search-' . $name : null);
@endphp

<div
    @if($id) id="{{ $id }}-wrapper" @endif
    class="{{ $attributes->get('class') }} position-relative grid-filter {{ $sizeClass }}"
    style="min-width:{{ $minWidth }}"
>
    <i
        class="bi {{ $icon }}"
        style="
            position:absolute;
            inset-inline-start:10px;
            top:50%;
            transform:translateY(-50%);
            font-size:13px;
            color:var(--text-muted);
            pointer-events:none;
        "
        aria-hidden="true"
    ></i>

    <input
        type="text"
        @if($wireModel)
            wire:model.live.debounce.{{ $debounce }}="{{ $wireModel }}"
        @else
            name="{{ $name ?? 'input' }}"
            @if(!is_null($value)) value="{{ $value }}" @endif
        @endif


        class="form-control"
        style="
            padding-block:7px;
            padding-inline:34px 14px;
            font-size:13px;
            border:1px solid var(--border);
            border-radius:var(--radius-sm);
            background:var(--card-bg);
            color:var(--text);
            width:100%;
        "
        placeholder="{{ $placeholder }}"
        autocomplete="off" value=""
    >
</div>
