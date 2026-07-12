<header class="topbar">
    <div class="topbar-left">
        <button class="topbar-hamburger d-lg-none" @click="toggleSidebarMobile()" type="button" aria-label="{{ __('general.toggle_sidebar') }}">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-search-wrapper" @click="window.dispatchEvent(new CustomEvent('toggle-cmd-palette'))" role="button" tabindex="0">
            <i class="bi bi-search search-icon"></i>
            <span class="search-placeholder">{{ __('general.search') }}</span>
            <span class="search-hint">⌘K</span>
        </div>
    </div>

    <div class="topbar-right">
        @php
            $allWorkspaces = auth()->user()->workspaces;
            $currentWs = auth()->user()->currentWorkspace;
        @endphp
        @if($allWorkspaces->count() > 1)
            <div class="dropdown" x-data="{ wsOpen: false }" @click.away="wsOpen = false">
                <button class="topbar-btn" type="button" @click="wsOpen = !wsOpen" aria-label="{{ __('workspace.switcher') }}" title="{{ $currentWs?->name }}">
                    <i class="bi bi-layers"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" x-show="wsOpen" style="display:none;min-width:200px" x-transition>
                    @foreach($allWorkspaces as $ws)
                        <li>
                            <form method="POST" action="{{ route('workspace.switch', $ws) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2"
                                    @if($currentWs && $currentWs->id === $ws->id) style="background:rgba(21,183,108,0.08);font-weight:600" @endif>
                                    <span style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;border-radius:6px;background:var(--bg-subtle);color:var(--text);font-size:11px;font-weight:700;flex-shrink:0">
                                        {{ substr($ws->name, 0, 1) }}
                                    </span>
                                    <span style="flex:1;font-size:13px">{{ $ws->name }}</span>
                                    @if($currentWs && $currentWs->id === $ws->id)
                                        <i class="bi bi-check-lg" style="color:var(--success);font-size:14px"></i>
                                    @endif
                                </button>
                            </form>
                        </li>
                    @endforeach
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('settings.index') }}" wire:navigate>
                            <i class="bi bi-gear"></i>
                            <span>{{ __('workspace.manage') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif

        <x-language-switcher variant="dropdown" />

        <button class="topbar-btn" data-theme-toggle @click="toggleTheme()" type="button" aria-label="{{ __('settings.theme') }}" title="{{ __('settings.theme') }}">
            <i class="bi {{ session('theme', 'light') === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill' }}"></i>
        </button>

        <div class="dropdown" x-data="notificationDropdown()" @click.away="notifOpen = false">
            <button class="topbar-btn" type="button" @click="notifOpen = !notifOpen" aria-label="{{ __('general.notifications') }}">
                <i class="bi bi-bell-fill"></i>
                <span class="badge-dot" x-show="unreadCount > 0" style="display:none"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end" x-show="notifOpen" style="display:none;width:min(360px,calc(100vw - 32px));max-height:480px;overflow-y:auto" x-transition>
                <div style="padding:12px 14px 10px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                    <strong style="font-size:13px;font-weight:600;color:var(--text)">{{ __('general.notifications') }} (<span x-text="unreadCount">0</span>)</strong>
                    <button x-show="unreadCount > 0" @click="markAllRead" class="btn-text-link">{{ __('general.mark_all_read') }}</button>
                </div>
                <template x-if="notifications.length === 0">
                    <div class="text-center py-4" style="color:var(--text-muted);font-size:13px">
                        <i class="bi bi-bell" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        {{ __('general.no_notifications') }}
                    </div>
                </template>
                <template x-for="n in notifications" :key="n.id">
                    <div class="notification-item" :style="n.is_read ? '' : 'background:var(--bg-subtle)'">
                        <template x-if="n.type === 'budget_exceeded' || n.type === 'budget_nearing_limit'">
                            <div class="notif-icon" style="background:var(--danger-light);color:var(--danger)"><i class="bi bi-exclamation-triangle"></i></div>
                        </template>
                        <template x-if="n.type === 'debt_reminder'">
                            <div class="notif-icon" style="background:var(--warning-light);color:var(--warning)"><i class="bi bi-credit-card-2-front"></i></div>
                        </template>
                        <template x-if="n.type === 'goal_achieved' || n.type === 'goal_milestone'">
                            <div class="notif-icon" style="background:var(--success-light);color:var(--success)"><i class="bi bi-flag"></i></div>
                        </template>
                        <template x-if="n.type === 'goal_deadline'">
                            <div class="notif-icon" style="background:var(--info-light);color:var(--info)"><i class="bi bi-clock"></i></div>
                        </template>
                        <template x-if="n.type === 'zakat_reminder'">
                            <div class="notif-icon" style="background:rgba(139,92,246,0.1);color:var(--sa-indigo)"><i class="bi bi-heart"></i></div>
                        </template>
                        <template x-if="n.type === 'role_changed'">
                            <div class="notif-icon" style="background:var(--warning-light);color:var(--warning)"><i class="bi bi-shield-check"></i></div>
                        </template>
                        <template x-if="!['budget_exceeded','budget_nearing_limit','debt_reminder','goal_achieved','goal_milestone','goal_deadline','zakat_reminder','role_changed'].includes(n.type)">
                            <div class="notif-icon" style="background:var(--info-light);color:var(--info)"><i class="bi bi-info-circle"></i></div>
                        </template>
                        <div class="notif-content">
                            <div class="notif-text" x-text="n.title"></div>
                            <div class="notif-time" x-text="n.time"></div>
                        </div>
                        <button x-show="!n.is_read" @click="markRead(n.id)" class="btn-text-link" style="flex-shrink:0;font-size:11px">✓</button>
                    </div>
                </template>
            </div>
        </div>

        <div class="dropdown" x-data="{ open: false }" @click.away="open = false">
            <button class="topbar-dropdown-btn" type="button" @click="open = !open" :class="{ show: open }" aria-label="{{ auth()->user()->name }}">
                <div class="user-avatar-mini" style="background:var(--accent);color:#0F172A">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <span class="user-name">{{ auth()->user()->name }}</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" x-show="open" style="display:none;min-width:220px" x-transition>
                <li class="user-menu-header">
                    <div class="user-avatar" style="background:var(--accent);color:#0F172A">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="user-details">
                        <div class="user-display-name">{{ auth()->user()->name }}</div>
                        <div class="user-email">{{ auth()->user()->email }}</div>
                    </div>
                </li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('dashboard') }}" wire:navigate><i class="bi bi-grid-1x2-fill"></i><span>{{ __('general.dashboard') }}</span></a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('account.profile') }}" wire:navigate><i class="bi bi-person"></i><span>{{ __('general.profile') }}</span></a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('settings.index') }}" wire:navigate><i class="bi bi-gear"></i><span>{{ __('general.settings') }}</span></a></li>
                @auth
                    @if(auth()->user()->hasRole('super_admin'))
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('super.admin.dashboard') }}" wire:navigate><i class="bi bi-shield-shaded"></i><span>{{ __('super-admin.enter_panel') }}</span></a></li>
                    @endif
                @endauth
                <li><hr class="dropdown-divider"></li>
                <li class="user-menu-item-danger">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2"><i class="bi bi-box-arrow-right"></i><span>{{ __('general.logout') }}</span></button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

@push('scripts')
<script>
function notificationDropdown() {
    return {
        notifOpen: false,
        notifications: [],
        unreadCount: 0,
        init() {
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 30000);
        },
        fetchNotifications() {
            fetch('{{ route('notifications.index') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(data => {
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                })
                .catch(() => {});
        },
        markRead(id) {
            fetch('{{ url('notifications') }}/' + id + '/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(() => this.fetchNotifications())
                .catch(() => {});
        },
        markAllRead() {
            fetch('{{ route('notifications.mark-all-read') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(() => this.fetchNotifications())
                .catch(() => {});
        }
    };
}
</script>
@endpush
