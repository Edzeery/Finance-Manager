<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" data-theme="{{ session('theme', 'light') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="{{ $metaDescription ?? config('app.name') . ' - ' . __('general.app_description') }}">
    <meta name="theme-color" content="#15B76C">
    <meta name="theme-switch-url" content="{{ route('theme.switch') }}">
    <meta name="password-verify-url" content="{{ route('account.settings.developer.verify-password') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>

<body>
    <x-page-loader />
    <div class="app-layout" x-data="appLayout" :class="{ 'sidebar-collapsed': collapsed }">
        @include('layouts.partials._user-sidebar')

        <div class="main-content"
            @click="if(window.innerWidth < 992 && event.target === event.currentTarget) closeSidebarMobile()">
            @include('layouts.partials.topbar')

            @auth
                @php
                    $hasActive = (bool) auth()->user()->activeSubscription();
                    $hasExpiredWithoutGrace =
                        !$hasActive &&
                        auth()
                            ->user()
                            ->subscriptions()
                            ->withoutGlobalScopes()
                            ->whereIn('status', ['expired', 'canceled'])
                            ->where(function ($q) {
                                $q->whereNull('grace_ends_at')->orWhere('grace_ends_at', '<', now());
                            })
                            ->exists();
                @endphp
                @if ($hasExpiredWithoutGrace)
                    <div x-data="{ show: true }" x-show="show" x-transition.duration.300
                        class="mx-3 mx-md-4 mt-2 mb-0 px-3 py-2 rounded-2 d-flex align-items-center justify-content-between"
                        style="background:#fff3cd;color:#856404;border:1px solid #ffc107;font-size:13px">
                        <div>
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            {{ __('enums.subscription_status.expired') ?? 'اشتراكك منتهي. يرجى تجديد الاشتراك.' }}
                            <a href="{{ route('account.subscriptions') }}" class="ms-2 fw-bold text-decoration-underline"
                                style="color:#856404">
                                {{ __('subscription.renew') ?? 'تجديد' }}
                            </a>
                        </div>
                        <button @click="show = false" type="button"
                            style="background:none;border:none;color:#856404;cursor:pointer;padding:2px 6px">
                            <i class="bi bi-x-lg" style="font-size:11px"></i>
                        </button>
                    </div>
                @endif
            @endauth
            <main>
                <div class="page-header">
                    @isset($breadcrumb)
                        <x-breadcrumb :items="$breadcrumb" />
                    @endisset
                    <h1>{{ $pageTitle ?? '' }}</h1>
                    @isset($pageDescription)
                        <p>{{ $pageDescription }}</p>
                    @endisset
                </div>

                <div class="p-3 p-md-4">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <x-command-palette :items="[
            [
                'title' => __('general.dashboard'),
                'description' => __('general.go_to_dashboard'),
                'icon' => 'bi bi-grid-1x2-fill',
                'url' => route('dashboard'),
            ],
            [
                'title' => __('general.profile'),
                'description' => __('general.manage_profile'),
                'icon' => 'bi bi-person',
                'url' => route('account.profile'),
            ],
            [
                'title' => __('general.settings'),
                'description' => __('general.manage_settings'),
                'icon' => 'bi bi-gear',
                'url' => route('settings.index'),
            ],
            [
                'title' => __('transactions.add_income'),
                'description' => __('transactions.record_new_income'),
                'icon' => 'bi bi-plus-circle',
                'url' => route('income.create'),
            ],
            [
                'title' => __('transactions.add_expense'),
                'description' => __('transactions.record_new_expense'),
                'icon' => 'bi bi-dash-circle',
                'url' => route('expense.create'),
            ],
            [
                'title' => __('transactions.title'),
                'description' => __('transactions.view_all'),
                'icon' => 'bi bi-arrow-left-right',
                'url' => route('transactions.index'),
            ],
            [
                'title' => __('budget.title'),
                'description' => __('budget.manage'),
                'icon' => 'bi bi-pie-chart',
                'url' => route('budget.index'),
            ],
            [
                'title' => __('goal.title'),
                'description' => __('goal.manage'),
                'icon' => 'bi bi-flag',
                'url' => route('goal.index'),
            ],
            [
                'title' => __('debt.title'),
                'description' => __('debt.manage'),
                'icon' => 'bi bi-credit-card-2-front',
                'url' => route('debt.index'),
            ],
            [
                'title' => __('general.notifications'),
                'description' => __('general.view_notifications'),
                'icon' => 'bi bi-bell',
                'url' => route('notifications.index'),
            ],
            [
                'title' => __('report.title'),
                'description' => __('report.generate'),
                'icon' => 'bi bi-file-earmark-bar-graph',
                'url' => route('report.index'),
            ],
            [
                'title' => __('invoices.title'),
                'description' => __('invoices.view_all'),
                'icon' => 'bi bi-receipt',
                'url' => route('account.invoices.index'),
            ],
        ]" />
    </div>

    <x-toast />
    <x-confirm-modal />

    @livewireScripts
    <script>
        (function() {
            const PING_URL = '{{ route("ping") }}';
            const PING_INTERVAL = 2 * 60 * 1000; // 2 minutes

            setInterval(function() {
                fetch(PING_URL, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                }).catch(function() {});
            }, PING_INTERVAL);

            // Also ping on page show (tab switch back / minimize restore)
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    fetch(PING_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    }).catch(function() {});
                }
            });
        })();
    </script>
    @stack('scripts')
    <script type="module" src="https://esm.sh/ionicons@latest/loader"></script>
    <script nomodule src="https://esm.sh/ionicons@latest/loader"></script>
    @include('layouts.partials._alpine-components')
</body>

</html>
