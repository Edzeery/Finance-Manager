@props([
    'icon' => null,
    'iconBg' => 'rgba(21,183,108,0.1)',
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
    $tag = $href ? 'a' : 'div';
    $attrs = $href ? "href=\"{$href}\" class=\"text-decoration-none\"" : '';
    $border = $borderColor ?? $iconColor;
@endphp

<{{ $tag }} {!! $attrs !!} class="kpi-card {{ $center ? 'text-center' : '' }} {{ $attributes->get('class', '') }}" style="border-top: 3px solid {{ $border }};">
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
</{{ $tag }}>
