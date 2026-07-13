<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" data-theme="{{ session('theme', 'light') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name') . ' - ' . __('super-admin.super_dashboard') }}">
    <meta name="theme-color" content="#6366F1">
    <meta name="theme-switch-url" content="{{ route('theme.switch') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <title>{{ $title ?? __('super-admin.super_dashboard') }} - {{ config('app.name', 'Finance Manager') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>

<body class="super-admin-layout" x-data="superAdminLayout" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
    @include('layouts.super-admin.partials.sidebar')

    <div class="main-content">
        @include('layouts.super-admin.partials.topbar')

        <main x-data="{ loaded: true }">
            <div class="page-header">
                @isset($breadcrumb)
                    <div class="mb-2">
                        <x-breadcrumb :items="$breadcrumb" />
                    </div>
                @endisset
                <h1>{{ $pageTitle ?? '' }}</h1>
                @isset($pageDescription)
                    <p>{{ $pageDescription }}</p>
                @endisset
            </div>

            <div class="page-content">
                {{ $slot }}
            </div>
        </main>
    </div>

    <x-command-palette :items="[
        [
            'title' => __('super-admin.dashboard'),
            'description' => __('super-admin.overview'),
            'icon' => 'bi bi-grid-1x2-fill',
            'url' => route('super.admin.dashboard'),
        ],
        [
            'title' => __('super-admin.users'),
            'description' => __('super-admin.manage_users'),
            'icon' => 'bi bi-people',
            'url' => route('super.admin.users.index'),
        ],
        [
            'title' => __('super-admin.workspaces'),
            'description' => __('super-admin.manage_workspaces'),
            'icon' => 'bi bi-layers',
            'url' => route('super.admin.workspaces.index'),
        ],
        [
            'title' => __('super-admin.subscriptions'),
            'description' => __('super-admin.manage_subscriptions'),
            'icon' => 'bi bi-credit-card',
            'url' => route('super.admin.subscriptions.index'),
        ],
        [
            'title' => __('super-admin.plans'),
            'description' => __('super-admin.manage_plans'),
            'icon' => 'bi bi-box',
            'url' => route('super.admin.plans.index'),
        ],
        [
            'title' => __('super-admin.invoices'),
            'description' => __('super-admin.view_invoices'),
            'icon' => 'bi bi-receipt',
            'url' => route('super.admin.invoices.index'),
        ],
        [
            'title' => __('super-admin.payments'),
            'description' => __('super-admin.view_payments'),
            'icon' => 'bi bi-currency-dollar',
            'url' => route('super.admin.payments.index'),
        ],
        [
            'title' => __('super-admin.payment_methods'),
            'description' => __('super-admin.payment_methods_desc'),
            'icon' => 'bi bi-credit-card-2-front',
            'url' => route('super.admin.payment-methods.index'),
        ],
        [
            'title' => __('super-admin.coupons'),
            'description' => __('super-admin.manage_coupons'),
            'icon' => 'bi bi-percent',
            'url' => route('super.admin.coupons-tax-rates.index'),
        ],
        [
            'title' => __('super-admin.roles'),
            'description' => __('super-admin.manage_roles'),
            'icon' => 'bi bi-shield',
            'url' => route('super.admin.roles.index'),
        ],
        [
            'title' => __('super-admin.test_checklist'),
            'description' => __('super-admin.test_checklist_cmd'),
            'icon' => 'bi bi-check2-square',
            'url' => route('super.admin.test-checklist.index'),
        ],
        [
            'title' => __('super-admin.activity_log'),
            'description' => __('super-admin.view_activity'),
            'icon' => 'bi bi-activity',
            'url' => route('super.admin.activity-log'),
        ],
        [
            'title' => __('super-admin.settings'),
            'description' => __('super.admin.system_settings'),
            'icon' => 'bi bi-gear',
            'url' => route('super.admin.settings.index'),
        ],
        [
            'title' => __('general.dashboard'),
            'description' => __('general.go_to_user_dashboard'),
            'icon' => 'bi bi-person',
            'url' => route('dashboard'),
        ],
    ]" />

    <x-toast />
    <x-confirm-modal />

    @livewireScripts
    @stack('scripts')

    @include('layouts.partials._alpine-components')

    <script>
        function adminNotificationDropdown() {
            return {
                open: false,
                notifications: [],
                unreadCount: 0,
                pollInterval: null,

                init() {
                    this.fetchNotifications();
                    this.pollInterval = setInterval(() => this.fetchNotifications(), 30000);
                },

                destroy() {
                    if (this.pollInterval) clearInterval(this.pollInterval);
                },

                toggle() {
                    this.open = !this.open;
                    if (this.open) this.fetchNotifications();
                },

                fetchNotifications() {
                    fetch('{{ route('super.admin.notifications.index') }}', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            this.notifications = data.notifications || [];
                            this.unreadCount = data.unread_count || 0;
                        })
                        .catch(() => {});
                },

                markRead(id) {
                    fetch(`/super-admin/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(() => this.fetchNotifications());
                },

                markAllRead() {
                    fetch('{{ route('super.admin.notifications.mark-all-read') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(() => this.fetchNotifications());
                },

                iconClass(type) {
                    const map = {
                        new_user: 'bi-person-plus',
                        new_payment: 'bi-cash-stack',
                        subscription_activated: 'bi-stars',
                        backup_completed: 'bi-cloud-check',
                        system_alert: 'bi-exclamation-triangle',
                    };
                    return map[type] || 'bi-bell';
                },

                iconColor(type) {
                    const map = {
                        new_user: 'primary',
                        new_payment: 'success',
                        subscription_activated: 'info',
                        backup_completed: 'secondary',
                        system_alert: 'warning',
                    };
                    return map[type] || 'secondary';
                },

                timeAgo(dateStr) {
                    const now = new Date();
                    const date = new Date(dateStr);
                    const diff = Math.floor((now - date) / 1000);
                    if (diff < 60) return '{{ __('general.just_now') }}';
                    if (diff < 3600) return Math.floor(diff / 60) + 'm';
                    if (diff < 86400) return Math.floor(diff / 3600) + 'h';
                    return Math.floor(diff / 86400) + 'd';
                },
            };
        }
    </script>
    <script type="module" src="https://esm.sh/ionicons@latest/loader"></script>
    <script nomodule src="https://esm.sh/ionicons@latest/loader"></script>
</body>

</html>
