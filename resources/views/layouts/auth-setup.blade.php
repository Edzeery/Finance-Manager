<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" data-theme="{{ session('theme', 'light') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name') . ' - ' . __('general.app_description') }}">
    <meta name="theme-color" content="#15B76C">
    <meta name="theme-switch-url" content="{{ route('theme.switch') }}">
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

<body class="auth-setup-page">
    <x-navbar variant="setup" />

    <main class="setup-content">
        {{ $slot }}
    </main>

    <x-toast />
    <x-confirm-modal />

    @livewireScripts
    @stack('scripts')
    <script type="module" src="https://esm.sh/ionicons@8.0.13/loader"></script>
    <script nomodule src="https://esm.sh/ionicons@8.0.13/loader"></script>
</body>

</html>
