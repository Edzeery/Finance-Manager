<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::prefix('super-admin')->name('super.admin.')->middleware(['super.admin', 'two-factor', 'throttle:120,1'])->group(function () {
    Route::get('/', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])
        ->middleware('permission:platform-dashboard.view')->name('dashboard');
    Route::get('/users', [\App\Http\Controllers\SuperAdmin\UserController::class, 'index'])
        ->middleware('permission:platform-user.view')->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\SuperAdmin\UserController::class, 'create'])
        ->middleware('permission:platform-user.create')->name('users.create');
    Route::post('/users', [\App\Http\Controllers\SuperAdmin\UserController::class, 'store'])
        ->middleware('permission:platform-user.create')->name('users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\SuperAdmin\UserController::class, 'edit'])
        ->middleware('permission:platform-user.update')->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\SuperAdmin\UserController::class, 'update'])
        ->middleware('permission:platform-user.update')->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\SuperAdmin\UserController::class, 'destroy'])
        ->middleware('permission:platform-user.delete')->name('users.destroy');
    Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\SuperAdmin\UserController::class, 'toggleStatus'])
        ->middleware('permission:platform-user.update')->name('users.toggle-status');
    Route::get('/workspaces', [\App\Http\Controllers\SuperAdmin\WorkspaceController::class, 'index'])
        ->middleware('permission:tenant.view')->name('workspaces.index');
    Route::post('/workspaces/{workspace}/restore', [\App\Http\Controllers\SuperAdmin\WorkspaceController::class, 'restore'])
        ->middleware('permission:tenant.restore')->name('workspaces.restore');
    Route::delete('/workspaces/{workspace}/force', [\App\Http\Controllers\SuperAdmin\WorkspaceController::class, 'forceDelete'])
        ->middleware('permission:tenant.delete')->name('workspaces.force-delete');
    Route::get('/subscriptions', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'index'])
        ->middleware('permission:subscription.view')->name('subscriptions.index');
    Route::get('/subscriptions/{id}', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'show'])
        ->middleware('permission:subscription.view')->name('subscriptions.show');
    Route::post('/subscriptions/{id}/cancel', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'cancel'])
        ->middleware('permission:subscription.update')->name('subscriptions.cancel');
    Route::post('/subscriptions/{id}/toggle-renew', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'toggleRenew'])
        ->middleware('permission:subscription.update')->name('subscriptions.toggle-renew');
    Route::post('/subscriptions/{id}/change-plan', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'changePlan'])
        ->middleware('permission:subscription.update')->name('subscriptions.change-plan');
    Route::get('/payments', [\App\Http\Controllers\SuperAdmin\PaymentController::class, 'index'])
        ->middleware('permission:payment.view')->name('payments.index');
    Route::post('/payments/{id}/approve', [\App\Http\Controllers\SuperAdmin\PaymentController::class, 'approve'])
        ->middleware('permission:payment.verify')->name('payments.approve');
    Route::post('/payments/{id}/reject', [\App\Http\Controllers\SuperAdmin\PaymentController::class, 'reject'])
        ->middleware('permission:payment.verify')->name('payments.reject');
    Route::post('/payments/{id}/refund', [\App\Http\Controllers\SuperAdmin\PaymentController::class, 'refund'])
        ->middleware('permission:payment.refund')->name('payments.refund');
    Route::get('/invoices', [\App\Http\Controllers\SuperAdmin\InvoiceController::class, 'index'])
        ->middleware('permission:invoice.view')->name('invoices.index');
    Route::get('/invoices/{id}', [\App\Http\Controllers\SuperAdmin\InvoiceController::class, 'show'])
        ->middleware('permission:invoice.view')->name('invoices.show');
    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'index'])
            ->middleware('permission:plan.view')->name('index');
        Route::get('/create', [\App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'create'])
            ->middleware('permission:plan.create')->name('create');
        Route::post('/', [\App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'store'])
            ->middleware('permission:plan.create')->name('store');
        Route::get('/{plan}/edit', [\App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'edit'])
            ->middleware('permission:plan.update')->name('edit');
        Route::put('/{plan}', [\App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'update'])
            ->middleware('permission:plan.update')->name('update');
        Route::delete('/{plan}', [\App\Http\Controllers\SuperAdmin\SubscriptionPlanController::class, 'destroy'])
            ->middleware('permission:plan.delete')->name('destroy');

        // Plan Prices (nested under plans)
        Route::prefix('{plan}/prices')->name('prices.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\PlanPriceController::class, 'index'])
                ->middleware('permission:price.view')->name('index');
            Route::get('/create', [\App\Http\Controllers\SuperAdmin\PlanPriceController::class, 'create'])
                ->middleware('permission:price.create')->name('create');
            Route::post('/', [\App\Http\Controllers\SuperAdmin\PlanPriceController::class, 'store'])
                ->middleware('permission:price.create')->name('store');
            Route::get('/{price}/edit', [\App\Http\Controllers\SuperAdmin\PlanPriceController::class, 'edit'])
                ->middleware('permission:price.update')->name('edit');
            Route::put('/{price}', [\App\Http\Controllers\SuperAdmin\PlanPriceController::class, 'update'])
                ->middleware('permission:price.update')->name('update');
            Route::delete('/{price}', [\App\Http\Controllers\SuperAdmin\PlanPriceController::class, 'destroy'])
                ->middleware('permission:price.delete')->name('destroy');
        });
    });

    Route::prefix('features')->name('features.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\PlanFeatureController::class, 'index'])
            ->middleware('permission:feature.view')->name('index');
        Route::get('/create', [\App\Http\Controllers\SuperAdmin\PlanFeatureController::class, 'create'])
            ->middleware('permission:feature.create')->name('create');
        Route::post('/', [\App\Http\Controllers\SuperAdmin\PlanFeatureController::class, 'store'])
            ->middleware('permission:feature.create')->name('store');
        Route::get('/{feature}/edit', [\App\Http\Controllers\SuperAdmin\PlanFeatureController::class, 'edit'])
            ->middleware('permission:feature.update')->name('edit');
        Route::put('/{feature}', [\App\Http\Controllers\SuperAdmin\PlanFeatureController::class, 'update'])
            ->middleware('permission:feature.update')->name('update');
        Route::delete('/{feature}', [\App\Http\Controllers\SuperAdmin\PlanFeatureController::class, 'destroy'])
            ->middleware('permission:feature.delete')->name('destroy');
    });
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'index'])
            ->middleware('permission:coupon.view')->name('index');
        Route::get('/create', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'create'])
            ->middleware('permission:coupon.create')->name('create');
        Route::post('/', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'store'])
            ->middleware('permission:coupon.create')->name('store');
        Route::get('/{coupon}/edit', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'edit'])
            ->middleware('permission:coupon.update')->name('edit');
        Route::put('/{coupon}', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'update'])
            ->middleware('permission:coupon.update')->name('update');
        Route::delete('/{coupon}', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'destroy'])
            ->middleware('permission:coupon.delete')->name('destroy');
    });
    Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
        Volt::route('/', 'pages.super-admin.payment-methods')
            ->middleware('permission:platform-setting.general')->name('index');
        Route::get('/create', [\App\Http\Controllers\SuperAdmin\PaymentMethodController::class, 'create'])
            ->middleware('permission:platform-setting.general')->name('create');
        Route::post('/', [\App\Http\Controllers\SuperAdmin\PaymentMethodController::class, 'store'])
            ->middleware('permission:platform-setting.general')->name('store');
        Route::get('/{paymentMethod}/edit', [\App\Http\Controllers\SuperAdmin\PaymentMethodController::class, 'edit'])
            ->middleware('permission:platform-setting.general')->name('edit');
        Route::put('/{paymentMethod}', [\App\Http\Controllers\SuperAdmin\PaymentMethodController::class, 'update'])
            ->middleware('permission:platform-setting.general')->name('update');
        Route::delete('/{paymentMethod}', [\App\Http\Controllers\SuperAdmin\PaymentMethodController::class, 'destroy'])
            ->middleware('permission:platform-setting.general')->name('destroy');
        Route::post('/{paymentMethod}/toggle-status', [\App\Http\Controllers\SuperAdmin\PaymentMethodController::class, 'toggleStatus'])
            ->middleware('permission:platform-setting.general')->name('toggle-status');
        Route::post('/{paymentMethod}/toggle-public', [\App\Http\Controllers\SuperAdmin\PaymentMethodController::class, 'togglePublic'])
            ->middleware('permission:platform-setting.general')->name('toggle-public');
    });
    Route::prefix('gateways')->name('gateways.')->group(function () {
        Route::get('/create', [\App\Http\Controllers\SuperAdmin\PaymentGatewayController::class, 'create'])
            ->middleware('permission:platform-setting.general')->name('create');
        Route::post('/', [\App\Http\Controllers\SuperAdmin\PaymentGatewayController::class, 'store'])
            ->middleware('permission:platform-setting.general')->name('store');
        Route::get('/{gateway}/edit', [\App\Http\Controllers\SuperAdmin\PaymentGatewayController::class, 'edit'])
            ->middleware('permission:platform-setting.general')->name('edit');
        Route::put('/{gateway}', [\App\Http\Controllers\SuperAdmin\PaymentGatewayController::class, 'update'])
            ->middleware('permission:platform-setting.general')->name('update');
        Route::delete('/{gateway}', [\App\Http\Controllers\SuperAdmin\PaymentGatewayController::class, 'destroy'])
            ->middleware('permission:platform-setting.general')->name('destroy');
    });
    Volt::route('/coupons-tax-rates', 'pages.super-admin.coupons-and-tax-rates')
        ->middleware('permission:coupon.view|tax-rate.view')->name('coupons-tax-rates.index');

    Route::prefix('tax-rates')->name('tax-rates.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\TaxRateController::class, 'index'])
            ->middleware('permission:tax-rate.view|platform-setting.general')->name('index');
        Route::get('/create', [\App\Http\Controllers\SuperAdmin\TaxRateController::class, 'create'])
            ->middleware('permission:tax-rate.create|platform-setting.general')->name('create');
        Route::post('/', [\App\Http\Controllers\SuperAdmin\TaxRateController::class, 'store'])
            ->middleware('permission:tax-rate.create|platform-setting.general')->name('store');
        Route::get('/{taxRate}/edit', [\App\Http\Controllers\SuperAdmin\TaxRateController::class, 'edit'])
            ->middleware('permission:tax-rate.update|platform-setting.general')->name('edit');
        Route::put('/{taxRate}', [\App\Http\Controllers\SuperAdmin\TaxRateController::class, 'update'])
            ->middleware('permission:tax-rate.update|platform-setting.general')->name('update');
        Route::delete('/{taxRate}', [\App\Http\Controllers\SuperAdmin\TaxRateController::class, 'destroy'])
            ->middleware('permission:tax-rate.delete|platform-setting.general')->name('destroy');
    });

    Route::get('/roles', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'index'])
        ->middleware('permission:platform-role.view')->name('roles.index');
    Route::get('/roles/{role}/edit', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'edit'])
        ->middleware('permission:platform-role.update')->name('roles.edit');
    Route::put('/roles/{role}', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'update'])
        ->middleware('permission:platform-role.update')->name('roles.update');

    Route::get('/workspace-roles', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'workspaceIndex'])
        ->middleware('permission:platform-role.view')->name('workspace-roles.index');
    Route::get('/workspace-roles/{role}/edit', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'workspaceEdit'])
        ->middleware('permission:platform-role.update')->name('workspace-roles.edit');
    Route::put('/workspace-roles/{role}', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'workspaceUpdate'])
        ->middleware('permission:platform-role.update')->name('workspace-roles.update');
    Route::prefix('settings')->name('settings.')->middleware('throttle:super-admin-settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])
            ->middleware('permission:platform-setting.general')->name('index');
        Route::put('/', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'update'])
            ->middleware('permission:platform-setting.general')->name('update');
        Route::put('/system', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateSystem'])
            ->middleware('permission:platform-setting.general')->name('system.update');
        Route::put('/exchange-rates', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateExchangeRates'])
            ->middleware('permission:platform-setting.general')->name('exchange-rates.update');
        Route::put('/2fa/disable', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'disable2fa'])
            ->name('2fa.disable');
        Route::put('/rate-limits', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateRateLimits'])
            ->middleware('permission:platform-setting.general')
            ->name('rate-limits.update');
        Route::put('/currencies', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateCurrencies'])
            ->middleware('permission:platform-setting.general')
            ->name('currencies.update');
    });

    Route::get('/test-checklist', [\App\Http\Controllers\SuperAdmin\TestChecklistController::class, 'index'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.index');
    Route::put('/test-checklist/{item}', [\App\Http\Controllers\SuperAdmin\TestChecklistController::class, 'update'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.update');
    Route::put('/test-checklist/{item}/notes', [\App\Http\Controllers\SuperAdmin\TestChecklistController::class, 'updateNotes'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.notes');
    Route::post('/test-checklist/reset', [\App\Http\Controllers\SuperAdmin\TestChecklistController::class, 'reset'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.reset');
    Route::get('/test-checklist/stats', [\App\Http\Controllers\SuperAdmin\TestChecklistController::class, 'stats'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.stats');
    Route::post('/test-checklist/export-markdown', [\App\Http\Controllers\SuperAdmin\TestChecklistController::class, 'exportMarkdown'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.export-markdown');
    Route::post('/test-checklist/import-markdown', [\App\Http\Controllers\SuperAdmin\TestChecklistController::class, 'importMarkdown'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.import-markdown');

    Route::get('/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])
        ->middleware('permission:audit.view')->name('activity-log');

    Volt::route('/noest-orders', 'pages.super-admin.noest-orders')
        ->middleware('permission:platform-setting.general')->name('noest-orders.index');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\AdminNotificationController::class, 'index'])
            ->name('index');
        Route::post('/{notification}/read', [\App\Http\Controllers\SuperAdmin\AdminNotificationController::class, 'markRead'])
            ->name('read');
        Route::post('/mark-all-read', [\App\Http\Controllers\SuperAdmin\AdminNotificationController::class, 'markAllRead'])
            ->name('mark-all-read');
        Route::delete('/{notification}', [\App\Http\Controllers\SuperAdmin\AdminNotificationController::class, 'destroy'])
            ->name('destroy');
    });

    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'index'])
            ->middleware('permission:backup.view')->name('index');
        Route::post('/', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'create'])
            ->middleware('permission:backup.create')->name('create');
        Route::get('/{directory}/{filename}', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'download'])
            ->middleware('permission:backup.view')->name('download');
        Route::post('/{directory}/{filename}/restore', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'restore'])
            ->middleware('permission:backup.restore')->name('restore');
        Route::delete('/{directory}/{filename}', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'destroy'])
            ->middleware('permission:backup.delete')->name('destroy');
    });
});
