@props([
    'tabs' => [],
    'current' => 'all',
    'route' => null,
    'preserve' => ['search', 'per_page'],
    'keyParam' => 'tab',
])

<div class="filter-tabs-wrapper">
    <div class="filter-tabs-scroll">
        <nav class="filter-tabs" role="tablist">
            @foreach($tabs as $key => $config)
                @php
                    $isActive = $current === $key;
                    $queryParams = array_merge(
                        request()->except([$keyParam, ...$preserve]),
                        [$keyParam => $key]
                    );
                    
                    $queryParams = array_filter($queryParams, fn($v) => $v !== '' && $v !== null);
                    $url = route($route ?? request()->route()->getName(), $queryParams);
                    $count = $config['count'] ?? null;
                    $label = $config['label'] ?? $key;
                    $icon = $config['icon'] ?? null;
                @endphp
                <a href="{{ $url }}"
                    class="filter-tab {{ $isActive ? 'active' : '' }}"
                    role="tab"
                    aria-selected="{{ $isActive ? 'true' : 'false' }}"
                >
                    @if($icon)
                        <i class="{{ $icon }}" style="font-size:14px"></i>
                    @endif
                    <span>{{ $label }}</span>
                    @if($count !== null)
                        <span class="filter-tab-count">{{ $count }}</span>
                    @endif
                </a>
            @endforeach
        </nav>
    </div>
</div>
