<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}"
      data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forbidden - {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="error-layout" style="--error-gradient:linear-gradient(135deg,var(--danger),var(--warning));--error-icon-bg:var(--danger-light);--error-icon-color:var(--danger)">
    <x-page-loader />
    <div class="error-page">
        <div class="app-name"><i class="bi bi-shield-check"></i>{{ config('app.name') }}</div>
        <div class="error-icon"><i class="bi bi-shield-exclamation"></i></div>
        <div class="error-code">403</div>
        <div class="error-title">{{ __('Access Denied') }}</div>
        <div class="error-message">{{ __('You do not have permission to view this page.') }}</div>
        <div class="error-actions">
            <a href="{{ route('dashboard') }}" class="btn-error btn-error-primary"><i class="bi bi-house-door"></i> {{ __('general.dashboard') }}</a>
            <a href="javascript:history.back()" class="btn-error btn-error-secondary"><i class="bi bi-arrow-left"></i> {{ __('Go Back') }}</a>
        </div>
    </div>
</body>
</html>
