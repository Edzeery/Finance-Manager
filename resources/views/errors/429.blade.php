<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Too Many Requests - {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="error-layout" style="--error-gradient:linear-gradient(135deg,var(--warning),var(--danger));--error-icon-bg:var(--warning-light);--error-icon-color:var(--warning)">
    <div class="error-page">
        <div class="app-name"><i class="bi bi-shield-check"></i>{{ config('app.name') }}</div>
        <div class="error-icon"><i class="bi bi-speedometer2"></i></div>
        <div class="error-code">429</div>
        <div class="error-title">{{ __('Too Many Requests') }}</div>
        <div class="error-message">{{ __('You have sent too many requests in a short period. Please wait a moment before trying again.') }}</div>
        <div class="retry-info"><i class="bi bi-hourglass-split"></i> {{ __('Please try again later') }}</div>
        <div class="error-actions">
            <a href="{{ route('dashboard') }}" class="btn-error btn-error-primary"><i class="bi bi-house-door"></i> {{ __('general.dashboard') }}</a>
            <a href="javascript:location.reload()" class="btn-error btn-error-secondary"><i class="bi bi-arrow-counterclockwise"></i> {{ __('Try Again') }}</a>
        </div>
    </div>
</body>
</html>
