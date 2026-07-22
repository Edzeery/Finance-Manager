@props([
    'icon' => null,
    'iconBg' => 'var(--accent-light)',
    'iconColor' => 'var(--accent)',
    'borderColor' => null,
    'label' => null,
    'value' => null,
    'currency' => null,
    'subtitle' => null,
    'trend' => null,
    'trendIcon' => null,
    'trendDir' => null,
    'valueClass' => '',
    'href' => null,
    'center' => false,
    'size' => 'md',
])

@php
    $border = $borderColor ?? $iconColor;
    $clickAttrs = $href ? "x-data @click=\"window.location.href = '" . addslashes($href) . "'\" role=\"link\"" : '';
@endphp

<div {!! $clickAttrs !!} class="kpi-card {{ $center ? 'text-center' : '' }} {{ $attributes->get('class', '') }}" style="border-top: 3px solid {{ $border }};{{ $href ? 'cursor:pointer' : '' }}">
    @if($icon)
        <div class="kpi-icon {{ $center ? 'mx-auto' : '' }}" style="background:{{ $iconBg }}; color:{{ $iconColor }}">
            <i class="bi {{ $icon }}"></i>
        </div>
    @endif

    @if($label)
        <div class="kpi-label">{{ $label }}</div>
    @endif

    @if($value)
        <div class="kpi-value {{ $valueClass }}" @if($size === 'sm') style="font-size:18px" @endif>
            {{ $value }}
            @if($currency)
                <small style="font-size:14px; font-weight:400; color:var(--text-muted)">{{ $currency }}</small>
            @endif
        </div>
    @endif

    @if($subtitle)
        <div class="kpi-trend" style="color:var(--text-muted)">{{ $subtitle }}</div>
    @endif

    @if($trend)
        <div class="kpi-trend {{ $trendDir }}">
            @if($trendIcon)
                <i class="bi {{ $trendIcon }}"></i>
            @endif
            {!! $trend !!}
        </div>
    @endif

    {{ $slot }}
</div>
