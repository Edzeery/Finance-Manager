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
                                    @if($currentWs && $currentWs->id === $ws->id) style="background:var(--accent-light);font-weight:600" @endif>
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
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('settings.workspace.index') }}" wire:navigate>
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
            <div class="dropdown-menu dropdown-menu-end" x-show="notifOpen" style="display:none;width:min(380px,calc(100vw - 32px));max-height:480px;overflow-y:auto" x-transition>
                <div style="padding:12px 14px 10px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                    <strong style="font-size:13px;font-weight:600;color:var(--text)">{{ __('general.notifications') }} <span style="font-weight:400;color:var(--text-muted)">(<span x-text="unreadCount">0</span> {{ __('general.unread') }})</span></strong>
                    <button x-show="unreadCount > 0" @click="markAllRead" class="btn-text-link" style="font-size:12px">{{ __('notifications.mark_all_read') }}</button>
                </div>

                {{-- Loading skeleton --}}
                <template x-if="loading">
                    <div class="p-3">
                        <template x-for="i in 3" :key="i">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <div style="width:32px;height:32px;border-radius:8px;background:var(--bg-subtle);flex-shrink:0"></div>
                                <div style="flex:1">
                                    <div style="height:12px;width:70%;background:var(--bg-subtle);border-radius:4px;margin-bottom:6px"></div>
                                    <div style="height:10px;width:90%;background:var(--bg-subtle);border-radius:4px"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Empty state --}}
                <template x-if="!loading && notifications.length === 0">
                    <div class="text-center py-4" style="color:var(--text-muted);font-size:13px">
                        <i class="bi bi-bell" style="font-size:28px;display:block;margin-bottom:8px;opacity:0.4"></i>
                        {{ __('general.no_notifications') }}
                    </div>
                </template>

                {{-- Notification list --}}
                <template x-if="!loading">
                    <div>
                        <template x-for="n in notifications" :key="n.id">
                            <div class="d-flex align-items-start gap-2 px-3 py-2" :style="n.is_read ? '' : 'background:var(--bg-subtle)'" style="border-bottom:1px solid var(--border)">
                                <div class="notif-icon" :style="'background:' + iconBg(n.type) + ';color:' + iconColor(n.type)">
                                    <i class="bi" :class="iconClass(n.type)"></i>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:13px;font-weight:500;color:var(--text)" x-text="n.title"></div>
                                    <div style="font-size:12px;color:var(--text-muted);margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" x-text="n.message"></div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px" x-text="n.time"></div>
                                </div>
                                <div class="d-flex flex-column align-items-center gap-1" style="flex-shrink:0">
                                    <button x-show="!n.is_read" @click="markRead(n.id)" class="btn p-0" style="color:var(--accent);background:none;border:none;font-size:14px;line-height:1" title="{{ __('notifications.mark_read') }}">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                    <button @click="deleteNotif(n.id, $el)" class="btn p-0" style="color:var(--text-muted);background:none;border:none;font-size:12px;line-height:1" title="{{ __('general.delete') }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Footer --}}
                <div style="border-top:1px solid var(--border);padding:8px;text-align:center">
                    <a href="{{ route('notifications.index') }}" style="font-size:12px;color:var(--accent);text-decoration:none;font-weight:500">
                        {{ __('notifications.view_all') }} <i class="bi bi-arrow-end" style="font-size:10px"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="dropdown" x-data="{ open: false }" @click.away="open = false">
            <button class="topbar-dropdown-btn" type="button" @click="open = !open" :class="{ show: open }" aria-label="{{ auth()->user()->name }}">
                <div class="user-avatar-mini" style="background:var(--accent);color:var(--primary)">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <span class="user-name">{{ auth()->user()->name }}</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" x-show="open" style="display:none;min-width:220px" x-transition>
                <li class="user-menu-header">
                    <div class="user-avatar" style="background:var(--accent);color:var(--primary)">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="user-details">
                        <div class="user-display-name">{{ auth()->user()->name }}</div>
                        <div class="user-email">{{ auth()->user()->email }}</div>
                    </div>
                </li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('dashboard') }}" wire:navigate><i class="bi bi-grid-1x2-fill"></i><span>{{ __('general.dashboard') }}</span></a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('settings.account.profile') }}" wire:navigate><i class="bi bi-person"></i><span>{{ __('general.profile') }}</span></a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('settings.workspace.index') }}" wire:navigate><i class="bi bi-gear"></i><span>{{ __('general.settings') }}</span></a></li>
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
        loading: true,
        iconMap: {
            budget_exceeded:       { bg: 'rgba(239,68,68,0.1)',  color: 'var(--danger)',   icon: 'bi-exclamation-triangle' },
            budget_nearing_limit:  { bg: 'rgba(245,158,11,0.1)', color: 'var(--warning)',  icon: 'bi-exclamation-circle' },
            debt_reminder:         { bg: 'rgba(245,158,11,0.1)', color: 'var(--warning)',  icon: 'bi-credit-card-2-front' },
            goal_achieved:         { bg: 'rgba(34,197,94,0.1)',  color: 'var(--success)',  icon: 'bi-flag-fill' },
            goal_milestone:        { bg: 'rgba(34,197,94,0.1)',  color: 'var(--success)',  icon: 'bi-flag' },
            goal_deadline:         { bg: 'rgba(59,130,246,0.1)', color: 'var(--info)',     icon: 'bi-clock-history' },
            zakat_reminder:        { bg: 'rgba(139,92,246,0.1)', color: 'var(--sa-indigo)', icon: 'bi-heart-fill' },
            zakat_approaching:     { bg: 'rgba(99,102,241,0.1)', color: '#6366F1',         icon: 'bi-hourglass-split' },
            login_new_device:      { bg: 'rgba(59,130,246,0.1)', color: 'var(--info)',     icon: 'bi-phone' },
            login_suspicious:      { bg: 'rgba(239,68,68,0.1)',  color: 'var(--danger)',   icon: 'bi-shield-exclamation' },
            password_changed:      { bg: 'rgba(245,158,11,0.1)', color: 'var(--warning)',  icon: 'bi-key' },
            two_factor_enabled:    { bg: 'rgba(34,197,94,0.1)',  color: 'var(--success)',  icon: 'bi-shield-lock' },
            two_factor_disabled:   { bg: 'rgba(239,68,68,0.1)',  color: 'var(--danger)',   icon: 'bi-shield-x' },
            session_revoked:       { bg: 'rgba(249,115,22,0.1)', color: '#F97316',         icon: 'bi-box-arrow-right' },
            email_changed:         { bg: 'rgba(59,130,246,0.1)', color: 'var(--info)',     icon: 'bi-envelope-at' },
            workspace_member_login:{ bg: 'rgba(34,197,94,0.1)',  color: 'var(--success)',  icon: 'bi-person-check' },
            role_changed:          { bg: 'rgba(245,158,11,0.1)', color: 'var(--warning)',  icon: 'bi-shield-check' },
        },
        _icon(type, key) { return (this.iconMap[type] || { bg: 'rgba(59,130,246,0.1)', color: 'var(--info)', icon: 'bi-info-circle' })[key]; },
        iconBg(type)    { return this._icon(type, 'bg'); },
        iconColor(type) { return this._icon(type, 'color'); },
        iconClass(type) { return this._icon(type, 'icon'); },
        init() {
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 60000);
        },
        fetchNotifications() {
            fetch('{{ route('notifications.index') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(data => {
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                    this.loading = false;
                })
                .catch(() => { this.loading = false; });
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
        },
        deleteNotif(id, el) {
            if (!confirm('{{ __("notifications.delete_confirm") }}')) return;
            fetch('{{ url('notifications') }}/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                    this.unreadCount = this.notifications.filter(n => !n.is_read).length;
                })
                .catch(() => {});
        }
    };
}
</script>
@endpush
