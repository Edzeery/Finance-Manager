<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\WorkspaceInvitationController;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $plans = SubscriptionPlan::active()->public()->with('planFeatures')->orderBy('sort_order')->get();

    return view('landing', compact('plans'));
})->name('landing');

Route::post('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::post('/theme/switch', [ThemeController::class, 'switch'])
    ->middleware(['auth', 'web'])->name('theme.switch');

Route::post('/currency/{currency}', [CurrencyController::class, 'switch'])
    ->middleware('auth')->name('currency.switch');

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

// Invitation routes (public — no auth needed; controller handles guest redirect)
Route::get('/invitations/accept/{token}', [WorkspaceInvitationController::class, 'accept'])
    ->name('invitations.accept');
Route::get('/invitations/decline/{token}', [WorkspaceInvitationController::class, 'decline'])
    ->name('invitations.decline');

Route::view('/api/documentation', 'api-docs.index')
    ->middleware('plan.feature:api_access')
    ->name('api.documentation');

// Webhook endpoints (payment gateways — no CSRF, see app.php except list)
require __DIR__.'/webhooks.php';

// Tenant (workspace) routes — main application
require __DIR__.'/tenant.php';

// Authentication routes
require __DIR__.'/auth.php';
