@php
    $user = auth()->user();
    $currentWs = $user->currentWorkspace;
    $activeSub = $currentWs ? $currentWs->owner()?->first()?->activeSubscription() : null;
    $currentPlan = $activeSub?->plan;
    $planFeats = $currentPlan ? $currentPlan->featureSlugs() : [];
    $feat = fn($s) => in_array($s, $planFeats);

    $activeTab = request()->query('tab');

    $accountLinks = [
        ['route' => 'settings.account.index', 'label' => __('profile.profile_info'), 'icon' => 'bi-person', 'active' => request()->routeIs('settings.account.index') && !$activeTab],
        ['route' => 'settings.account.index', 'params' => ['tab' => 'notifications'], 'label' => __('profile.notifications'), 'icon' => 'bi-bell', 'active' => $activeTab === 'notifications'],
        ['route' => 'settings.account.index', 'params' => ['tab' => 'security'], 'label' => __('settings.security'), 'icon' => 'bi-shield-lock', 'active' => $activeTab === 'security'],
    ];

    $workspaceLinks = [
        ['route' => 'settings.workspace.index', 'params' => ['tab' => 'general'], 'label' => __('settings.general'), 'icon' => 'bi-gear', 'active' => ($activeTab === 'general' || request()->routeIs('settings.workspace.index')) && !in_array($activeTab, ['team', 'roles', 'integrations'])],
        ['route' => 'settings.workspace.index', 'params' => ['tab' => 'team'], 'label' => __('workspace.team'), 'icon' => 'bi-people', 'active' => $activeTab === 'team'],
        ['route' => 'settings.workspace.index', 'params' => ['tab' => 'roles'], 'label' => __('workspace.roles'), 'icon' => 'bi-shield-check', 'active' => $activeTab === 'roles'],
        ['route' => 'settings.workspace.index', 'params' => ['tab' => 'integrations'], 'label' => __('settings.integrations'), 'icon' => 'bi-plug', 'active' => $activeTab === 'integrations'],
    ];
@endphp

<div class="profile-sidebar">
    <div class="profile-card">
        <div class="profile-card-header">
            <i class="bi bi-gear-fill"></i>
            <span>{{ __('general.settings') }}</span>
        </div>
        <nav class="profile-nav">
            <div style="padding:8px 12px 4px;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">
                {{ __('settings.personal') }}
            </div>
            @foreach($accountLinks as $link)
                @php
                    $linkUrl = isset($link['params'])
                        ? route($link['route'], $link['params'])
                        : route($link['route']);
                @endphp
                <a href="{{ $linkUrl }}"
                   class="profile-nav-item {{ $link['active'] ? 'active' : '' }}"
                   wire:navigate
                   style="background:none;border:none;cursor:pointer;text-align:start;width:100%;text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:8px;font-size:14px;transition:all 0.15s;">
                    <i class="bi {{ $link['icon'] }}"></i>
                    <span>{{ $link['label'] }}</span>
                    @if(($link['premium'] ?? false))
                        <x-status-badge domain="general" status="premium" set="bi" />
                    @endif
                </a>
            @endforeach

            @if($user->workspaceHasPermission('workspace-setting.view') || $user->isWorkspaceAdmin())
                <hr style="border:none;border-top:1px solid var(--border);margin:8px 0;">
                <div style="padding:4px 12px 4px;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">
                    {{ __('settings.workspace') }}
                </div>
                @foreach($workspaceLinks as $link)
                    @php
                        $linkUrl = isset($link['params'])
                            ? route($link['route'], $link['params'])
                            : route($link['route']);
                    @endphp
                    <a href="{{ $linkUrl }}"
                       class="profile-nav-item {{ $link['active'] ? 'active' : '' }}"
                       wire:navigate
                       style="background:none;border:none;cursor:pointer;text-align:start;width:100%;text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:8px;font-size:14px;transition:all 0.15s;">
                        <i class="bi {{ $link['icon'] }}"></i>
                        <span>{{ $link['label'] }}</span>
                        @if(($link['premium'] ?? false))
                            <x-status-badge domain="general" status="premium" set="bi" />
                        @endif
                    </a>
                @endforeach
            @endif
        </nav>
    </div>
</div>
