@props([
    'variant' => 'accent',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'submit' => false,
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'block' => false,
    'class' => '',
])

@php
    $sizeClasses = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
    ];

    $variantClasses = [
        'accent' => 'btn-accent',
        'outline' => 'btn-outline-secondary',
        'outline-accent' => 'btn-outline-accent',
        'danger' => 'btn-danger',
        'success' => 'btn-success',
        'outline-danger' => 'btn-outline-danger',
        'outline-success' => 'btn-outline-success',
        'ghost' => 'btn-ghost',
    ];

    $iconFirst = $iconPosition === 'left';
    $hasText = $slot !== null && trim(strip_tags($slot->toHtml())) !== '';
    $classes = 'btn btn-custom ' . ($variantClasses[$variant] ?? 'btn-accent') . ' ' . ($sizeClasses[$size] ?? '');
    if ($block) { $classes .= ' btn-block-mobile'; }
    if ($loading) { $classes .= ' btn-submitting'; }
    if ($class) { $classes .= ' ' . $class; }

    $tag = $href ? 'a' : 'button';
    $type = $submit ? 'submit' : 'button';
    $hrefAttr = $href ? "href={$href}" : '';
    $disabledAttr = $disabled && $tag === 'button' ? 'disabled' : '';
@endphp

<{{ $tag }} {{ $hrefAttr }} type="{{ $tag === 'button' ? $type : '' }}" {{ $disabledAttr }} {{ $attributes->merge(['class' => $classes]) }}>
    @if($loading)<span style="visibility:hidden;display:inline-flex;align-items:center;gap:8px">@endif

    @if($icon && $iconFirst)<i class="{{ $icon }}"></i>@endif
    @if($hasText)<span>{{ $slot }}</span>@endif
    @if($icon && !$iconFirst)<i class="{{ $icon }}"></i>@endif

    @if($loading)</span>@endif
</{{ $tag }}>
