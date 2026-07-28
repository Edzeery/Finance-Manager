<aside class="sidebar" id="mainSidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-logo" wire:navigate>
            <div class="logo-icon">FM</div>
            <div class="logo-text-group" x-show="!collapsed" x-cloak>
                <span class="logo-text">{{ config('app.name') }}</span>
                <span class="logo-badge">{{ __('general.enterprise') }}</span>
            </div>
        </a>
    </div>
    <button class="sidebar-collapse-btn" @click="toggleSidebar()" title="{{ __('general.collapse_toggle') }}">
        <i class="bi bi-chevron-left" x-show="!collapsed" x-cloak></i>
        <i class="bi bi-chevron-right" x-show="collapsed" x-cloak></i>
    </button>

    @php
        $user = auth()->user();
        $allWorkspaces = $user->workspaces;
        $currentWs = $user->currentWorkspace;
        $activeSub = $currentWs ? $currentWs->owner()?->first()?->activeSubscription() : null;
        $currentPlan = $activeSub?->plan;
        $planName = $currentPlan?->name;
        $planFeats = $currentPlan ? $currentPlan->featureSlugs() : [];

        $feat = fn($s) => in_array($s, $planFeats);
    @endphp
    <div class="sidebar-workspace" x-data="{ wsOpen: false }">
        <button class="workspace-switcher" type="button" @click="wsOpen = !wsOpen" @click.away="wsOpen = false">
            <span class="workspace-avatar">{{ substr($currentWs?->name ?? 'W', 0, 1) }}</span>
            <span class="workspace-info" x-show="!collapsed" x-cloak>
                <span class="workspace-name">{{ $currentWs?->name ?? __('workspace.select') }}</span>
                <span class="workspace-role">{{ __('workspace.owner') }}</span>
                @if ($planName)
                    <span class="workspace-plan-badge"
                        style="font-size:10px;color:var(--accent);font-weight:600;display:block;margin-top:1px">{{ $planName }}</span>
                @endif
            </span>
            <i class="bi bi-chevron-down workspace-chevron" :class="wsOpen ? 'rotate-180' : ''" x-show="!collapsed"
                x-cloak></i>
        </button>
        <div class="workspace-dropdown" x-show="wsOpen" @click.away="wsOpen = false"
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak>
            @foreach ($allWorkspaces as $ws)
                <form method="POST" action="{{ route('workspace.switch', $ws) }}">
                    @csrf
                    <button type="submit" class="workspace-dropdown-item">
                        <span
                            class="ws-dot {{ $currentWs && $currentWs->id === $ws->id ? 'active-ws' : '' }}">{{ substr($ws->name, 0, 1) }}</span>
                        <span>{{ $ws->name }}</span>
                        @if ($currentWs && $currentWs->id === $ws->id)
                            <i class="bi bi-check-lg ws-check"></i>
                        @endif
                    </button>
                </form>
            @endforeach
            <hr class="workspace-dropdown-divider">
            @can('create', App\Models\Workspace::class)
                <a href="{{ route('settings.workspace.create') }}" class="workspace-dropdown-create" wire:navigate>
                    <i class="bi bi-plus-lg" style="font-size:10px;opacity:0.6"></i>
                    <span>{{ __('workspace.create_new') }}</span>
                </a>
            @endcan
        </div>
    </div>

    <div class="sidebar-search" @click="window.dispatchEvent(new CustomEvent('toggle-cmd-palette'))" role="button"
        tabindex="0">
        <div class="sidebar-search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="{{ __('general.search') }}..." readonly x-show="!collapsed" x-cloak>
            <span class="sidebar-search-hint" x-show="!collapsed" x-cloak>⌘K</span>
        </div>
    </div>

    <nav class="sidebar-body">
        {{-- Overview --}}
        <div class="sidebar-nav-section" x-data="{ open: true }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button"
                data-label="{{ __('general.overview') }}">
                <i class="bi bi-compass section-toggle-icon"></i>
                <span x-show="!collapsed" x-cloak>{{ __('general.overview') }}</span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed"
                    x-cloak></i>
            </button>
            <div x-show="open">
                <a href="{{ route('dashboard') }}"
                    class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" wire:navigate
                    data-label="{{ __('general.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('general.dashboard') }}</span>
                </a>
            </div>
        </div>

        @php
            $cTxn = $user->workspaceHasPermission('transaction.view');
            $cInc = $user->workspaceHasPermission('income.view');
            $cExp = $user->workspaceHasPermission('expense.view');
            $hasFin = $cTxn || $cInc || $cExp;
        @endphp
        @if ($hasFin)
            <div class="sidebar-nav-section" x-data="{ open: {{ request()->routeIs('transactions.*') || request()->routeIs('income.*') || request()->routeIs('expense.*') ? 'true' : 'false' }} }">
                <button class="sidebar-section-toggle" @click="open = !open" type="button"
                    data-label="{{ __('general.finances') }}">
                    <i class="bi bi-wallet2 section-toggle-icon"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('general.finances') }}</span>
                    <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed"
                        x-cloak></i>
                </button>
                <div x-show="open" style="display:none">
                    @if ($cTxn)
                        <a href="{{ route('transactions.index') }}"
                            class="sidebar-nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
                            wire:navigate data-label="{{ __('transactions.title') }}">
                            <i class="bi bi-arrow-left-right"></i>
                            <span x-show="!collapsed" x-cloak>{{ __('transactions.title') }}@if (!$feat('income_expense'))
                                    <x-status-badge domain="general" status="premium" set="bi" />
                                @endif
                            </span>
                        </a>
                    @endif
                    @if ($cInc)
                        <div x-data="{ incOpen: {{ request()->routeIs('income.index') || request()->routeIs('income.categories.*') ? 'true' : 'false' }} }">
                            <a href="{{ route('income.index') }}"
                                class="sidebar-nav-item sidebar-nav-parent {{ request()->routeIs('income.index') && !request()->routeIs('income.categories.*') ? 'active' : '' }}"
                                wire:navigate data-label="{{ __('general.income') }}">
                                <i class="bi bi-cash-stack"></i>
                                <span x-show="!collapsed" x-cloak>{{ __('general.income') }}@if (!$feat('income_expense'))
                                        <x-status-badge domain="general" status="premium" set="bi" />
                                    @endif
                                </span>
                                <i class="bi bi-chevron-down nav-parent-chevron" :class="incOpen ? '' : 'collapsed'"
                                    x-show="!collapsed" x-cloak @click.prevent.stop="incOpen = !incOpen"></i>
                            </a>
                            <div x-show="incOpen" x-collapse style="display: none;">
                                <a href="{{ route('income.categories.index') }}"
                                    class="sidebar-nav-item {{ request()->routeIs('income.categories.*') ? 'active' : '' }}"
                                    wire:navigate data-label="{{ __('income.categories') }}">
                                    <i class="bi bi-tags"></i>
                                    <span x-show="!collapsed" x-cloak>{{ __('income.categories') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif
                    @if ($cExp)
                        <div x-data="{ expOpen: {{ request()->routeIs('expense.index') || request()->routeIs('expense.categories.*') ? 'true' : 'false' }} }">
                            <a href="{{ route('expense.index') }}"
                                class="sidebar-nav-item sidebar-nav-parent {{ request()->routeIs('expense.index') && !request()->routeIs('expense.categories.*') ? 'active' : '' }}"
                                wire:navigate data-label="{{ __('general.expense') }}">
                                <i class="bi bi-cart"></i>
                                <span x-show="!collapsed" x-cloak>{{ __('general.expense') }}@if (!$feat('income_expense'))
                                        <x-status-badge domain="general" status="premium" set="bi" />
                                    @endif
                                </span>
                                <i class="bi bi-chevron-down nav-parent-chevron" :class="expOpen ? '' : 'collapsed'"
                                    x-show="!collapsed" x-cloak @click.prevent.stop="expOpen = !expOpen"></i>
                            </a>
                            <div x-show="expOpen" x-collapse style="display: none;">
                                <a href="{{ route('expense.categories.index') }}"
                                    class="sidebar-nav-item {{ request()->routeIs('expense.categories.*') ? 'active' : '' }}"
                                    wire:navigate data-label="{{ __('expense.categories') }}">
                                    <i class="bi bi-tags"></i>
                                    <span x-show="!collapsed" x-cloak>{{ __('expense.categories') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @php
            $cBud = $user->workspaceHasPermission('budget.view');
            $cGoal = $user->workspaceHasPermission('goal.view');
            $hasPlan = $cBud || $cGoal;
        @endphp
        @if ($hasPlan)
            <div class="sidebar-nav-section" x-data="{ open: {{ request()->routeIs('budget.*') || request()->routeIs('goal.*') ? 'true' : 'false' }} }">
                <button class="sidebar-section-toggle" @click="open = !open" type="button"
                    data-label="{{ __('general.planning') }}">
                    <i class="bi bi-clipboard-check section-toggle-icon"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('general.planning') }}</span>
                    <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed"
                        x-cloak></i>
                </button>
                <div x-show="open" style="display:none">
                    @if ($cBud)
                        <div x-data="{ budOpen: {{ request()->routeIs('budget.*') ? 'true' : 'false' }} }">
                            <a href="{{ route('budget.index') }}"
                                class="sidebar-nav-item sidebar-nav-parent {{ request()->routeIs('budget.index') && !request()->routeIs('budget.categories') ? 'active' : '' }}"
                                wire:navigate data-label="{{ __('general.budget') }}">
                                <i class="bi bi-calculator-fill"></i>
                                <span x-show="!collapsed" x-cloak>{{ __('general.budget') }}@if (!$feat('budget'))
                                        <x-status-badge domain="general" status="premium" set="bi" />
                                    @endif
                                </span>
                                <i class="bi bi-chevron-down nav-parent-chevron" :class="budOpen ? '' : 'collapsed'"
                                    x-show="!collapsed" x-cloak @click.prevent.stop="budOpen = !budOpen"></i>
                            </a>
                            <div x-show="budOpen" x-collapse style="display: none;">
                                <a href="{{ route('budget.categories') }}"
                                    class="sidebar-nav-item {{ request()->routeIs('budget.categories') ? 'active' : '' }}"
                                    wire:navigate data-label="{{ __('budget.categories') }}">
                                    <i class="bi bi-tags"></i>
                                    <span x-show="!collapsed" x-cloak>{{ __('budget.categories') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif
                    @if ($cGoal)
                        <a href="{{ route('goal.index') }}"
                            class="sidebar-nav-item {{ request()->routeIs('goal.*') ? 'active' : '' }}" wire:navigate
                            data-label="{{ __('general.goal') }}">
                            <i class="bi bi-flag-fill"></i>
                            <span x-show="!collapsed" x-cloak>{{ __('general.goal') }}@if (!$feat('goals'))
                                    <x-status-badge domain="general" status="premium" set="bi" />
                                @endif
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        @php
            $cAst = $user->workspaceHasPermission('asset.view');
            $cDebt = $user->workspaceHasPermission('debt.view');
            $hasAst = $cAst || $cDebt;
        @endphp
        @if ($hasAst)
            <div class="sidebar-nav-section" x-data="{ open: {{ request()->routeIs('asset.*') || request()->routeIs('debt.*') ? 'true' : 'false' }} }">
                <button class="sidebar-section-toggle" @click="open = !open" type="button"
                    data-label="{{ __('general.assets_liabilities') }}">
                    <i class="bi bi-diagram-3 section-toggle-icon"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('general.assets_liabilities') }}</span>
                    <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed"
                        x-cloak></i>
                </button>
                <div x-show="open" style="display:none">
                    @if ($cAst)
                        <a href="{{ route('asset.index') }}"
                            class="sidebar-nav-item {{ request()->routeIs('asset.*') ? 'active' : '' }}" wire:navigate
                            data-label="{{ __('general.asset') }}">
                            <i class="bi bi-pie-chart-fill"></i>
                            <span x-show="!collapsed" x-cloak>{{ __('general.asset') }}@if (!$feat('debt'))
                                    <x-status-badge domain="general" status="premium" set="bi" />
                                @endif
                            </span>
                        </a>
                    @endif
                    @if ($cDebt)
                        <a href="{{ route('debt.index') }}"
                            class="sidebar-nav-item {{ request()->routeIs('debt.*') ? 'active' : '' }}" wire:navigate
                            data-label="{{ __('general.debt') }}">
                            <i class="bi bi-credit-card-2-front"></i>
                            <span x-show="!collapsed" x-cloak>{{ __('general.debt') }}@if (!$feat('debt'))
                                    <x-status-badge domain="general" status="premium" set="bi" />
                                @endif
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        @php
            $cZak = $user->workspaceHasPermission('zakat.view');
            $cRpt = $user->workspaceHasPermission('report.view');
            $cAct = $user->workspaceHasPermission('activity-log.view');
            $hasMore = $cZak || $cRpt || $cAct;
        @endphp
        @if ($hasMore)
            <div class="sidebar-nav-section" x-data="{ open: {{ request()->routeIs('zakat.*') || request()->routeIs('report.*') || request()->routeIs('activity.logs') ? 'true' : 'false' }} }">
                <button class="sidebar-section-toggle" @click="open = !open" type="button"
                    data-label="{{ __('general.more') }}">
                    <i class="bi bi-three-dots-vertical section-toggle-icon"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('general.more') }}</span>
                    <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed"
                        x-cloak></i>
                </button>
                <div x-show="open" style="display:none">
                    @if ($cZak)
                        <a href="{{ route('zakat.calculator') }}"
                            class="sidebar-nav-item {{ request()->routeIs('zakat.*') ? 'active' : '' }}" wire:navigate
                            data-label="{{ __('general.zakat') }}">
                            <i class="bi bi-heart-fill"></i>
                            <span x-show="!collapsed" x-cloak>{{ __('general.zakat') }}@if (!$feat('zakat'))
                                    <x-status-badge domain="general" status="premium" set="bi" />
                                @endif
                            </span>
                        </a>
                    @endif
                    @if ($cRpt)
                        <a href="{{ route('report.index') }}"
                            class="sidebar-nav-item {{ request()->routeIs('report.*') ? 'active' : '' }}"
                            wire:navigate data-label="{{ __('general.report') }}">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i>
                            <span x-show="!collapsed" x-cloak>{{ __('general.report') }}@if (!$feat('reports'))
                                    <x-status-badge domain="general" status="premium" set="bi" />
                                @endif
                            </span>
                        </a>
                    @endif
                    @if ($cAct)
                        <a href="{{ route('activity.logs') }}"
                            class="sidebar-nav-item {{ request()->routeIs('activity.logs') ? 'active' : '' }}"
                            wire:navigate data-label="{{ __('settings.activity_log') }}">
                            <i class="bi bi-clock-history"></i>
                            <span x-show="!collapsed" x-cloak>{{ __('settings.activity_log') }}@if (!$feat('activity_logs'))
                                    <x-status-badge domain="general" status="premium" set="bi" />
                                @endif
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <hr class="sidebar-nav-divider" x-show="!collapsed" x-cloak>

        {{-- Notifications --}}
        <div class="sidebar-nav-section" x-data="{ open: {{ request()->routeIs('notifications.*') ? 'true' : 'false' }} }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button"
                data-label="{{ __('general.notifications') }}">
                <i class="bi bi-bell section-toggle-icon"></i>
                <span x-show="!collapsed" x-cloak>{{ __('general.notifications') }}</span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed"
                    x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <a href="{{ route('notifications.index') }}"
                    class="sidebar-nav-item {{ request()->routeIs('notifications.index') ? 'active' : '' }}"
                    wire:navigate data-label="{{ __('general.notifications') }}">
                    <i class="bi bi-bell"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('general.notifications') }}</span>
                </a>
                <a href="{{ route('notifications.settings') }}"
                    class="sidebar-nav-item {{ request()->routeIs('notifications.settings') ? 'active' : '' }}"
                    wire:navigate data-label="{{ __('notifications.preferences') }}">
                    <i class="bi bi-sliders2"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('notifications.preferences') }}</span>
                </a>
            </div>
        </div>

        {{-- Account --}}
        <div class="sidebar-nav-section" x-data="{ open: {{ request()->routeIs('settings.*') || request()->routeIs('two-factor.*') || request()->routeIs('workspace.*') ? 'true' : 'false' }} }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button"
                data-label="{{ __('general.account') }}">
                <i class="bi bi-person-circle section-toggle-icon"></i>
                <span x-show="!collapsed" x-cloak>{{ __('general.account') }}</span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed"
                    x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <a href="{{ route('settings.account.profile') }}"
                    class="sidebar-nav-item {{ request()->routeIs('settings.account.profile') ? 'active' : '' }}"
                    wire:navigate data-label="{{ __('general.profile') }}">
                    <i class="bi bi-person-fill"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('general.profile') }}</span>
                </a>
                <a href="{{ route('billing.subscriptions') }}"
                    class="sidebar-nav-item {{ request()->routeIs('billing.subscriptions') ? 'active' : '' }}"
                    wire:navigate data-label="{{ __('settings.subscriptions') }}">
                    <i class="bi bi-credit-card-fill"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('settings.subscriptions') }}</span>
                </a>
                <a href="{{ route('billing.invoices.index') }}"
                    class="sidebar-nav-item {{ request()->routeIs('billing.invoices.*') ? 'active' : '' }}"
                    wire:navigate data-label="{{ __('settings.invoices') }}">
                    <i class="bi bi-receipt"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('settings.invoices') }}</span>
                </a>
                <a href="{{ route('settings.account.index') }}"
                    class="sidebar-nav-item {{ request()->routeIs('settings.account*') ? 'active' : '' }}"
                    wire:navigate data-label="{{ __('settings.settings') }}">
                    <i class="bi bi-sliders2"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('settings.settings') }}</span>
                </a>
                @if ($user->workspaceHasPermission('workspace-setting.view') || $user->isWorkspaceAdmin())
                    <a href="{{ route('settings.workspace.index', ['tab' => 'general']) }}"
                        class="sidebar-nav-item {{ request()->routeIs('settings.workspace.index') ? 'active' : '' }}"
                        wire:navigate data-label="{{ __('workspace.settings') }}">
                        <i class="bi bi-building"></i>
                        <span x-show="!collapsed" x-cloak>{{ __('workspace.settings') }}</span>
                    </a>
                @endif
                <a href="{{ route('settings.account.developer.index') }}"
                    class="sidebar-nav-item {{ request()->routeIs('settings.account.developer*') ? 'active' : '' }}"
                    wire:navigate data-label="{{ __('developer.api_tokens') }}">
                    <i class="bi bi-code-slash"></i>
                    <span x-show="!collapsed" x-cloak>{{ __('developer.api_tokens') }}</span>
                    @if (!$feat('api_access'))
                        <x-status-badge domain="general" status="premium" set="bi" />
                    @endif
                </a>
            </div>
        </div>
    </nav>

    <hr class="sidebar-nav-divider" x-show="!collapsed" x-cloak>

    <nav class="sidebar-nav" x-show="!collapsed" x-cloak>
        <a href="{{ route('api.documentation') }}" class="sidebar-nav-item" target="_blank"
            data-label="{{ __('general.api_documentation') }}">
            <i class="bi bi-file-earmark-text"></i>
            <span>{{ __('general.api_documentation') }}@if (!$feat('api_access'))
                    <x-status-badge domain="general" status="premium" set="bi" />
                @endif
            </span>
        </a>
    </nav>

    <div class="sidebar-footer" x-data="profileDropdown()" @profile-dropdown-close.window="open = false">
        <button class="sidebar-nav-item profile-item" @click="toggle()" data-label="{{ __('general.profile') }}">
            <div class="profile-avatar">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="profile-info" x-show="!collapsed" x-cloak>
                <span class="profile-name">{{ auth()->user()->name }}</span>
                <span class="profile-email">{{ auth()->user()->email }}</span>
            </div>
            <i class="bi bi-chevron-up profile-chevron" :class="open && !collapsed ? 'rotate-180' : ''"
                x-show="!collapsed" x-cloak></i>
        </button>

        @include('layouts.partials._profile-dropdown-menu')
    </div>
</aside>

<div class="sidebar-overlay" @click="closeSidebarMobile()"></div>
