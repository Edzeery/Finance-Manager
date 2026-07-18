<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found - {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="error-layout" style="--error-gradient:linear-gradient(135deg,var(--warning),#f97316);--error-icon-bg:var(--warning-light);--error-icon-color:var(--warning)">
    <x-page-loader />
    <div class="error-page">
        <div class="app-name"><i class="bi bi-shield-check"></i>{{ config('app.name') }}</div>
        <div class="error-icon"><i class="bi bi-compass"></i></div>
        <div class="error-code">404</div>
        <div class="error-title">{{ __('Page Not Found') }}</div>
        <div class="error-message">{{ __('The page you are looking for does not exist or has been moved.') }}</div>
        <form action="{{ route('search') }}" method="GET" class="search-box" style="display:none" data-search>
            <input type="text" name="search" placeholder="{{ __('general.search') }}" aria-label="{{ __('general.search') }}">
            <button type="submit"><i class="bi bi-search"></i></button>
        </form>
        <div class="error-actions">
            <a href="{{ route('dashboard') }}" class="btn-error btn-error-primary"><i class="bi bi-house-door"></i> {{ __('general.dashboard') }}</a>
            <a href="{{ url('/') }}" class="btn-error btn-error-secondary"><i class="bi bi-globe"></i> {{ __('Home') }}</a>
        </div>
    </div>
</body>
</html>
