@props([
    'tabs' => [],
    'current' => '',
    'route' => null,
    'preserve' => ['search', 'per_page'],
    'keyParam' => 'tab',
    'defaultKey' => null,
    'subParam' => null,
    'subTabs' => [],
    'subCurrent' => null,
])

@php
    if ($subCurrent === null) {
        $subCurrent = request($subParam, '');
    }
    $exceptKeys = array_merge([$keyParam], $subParam ? [$subParam] : [], $preserve);
    $routeName = $route ?? request()->route()->getName();
@endphp

<div class="filter-tabs-wrapper mt-2">
    <div class="filter-tabs-scroll">
        <nav class="filter-tabs" role="tablist">
            @foreach($tabs as $key => $config)
                @php
                    $isActive = $current === $key;
                    $queryParams = array_merge(
                        request()->except($exceptKeys),
                        $key !== $defaultKey ? [$keyParam => $key] : []
                    );
                    $queryParams = array_filter($queryParams, fn($v) => $v !== '' && $v !== null);
                    $url = route($routeName, $queryParams);
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

    @if($subParam && count($subTabs) > 0)
        <div class="filter-subtabs">
            <nav class="filter-subtabs-inner" role="tablist">
                @foreach($subTabs as $key => $config)
                    @php
                        $isActive = $subCurrent === $key;
                        $queryParams = array_merge(
                            request()->except($exceptKeys),
                            [$keyParam => $current, $subParam => $key]
                        );
                        $queryParams = array_filter($queryParams, fn($v) => $v !== '' && $v !== null);
                        $url = route($routeName, $queryParams);
                        $count = $config['count'] ?? null;
                        $label = $config['label'] ?? $key;
                    @endphp
                    <a href="{{ $url }}"
                        class="filter-subtab {{ $isActive ? 'active' : '' }}"
                        role="tab"
                        aria-selected="{{ $isActive ? 'true' : 'false' }}"
                    >
                        <span>{{ $label }}</span>
                        @if($count !== null)
                            <span class="filter-tab-count">{{ $count }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>
    @endif
</div>
