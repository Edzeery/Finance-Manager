<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SuperAdmin\AccountController;
use App\Http\Controllers\SuperAdmin\AdminNotificationController;
use App\Http\Controllers\SuperAdmin\BackupController;
use App\Http\Controllers\SuperAdmin\CouponController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\InvoiceController;
use App\Http\Controllers\SuperAdmin\PaymentController;
use App\Http\Controllers\SuperAdmin\PaymentGatewayController;
use App\Http\Controllers\SuperAdmin\PaymentMethodController;
use App\Http\Controllers\SuperAdmin\PlanFeatureController;
use App\Http\Controllers\SuperAdmin\PlanPriceController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\SettingsController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use App\Http\Controllers\SuperAdmin\SubscriptionPlanController;
use App\Http\Controllers\SuperAdmin\TaxRateController;
use App\Http\Controllers\SuperAdmin\TestChecklistController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\WorkspaceController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::prefix('super-admin')->name('super.admin.')->middleware(['super.admin', 'two-factor', 'throttle:120,1'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->middleware('permission:platform-dashboard.view')->name('dashboard');
    Route::get('/account/profile', [AccountController::class, 'index'])
        ->name('account.profile');
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:platform-user.view')->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:platform-user.create')->name('users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:platform-user.create')->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:platform-user.update')->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:platform-user.update')->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:platform-user.delete')->name('users.destroy');
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])
        ->middleware('permission:platform-user.delete')->name('users.bulk-delete');
    Route::post('/users/bulk-restore', [UserController::class, 'bulkRestore'])
        ->middleware('permission:platform-user.update')->name('users.bulk-restore');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->middleware('permission:platform-user.update')->name('users.toggle-status');
    Route::post('/users/{user}/status', [UserController::class, 'setStatus'])
        ->middleware('permission:platform-user.update')->name('users.set-status');
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])
        ->middleware('permission:platform-user.update')->name('users.restore');
    Route::delete('/users/{id}/force', [UserController::class, 'forceDestroy'])
        ->middleware('permission:platform-user.delete')->name('users.force-destroy');
    Route::get('/workspaces', [WorkspaceController::class, 'index'])
        ->middleware('permission:tenant.view')->name('workspaces.index');
    Route::post('/workspaces/{workspace}/restore', [WorkspaceController::class, 'restore'])
        ->middleware('permission:tenant.restore')->name('workspaces.restore');
    Route::delete('/workspaces/{workspace}/force', [WorkspaceController::class, 'forceDelete'])
        ->middleware('permission:tenant.delete')->name('workspaces.force-delete');
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])
        ->middleware('permission:subscription.view')->name('subscriptions.index');
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show'])
        ->middleware('permission:subscription.view')->name('subscriptions.show');
    Route::post('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel'])
        ->middleware('permission:subscription.update')->name('subscriptions.cancel');
    Route::post('/subscriptions/{id}/toggle-renew', [SubscriptionController::class, 'toggleRenew'])
        ->middleware('permission:subscription.update')->name('subscriptions.toggle-renew');
    Route::post('/subscriptions/{id}/change-plan', [SubscriptionController::class, 'changePlan'])
        ->middleware('permission:subscription.update')->name('subscriptions.change-plan');
    Route::get('/payments', [PaymentController::class, 'index'])
        ->middleware('permission:payment.view')->name('payments.index');
    Route::post('/payments/{id}/approve', [PaymentController::class, 'approve'])
        ->middleware('permission:payment.verify')->name('payments.approve');
    Route::post('/payments/{id}/reject', [PaymentController::class, 'reject'])
        ->middleware('permission:payment.verify')->name('payments.reject');
    Route::post('/payments/{id}/refund', [PaymentController::class, 'refund'])
        ->middleware('permission:payment.refund')->name('payments.refund');
    Route::get('/invoices', [InvoiceController::class, 'index'])
        ->middleware('permission:invoice.view')->name('invoices.index');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])
        ->middleware('permission:invoice.view')->name('invoices.show');
    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [SubscriptionPlanController::class, 'index'])
            ->middleware('permission:plan.view')->name('index');
        Route::get('/create', [SubscriptionPlanController::class, 'create'])
            ->middleware('permission:plan.create')->name('create');
        Route::post('/', [SubscriptionPlanController::class, 'store'])
            ->middleware('permission:plan.create')->name('store');
        Route::get('/{plan}/edit', [SubscriptionPlanController::class, 'edit'])
            ->middleware('permission:plan.update')->name('edit');
        Route::put('/{plan}', [SubscriptionPlanController::class, 'update'])
            ->middleware('permission:plan.update')->name('update');
        Route::delete('/{plan}', [SubscriptionPlanController::class, 'destroy'])
            ->middleware('permission:plan.delete')->name('destroy');

        // Plan Prices (nested under plans)
        Route::prefix('{plan}/prices')->name('prices.')->group(function () {
            Route::get('/', [PlanPriceController::class, 'index'])
                ->middleware('permission:price.view')->name('index');
            Route::get('/create', [PlanPriceController::class, 'create'])
                ->middleware('permission:price.create')->name('create');
            Route::post('/', [PlanPriceController::class, 'store'])
                ->middleware('permission:price.create')->name('store');
            Route::get('/{price}/edit', [PlanPriceController::class, 'edit'])
                ->middleware('permission:price.update')->name('edit');
            Route::put('/{price}', [PlanPriceController::class, 'update'])
                ->middleware('permission:price.update')->name('update');
            Route::delete('/{price}', [PlanPriceController::class, 'destroy'])
                ->middleware('permission:price.delete')->name('destroy');
        });
    });

    Route::prefix('features')->name('features.')->group(function () {
        Route::get('/', [PlanFeatureController::class, 'index'])
            ->middleware('permission:feature.view')->name('index');
        Route::get('/create', [PlanFeatureController::class, 'create'])
            ->middleware('permission:feature.create')->name('create');
        Route::post('/', [PlanFeatureController::class, 'store'])
            ->middleware('permission:feature.create')->name('store');
        Route::get('/{feature}/edit', [PlanFeatureController::class, 'edit'])
            ->middleware('permission:feature.update')->name('edit');
        Route::put('/{feature}', [PlanFeatureController::class, 'update'])
            ->middleware('permission:feature.update')->name('update');
        Route::delete('/{feature}', [PlanFeatureController::class, 'destroy'])
            ->middleware('permission:feature.delete')->name('destroy');
    });
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [CouponController::class, 'index'])
            ->middleware('permission:coupon.view')->name('index');
        Route::get('/create', [CouponController::class, 'create'])
            ->middleware('permission:coupon.create')->name('create');
        Route::post('/', [CouponController::class, 'store'])
            ->middleware('permission:coupon.create')->name('store');
        Route::get('/{coupon}/edit', [CouponController::class, 'edit'])
            ->middleware('permission:coupon.update')->name('edit');
        Route::put('/{coupon}', [CouponController::class, 'update'])
            ->middleware('permission:coupon.update')->name('update');
        Route::delete('/{coupon}', [CouponController::class, 'destroy'])
            ->middleware('permission:coupon.delete')->name('destroy');
    });
    Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
        Volt::route('/', 'pages.super-admin.payment-methods')
            ->middleware('permission:platform-setting.general')->name('index');
        Route::get('/create', [PaymentMethodController::class, 'create'])
            ->middleware('permission:platform-setting.general')->name('create');
        Route::post('/', [PaymentMethodController::class, 'store'])
            ->middleware('permission:platform-setting.general')->name('store');
        Route::get('/{paymentMethod}/edit', [PaymentMethodController::class, 'edit'])
            ->middleware('permission:platform-setting.general')->name('edit');
        Route::put('/{paymentMethod}', [PaymentMethodController::class, 'update'])
            ->middleware('permission:platform-setting.general')->name('update');
        Route::delete('/{paymentMethod}', [PaymentMethodController::class, 'destroy'])
            ->middleware('permission:platform-setting.general')->name('destroy');
        Route::post('/{paymentMethod}/toggle-status', [PaymentMethodController::class, 'toggleStatus'])
            ->middleware('permission:platform-setting.general')->name('toggle-status');
        Route::post('/{paymentMethod}/toggle-public', [PaymentMethodController::class, 'togglePublic'])
            ->middleware('permission:platform-setting.general')->name('toggle-public');
    });
    Route::prefix('gateways')->name('gateways.')->group(function () {
        Route::get('/create', [PaymentGatewayController::class, 'create'])
            ->middleware('permission:platform-setting.general')->name('create');
        Route::post('/', [PaymentGatewayController::class, 'store'])
            ->middleware('permission:platform-setting.general')->name('store');
        Route::get('/{gateway}/edit', [PaymentGatewayController::class, 'edit'])
            ->middleware('permission:platform-setting.general')->name('edit');
        Route::put('/{gateway}', [PaymentGatewayController::class, 'update'])
            ->middleware('permission:platform-setting.general')->name('update');
        Route::delete('/{gateway}', [PaymentGatewayController::class, 'destroy'])
            ->middleware('permission:platform-setting.general')->name('destroy');
    });
    Volt::route('/coupons-tax-rates', 'pages.super-admin.coupons-and-tax-rates')
        ->middleware('permission:coupon.view|tax-rate.view')->name('coupons-tax-rates.index');

    Route::prefix('tax-rates')->name('tax-rates.')->group(function () {
        Route::get('/', [TaxRateController::class, 'index'])
            ->middleware('permission:tax-rate.view|platform-setting.general')->name('index');
        Route::get('/create', [TaxRateController::class, 'create'])
            ->middleware('permission:tax-rate.create|platform-setting.general')->name('create');
        Route::post('/', [TaxRateController::class, 'store'])
            ->middleware('permission:tax-rate.create|platform-setting.general')->name('store');
        Route::get('/{taxRate}/edit', [TaxRateController::class, 'edit'])
            ->middleware('permission:tax-rate.update|platform-setting.general')->name('edit');
        Route::put('/{taxRate}', [TaxRateController::class, 'update'])
            ->middleware('permission:tax-rate.update|platform-setting.general')->name('update');
        Route::delete('/{taxRate}', [TaxRateController::class, 'destroy'])
            ->middleware('permission:tax-rate.delete|platform-setting.general')->name('destroy');
    });

    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:platform-role.view')->name('roles.index');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:platform-role.update')->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:platform-role.update')->name('roles.update');

    Route::get('/workspace-roles', [RoleController::class, 'workspaceIndex'])
        ->middleware('permission:platform-role.view')->name('workspace-roles.index');
    Route::get('/workspace-roles/{role}/edit', [RoleController::class, 'workspaceEdit'])
        ->middleware('permission:platform-role.update')->name('workspace-roles.edit');
    Route::put('/workspace-roles/{role}', [RoleController::class, 'workspaceUpdate'])
        ->middleware('permission:platform-role.update')->name('workspace-roles.update');
    Route::prefix('settings')->name('settings.')->middleware('throttle:super-admin-settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])
            ->middleware('permission:platform-setting.general')->name('index');
        Route::put('/', [SettingsController::class, 'update'])
            ->middleware('permission:platform-setting.general')->name('update');
        Route::put('/system', [SettingsController::class, 'updateSystem'])
            ->middleware('permission:platform-setting.general')->name('system.update');
        Route::put('/exchange-rates', [SettingsController::class, 'updateExchangeRates'])
            ->middleware('permission:platform-setting.general')->name('exchange-rates.update');
        Route::put('/2fa/disable', [SettingsController::class, 'disable2fa'])
            ->middleware('permission:platform-setting.general')
            ->name('2fa.disable');
        Route::put('/rate-limits', [SettingsController::class, 'updateRateLimits'])
            ->middleware('permission:platform-setting.general')
            ->name('rate-limits.update');
        Route::put('/currencies', [SettingsController::class, 'updateCurrencies'])
            ->middleware('permission:platform-setting.general')
            ->name('currencies.update');
        Route::put('/zakat-prices', [SettingsController::class, 'updateZakatPrices'])
            ->middleware('permission:platform-setting.general')
            ->name('zakat-prices.update');
    });

    Route::get('/test-checklist', [TestChecklistController::class, 'index'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.index');
    Route::put('/test-checklist/{item}', [TestChecklistController::class, 'update'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.update');
    Route::put('/test-checklist/{item}/notes', [TestChecklistController::class, 'updateNotes'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.notes');
    Route::post('/test-checklist/reset', [TestChecklistController::class, 'reset'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.reset');
    Route::get('/test-checklist/stats', [TestChecklistController::class, 'stats'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.stats');
    Route::post('/test-checklist/export-markdown', [TestChecklistController::class, 'exportMarkdown'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.export-markdown');
    Route::post('/test-checklist/import-markdown', [TestChecklistController::class, 'importMarkdown'])
        ->middleware('permission:platform-setting.general')->name('test-checklist.import-markdown');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->middleware('permission:audit.view')->name('activity-log');

    Volt::route('/noest-orders', 'pages.super-admin.noest-orders')
        ->middleware('permission:platform-setting.general')->name('noest-orders.index');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AdminNotificationController::class, 'index'])
            ->name('index');
        Route::get('/{notification}', [AdminNotificationController::class, 'show'])
            ->name('show');
        Route::post('/{notification}/read', [AdminNotificationController::class, 'markRead'])
            ->name('read');
        Route::post('/mark-all-read', [AdminNotificationController::class, 'markAllRead'])
            ->name('mark-all-read');
        Route::delete('/{notification}', [AdminNotificationController::class, 'destroy'])
            ->name('destroy');
    });

    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])
            ->middleware('permission:backup.view')->name('index');
        Route::post('/', [BackupController::class, 'create'])
            ->middleware('permission:backup.create')->name('create');
        Route::get('/{directory}/{filename}', [BackupController::class, 'download'])
            ->middleware('permission:backup.view')->name('download');
        Route::post('/{directory}/{filename}/restore', [BackupController::class, 'restore'])
            ->middleware('permission:backup.restore')->name('restore');
        Route::delete('/{directory}/{filename}', [BackupController::class, 'destroy'])
            ->middleware('permission:backup.delete')->name('destroy');
    });
});
