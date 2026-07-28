@props([
    'tabs' => [],
    'current' => null,
    'style' => 'underline',
    'mode' => 'server',
    'route' => null,
    'preserve' => ['search', 'per_page'],
    'keyParam' => 'tab',
    'alpine' => null,
    'class' => '',
    'onTabClick' => null,
    'description' => null,
])

@php
    $wrapperClass = match($style) {
        'pills' => 'tabs-pills',
        'vertical' => 'tabs-vertical',
        default => 'tabs-underline',
    };
    if ($class) { $wrapperClass .= ' ' . $class; }
@endphp

@if($style === 'vertical')
    <div class="{{ $wrapperClass }}">
        <nav class="tabs-vertical-nav" role="tablist">
            @foreach($tabs as $key => $config)
                @php
                    $isActive = $current === $key;
                    $label = $config['label'] ?? $key;
                    $icon = $config['icon'] ?? null;
                    $count = $config['count'] ?? null;
                    $desc = $config['desc'] ?? null;

                    if ($mode === 'server') {
                        $queryParams = array_merge(
                            request()->except([$keyParam, ...$preserve]),
                            [$keyParam => $key]
                        );
                        $queryParams = array_filter($queryParams, fn($v) => $v !== '' && $v !== null);
                        $url = route($route ?? request()->route()->getName(), $queryParams);
                    }
                @endphp

                @if($mode === 'server')
                    <a href="{{ $url }}"
                        class="tabs-vertical-item {{ $isActive ? 'active' : '' }}"
                        role="tab"
                        aria-selected="{{ $isActive ? 'true' : 'false' }}"
                    >
                @else
                    <button type="button"
                        class="tabs-vertical-item {{ $isActive ? 'active' : '' }}"
                        data-tab="{{ $key }}"
                        role="tab"
                        aria-selected="{{ $isActive ? 'true' : 'false' }}"
                        @if($alpine)
                            @click="{{ $alpine }} = '{{ $key }}'"
                        @elseif($onTabClick)
                            onclick="{{ $onTabClick }}('{{ $key }}')"
                        @endif
                    >
                @endif

                    <div class="tabs-vertical-icon">
                        @if($icon)
                            <i class="{{ $icon }}"></i>
                        @endif
                    </div>
                    <div class="tabs-vertical-text">
                        <span class="tabs-vertical-label">{{ $label }}</span>
                        @if($desc)
                            <span class="tabs-vertical-desc">{{ $desc }}</span>
                        @endif
                    </div>
                    @if($count !== null)
                        <span class="tabs-vertical-count">{{ $count }}</span>
                    @endif

                @if($mode === 'server')
                    </a>
                @else
                    </button>
                @endif
            @endforeach
        </nav>
    </div>
@else
    <div class="tabs-wrapper {{ $wrapperClass }}">
        <nav class="tabs-nav" role="tablist">
            @foreach($tabs as $key => $config)
                @php
                    $isActive = $current === $key;
                    $label = $config['label'] ?? $key;
                    $icon = $config['icon'] ?? null;
                    $count = $config['count'] ?? null;

                    if ($mode === 'server') {
                        $queryParams = array_merge(
                            request()->except([$keyParam, ...$preserve]),
                            [$keyParam => $key]
                        );
                        $queryParams = array_filter($queryParams, fn($v) => $v !== '' && $v !== null);
                        $url = route($route ?? request()->route()->getName(), $queryParams);
                    }
                @endphp

                @if($mode === 'server')
                    <a href="{{ $url }}"
                        class="tabs-tab {{ $isActive ? 'active' : '' }}"
                        role="tab"
                        aria-selected="{{ $isActive ? 'true' : 'false' }}"
                    >
                @else
                    <button type="button"
                        class="tabs-tab {{ $isActive ? 'active' : '' }}"
                        data-tab="{{ $key }}"
                        role="tab"
                        aria-selected="{{ $isActive ? 'true' : 'false' }}"
                        @if($alpine)
                            @click="{{ $alpine }} = '{{ $key }}'"
                        @elseif($onTabClick)
                            onclick="{{ $onTabClick }}('{{ $key }}')"
                        @endif
                    >
                @endif

                    @if($icon)
                        <i class="{{ $icon }}"></i>
                    @endif
                    <span>{{ $label }}</span>
                    @if($count !== null)
                        <span class="tabs-count">{{ $count }}</span>
                    @endif

                @if($mode === 'server')
                    </a>
                @else
                    </button>
                @endif
            @endforeach
        </nav>
    </div>
@endif
