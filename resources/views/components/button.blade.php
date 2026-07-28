@props([
    'variant' => 'accent',
    'size' => 'sm',
    'icon' => null,
    'iconPosition' => 'left',
    'submit' => false,
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'block' => false,
    'class' => '',
    'wireClick' => null,
    'wireTarget' => null,
    'wireLoadingRemove' => true,
    'wireLoadingClass' => 'spinner-border spinner-border-sm',
    'wireLoadingAttr' => null,
    'wireLoadingValue' => null,
    'statusBadge' => null,
    'statusSet' => null,
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
    $hasStatusBadge = $statusBadge !== null;
    $hasWireLoading = $wireTarget !== null;

    $classes = 'btn btn-custom ' . ($variantClasses[$variant] ?? 'btn-accent') . ' ' . ($sizeClasses[$size] ?? '');
    if ($block) {
        $classes .= ' btn-block-mobile';
    }
    if ($loading) {
        $classes .= ' btn-submitting';
    }
    if ($class) {
        $classes .= ' ' . $class;
    }

    $tag = $href ? 'a' : 'button';

    $extraAttrs = [];
    if ($href) {
        $extraAttrs['href'] = $href;
    }

    if ($tag === 'button') {
        $extraAttrs['type'] = $submit ? 'submit' : 'button';
    }
    if ($disabled && $tag === 'button') {
        $extraAttrs['disabled'] = 'disabled';
    }
    if ($wireClick) {
        $extraAttrs['wire:click'] = $wireClick;
    }
    if ($wireTarget) {
        $extraAttrs['wire:target'] = $wireTarget;
    }
    if ($wireLoadingAttr) {
        $extraAttrs["wire:loading.{$wireLoadingAttr}"] = $wireLoadingValue;
    }

    $statusBadgeParts = null;
    if ($hasStatusBadge) {
        $parts = explode('.', $statusBadge);
        $statusBadgeDomain = $parts[0] ?? null;
        $statusBadgeStatus = $parts[1] ?? null;
        $statusBadgeParts =
            $statusBadgeDomain && $statusBadgeStatus
                ? \Edzeery\MyStatusKit\Facades\Status::for($statusBadgeDomain, $statusBadgeStatus)
                : null;
    }
@endphp

<{{ $tag }} {{ $attributes->merge(array_merge($extraAttrs, ['class' => $classes])) }}>
    @if ($loading)
        <span style="visibility:hidden;display:inline-flex;align-items:center;gap:8px">
    @endif

    {{-- Left icon --}}
    @if ($icon && $iconFirst)
        <i class="{{ $icon }}"
            @if ($hasWireLoading && $wireLoadingRemove) wire:loading.remove wire:target="{{ $wireTarget }}" @endif></i>
    @endif

    {{-- Text slot --}}
    @if ($hasText)
        <span
            @if ($hasWireLoading && $wireLoadingRemove) wire:loading.remove wire:target="{{ $wireTarget }}" @endif>{{ $slot }}</span>
    @endif

    {{-- Status badge --}}
    @if ($hasStatusBadge && $statusBadgeParts)
        <span
            @if ($hasWireLoading && $wireLoadingRemove) wire:loading.remove wire:target="{{ $wireTarget }}" @endif>{!! $statusBadgeParts->badge($statusSet) !!}</span>
    @endif

    {{-- Right icon --}}
    @if ($icon && !$iconFirst)
        <i class="{{ $icon }}"
            @if ($hasWireLoading && $wireLoadingRemove) wire:loading.remove wire:target="{{ $wireTarget }}" @endif></i>
    @endif

    {{-- Loading spinner --}}
    @if ($hasWireLoading)
        <div wire:loading wire:target="{{ $wireTarget }}" class="{{ $wireLoadingClass }}" role="status"></div>
    @endif

    @if ($loading)
        </span>
    @endif
    </{{ $tag }}>
