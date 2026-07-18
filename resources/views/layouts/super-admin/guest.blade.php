<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" data-theme="dark">

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

<body class="auth-page sa-guest overflow-y-auto">
    <x-page-loader />
    {{ $slot }}

    @livewireScripts
    @stack('scripts')
    <script type="module" src="https://esm.sh/ionicons@latest/loader"></script>
    <script nomodule src="https://esm.sh/ionicons@latest/loader"></script>
</body>

</html>
