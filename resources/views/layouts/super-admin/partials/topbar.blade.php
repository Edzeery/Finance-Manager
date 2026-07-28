<header class="topbar">
    <div class="topbar-left">
        <button class="topbar-hamburger d-lg-none" @click="toggleSidebarMobile()" type="button" aria-label="{{ __('general.toggle_sidebar') }}">
            <i class="bi bi-list"></i>
        </button>

        <span class="admin-indicator d-none d-sm-inline-flex">
            <i class="bi bi-shield-fill-check"></i>
            <span>{{ __('super-admin.admin_panel') }}</span>
        </span>
    </div>

    <div class="topbar-right">
        {{-- Command Palette Trigger --}}
        <button class="topbar-btn" @click="window.dispatchEvent(new CustomEvent('toggle-cmd-palette'))" type="button" aria-label="{{ __('general.search') }}" title="{{ __('general.search') }} (⌘K)">
            <i class="bi bi-command"></i>
        </button>

        <x-language-switcher variant="dropdown" :showCode="false" />

        {{-- Admin Notifications --}}
        <div class="dropdown" x-data="adminNotificationDropdown()" @click.away="open = false" @keydown.escape.window="open = false">
            <button class="topbar-btn position-relative" type="button" @click="toggle()" aria-label="{{ __('notifications.page_title') }}">
                <i class="bi bi-bell-fill"></i>
                <template x-if="unreadCount > 0">
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" x-text="unreadCount" style="font-size:0.6rem;"></span>
                </template>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0" x-show="open" style="display:none;width:min(380px,calc(100vw - 32px));max-height:480px;overflow-y:auto;" x-transition>
                <div style="padding:12px 14px 10px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                    <strong style="font-size:13px;font-weight:600;color:var(--text)">{{ __('notifications.page_title') }} <span style="font-weight:400;color:var(--text-muted)">(<span x-text="unreadCount">0</span> {{ __('general.unread') }})</span></strong>
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
                        <i class="bi bi-bell-slash" style="font-size:28px;display:block;margin-bottom:8px;opacity:0.4"></i>
                        {{ __('notifications.empty') }}
                    </div>
                </template>

                {{-- Notification list --}}
                <template x-if="!loading">
                    <div>
                        <template x-for="n in notifications" :key="n.id">
                            <div class="d-flex align-items-start gap-2 px-3 py-2" :style="n.is_read ? '' : 'background:var(--bg-subtle)'" style="border-bottom:1px solid var(--border)">
                                <div :style="'background:' + iconBg(n.type) + ';color:' + iconColor(n.type) + ';flex-shrink:0;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center'">
                                    <i class="bi" :class="iconClass(n.type)"></i>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="fw-bold" x-text="n.title" :style="n.is_read ? 'color:var(--text-muted)' : 'color:var(--text)'" style="font-size:13px"></small>
                                        <small class="text-nowrap ms-1" style="font-size:11px;color:var(--text-muted)" x-text="n.time"></small>
                                    </div>
                                    <p class="mb-0 text-truncate" style="font-size:12px;color:var(--text-muted);margin-top:1px" x-text="n.message"></p>
                                </div>
                                <div class="d-flex flex-column align-items-center gap-1" style="flex-shrink:0">
                                    <button x-show="!n.is_read" @click.stop="markRead(n.id)" class="btn p-0" style="color:var(--accent);background:none;border:none;font-size:14px;line-height:1" title="{{ __('notifications.mark_read') }}">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                    <button @click.stop="deleteNotif(n.id)" class="btn p-0" style="color:var(--text-muted);background:none;border:none;font-size:12px;line-height:1" title="{{ __('general.delete') }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <a class="dropdown-item text-center small py-2 border-top" href="{{ route('super.admin.notifications.index') }}" style="font-size:12px;color:var(--accent);text-decoration:none;font-weight:500">
                    {{ __('notifications.view_all') }}
                    <i class="bi bi-arrow-end ms-1" style="font-size:10px"></i>
                </a>
            </div>
        </div>

        {{-- Theme Switcher --}}
        <button class="topbar-btn" data-theme-toggle @click="toggleTheme()" type="button" aria-label="{{ __('settings.theme') }}" title="{{ __('settings.theme') }}">
            <i class="bi {{ session('theme', 'light') === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill' }}"></i>
        </button>

        {{-- User Dropdown --}}
        <div class="dropdown" x-data="{ saUserOpen: false }" @click.away="saUserOpen = false">
            <button class="topbar-dropdown-btn" type="button" @click="saUserOpen = !saUserOpen" :class="{ show: saUserOpen }">
                <div class="user-avatar-mini" style="background:linear-gradient(135deg,#6366F1,#8B5CF6)">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <span class="user-name d-none d-md-inline">{{ auth()->user()->name }}</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" x-show="saUserOpen" style="display:none;width:260px" x-transition>
                <div class="user-menu-header">
                    <div class="user-avatar" style="background:linear-gradient(135deg,#6366F1,#8B5CF6)">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="user-details">
                        <div class="user-display-name">{{ auth()->user()->name }}</div>
                        <div class="user-email">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <a class="dropdown-item" href="{{ route('super.admin.dashboard') }}" wire:navigate>
                    <i class="bi bi-shield-shaded"></i>{{ __('super-admin.dashboard') }}
                </a>
                <a class="dropdown-item" href="{{ route('super.admin.account.profile') }}" wire:navigate>
                    <i class="bi bi-person"></i>{{ __('general.profile') }}
                </a>
                <a class="dropdown-item" href="{{ route('super.admin.settings.index') }}" wire:navigate>
                    <i class="bi bi-gear"></i>{{ __('super-admin.settings') }}
                </a>
                <div class="dropdown-divider"></div>
                <li class="user-menu-item-danger">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-box-arrow-right"></i>{{ __('general.logout') }}
                        </button>
                    </form>
                </li>
            </div>
        </div>
    </div>
</header>
