<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error - {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="error-layout" style="--error-gradient:linear-gradient(135deg,var(--danger),#dc2626);--error-icon-bg:var(--danger-light);--error-icon-color:var(--danger)">
    <div class="error-page">
        <div class="app-name"><i class="bi bi-shield-check"></i>{{ config('app.name') }}</div>
        <div class="error-icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="error-code">500</div>
        <div class="error-title">{{ __('Server Error') }}</div>
        <div class="error-message">{{ __('Something went wrong on our end. Please try again later or contact support if the problem persists.') }}</div>
        <div class="error-actions">
            <a href="{{ route('dashboard') }}" class="btn-error btn-error-primary"><i class="bi bi-house-door"></i> {{ __('general.dashboard') }}</a>
            <a href="javascript:location.reload()" class="btn-error btn-error-secondary"><i class="bi bi-arrow-counterclockwise"></i> {{ __('Try Again') }}</a>
        </div>
        <div class="error-detail">{{ config('app.name') }} &mdash; {{ now()->year }}</div>
    </div>
</body>
</html>
