<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->middleware('throttle:register')
        ->name('register');

    Volt::route('login', 'pages.auth.login')
        ->middleware('throttle:login')
        ->name('login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->middleware('throttle:login')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->middleware('throttle:login')
        ->name('password.reset');
});

Route::middleware('guest')->group(function () {
    Volt::route('super-admin/login', 'pages.super-admin.login')
        ->middleware('throttle:login')
        ->name('super.admin.login');
});

Route::middleware('guest')->group(function () {
    Volt::route('two-factor-challenge', 'pages.auth.two-factor-challenge')
        ->middleware('throttle:login')
        ->name('two-factor.challenge');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:10,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');

    Volt::route('account/security/two-factor-setup', 'pages.auth.two-factor-setup')
        ->name('two-factor.setup');
});
