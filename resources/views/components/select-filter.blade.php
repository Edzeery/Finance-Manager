@props([
    'name' => '',
    'options' => [],
    'selected' => null,
    'placeholder' => '',
    'minWidth' => '120px',
    'wireModel' => null,
])

@php
    $defaultClass = 'form-control grid-filter-sm';
    $mergedClass = trim($defaultClass . ' ' . $attributes->get('class', ''));
@endphp

<select
    @if($name) name="{{ $name }}" @endif
    @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
    class="{{ $mergedClass }}"
    style="width:auto;min-width:{{ $minWidth }};padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text);{{ $attributes->get('style') }}"
    {{ $attributes->except(['class', 'style']) }}
>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    @foreach($options as $value => $label)
        <option value="{{ $value }}" {{ (!is_null($selected) && $selected == $value) || (is_null($selected) && request($name) == $value) ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>
