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
            <button class="topbar-btn position-relative" type="button" @click="toggle()" aria-label="{{ __('admin.notifications') }}">
                <i class="bi bi-bell"></i>
                <template x-if="unreadCount > 0">
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" x-text="unreadCount" style="font-size:0.6rem;"></span>
                </template>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0" x-show="open" style="display:none;width:360px;max-height:480px;overflow-y:auto;" x-transition>
                <div class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 bg-light">
                    <strong>{{ __('admin.notifications') }}</strong>
                    <template x-if="unreadCount > 0">
                        <button class="btn btn-sm btn-link p-0 text-decoration-none" type="button" @click="markAllRead">
                            <i class="bi bi-check-all me-1"></i>{{ __('admin.mark_all_read') }}
                        </button>
                    </template>
                </div>
                <div class="list-group list-group-flush">
                    <template x-for="note in notifications" :key="note.id">
                        <div class="list-group-item list-group-item-action px-3 py-2" :class="{ 'bg-light': !note.is_read }">
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge rounded-circle p-1 mt-1 flex-shrink-0" :class="'bg-' + iconColor(note.type)">
                                    <i class="bi" :class="iconClass(note.type)" style="font-size:0.7rem;"></i>
                                </span>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex justify-content-between">
                                        <small class="fw-bold" x-text="note.title_en" :class="{ 'text-muted': note.is_read }"></small>
                                        <small class="text-muted text-nowrap ms-1" x-text="timeAgo(note.created_at)"></small>
                                    </div>
                                    <p class="mb-0 small text-muted text-truncate" x-text="note.message_en"></p>
                                </div>
                                <template x-if="!note.is_read">
                                    <button class="btn btn-sm btn-link p-0 flex-shrink-0" type="button" @click.stop="markRead(note.id)">
                                        <i class="bi bi-check text-primary"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="notifications.length === 0">
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-bell-slash d-block mb-1" style="font-size:1.5rem;"></i>
                            <small>{{ __('admin.no_notifications') }}</small>
                        </div>
                    </template>
                </div>
                <a class="dropdown-item text-center small py-2 border-top" href="{{ route('super.admin.notifications.index') }}">
                    {{ __('admin.view_all') }}
                    <i class="bi bi-arrow-right ms-1"></i>
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
