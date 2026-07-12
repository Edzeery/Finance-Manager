<?php
// routes\tenant.php
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Asset\AssetController;
use App\Http\Controllers\Budget\BudgetController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponValidationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\Debt\DebtController;
use App\Http\Controllers\Expense\ExpenseCategoryController;
use App\Http\Controllers\Expense\ExpenseController;
use App\Http\Controllers\Goal\FinancialGoalController;
use App\Http\Controllers\Income\IncomeCategoryController;
use App\Http\Controllers\Income\IncomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentReturnController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WorkspaceSwitchController;
use App\Http\Controllers\Zakat\ZakatController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'verified', 'subscription', 'subscription.status'])->group(function () {
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Volt::route('/plan', 'pages.onboarding.plan')->name('plan');
        Volt::route('/manual-proof/{payment}', 'pages.onboarding.manual-proof')
            ->middleware('throttle:web-proof')->name('manual-proof');
        Volt::route('/setup', 'pages.onboarding.setup')->name('setup');
        Route::permanentRedirect('/complete', '/dashboard')->name('complete');
        // Redirect old /onboarding/payment to /onboarding/plan
        Route::permanentRedirect('/payment', '/onboarding/plan')->name('payment.redirect');
    });

    // Unified payment status page (replaces payment-resume + payment-retry)
    Volt::route('/payment/status/{payment}', 'pages.onboarding.payment-status')->name('payment.status');

    // Redirect old routes to new unified status page
    Route::permanentRedirect('/payment/resume/{payment}', '/payment/status/{payment}');
    Route::permanentRedirect('/payment/retry/{payment}', '/payment/status/{payment}');

    // Payment return pages — each online gateway has its own result route
    Volt::route('/payment/chargily/result/{payment?}', 'pages.onboarding.payment-result')->name('chargily.back');
    Volt::route('/payment/paypal/result/{payment?}', 'pages.onboarding.payment-result')->name('paypal.back');
    // Unified return route (clean name, same component)
    Volt::route('/payment/return/{payment?}', 'pages.onboarding.payment-result')->name('payment.return');

    // Payment gateway redirect (Charge via gateway-manager route)
    Route::get('/payment/checkout/{payment}', [CheckoutController::class, 'redirect'])->name('payment.checkout');
    // Payment status polling (used by Volt payment-success component)
    Route::get('/payment/check-status/{payment}', [PaymentReturnController::class, 'checkStatus'])->name('payment.check-status');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/transactions', [TransactionController::class, 'index'])
        ->middleware(['permission:transaction.view', 'plan.feature:income_expense'])
        ->name('transactions.index');

    Route::prefix('income')->name('income.')->middleware(['permission:income.view', 'plan.feature:income_expense', 'throttle:web-list'])->group(function () {
        Route::post('/bulk-delete', [IncomeController::class, 'bulkDelete'])
            ->middleware('permission:income.delete')->name('bulk-delete');
        Route::post('/bulk-restore', [IncomeController::class, 'bulkRestore'])
            ->middleware('permission:income.restore')->name('bulk-restore');
        Route::post('/bulk-force-delete', [IncomeController::class, 'bulkForceDelete'])
            ->middleware('permission:income.force-delete')->name('bulk-force-delete');
        Route::get('/', [IncomeController::class, 'index'])->name('index');
        Route::get('/create', [IncomeController::class, 'create'])
            ->middleware('permission:income.create')->name('create');
        Route::post('/', [IncomeController::class, 'store'])
            ->middleware(['permission:income.create', 'throttle:web-crud'])->name('store');
        Route::get('/{income}/edit', [IncomeController::class, 'edit'])
            ->middleware('permission:income.update')->name('edit');
        Route::put('/{income}', [IncomeController::class, 'update'])
            ->middleware(['permission:income.update', 'throttle:web-crud'])->name('update');
        Route::delete('/{income}', [IncomeController::class, 'destroy'])
            ->middleware(['permission:income.delete', 'throttle:web-crud'])->name('destroy');
        Route::delete('/{id}/force-delete', [IncomeController::class, 'forceDelete'])
            ->middleware(['permission:income.force-delete', 'throttle:web-sensitive'])->name('force-delete');
        Route::patch('/{income}/archive', [IncomeController::class, 'archive'])
            ->middleware('permission:income.archive')->name('archive');
        Route::patch('/{income}/restore', [IncomeController::class, 'restore'])
            ->middleware('permission:income.restore')->name('restore');
        Route::resource('categories', IncomeCategoryController::class)->except(['show']);
    });

    Route::prefix('expense')->name('expense.')->middleware(['permission:expense.view', 'plan.feature:income_expense', 'throttle:web-list'])->group(function () {
        Route::post('/bulk-delete', [ExpenseController::class, 'bulkDelete'])
            ->middleware('permission:expense.delete')->name('bulk-delete');
        Route::post('/bulk-restore', [ExpenseController::class, 'bulkRestore'])
            ->middleware('permission:expense.restore')->name('bulk-restore');
        Route::post('/bulk-force-delete', [ExpenseController::class, 'bulkForceDelete'])
            ->middleware('permission:expense.force-delete')->name('bulk-force-delete');
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [ExpenseController::class, 'create'])
            ->middleware('permission:expense.create')->name('create');
        Route::post('/', [ExpenseController::class, 'store'])
            ->middleware(['permission:expense.create', 'throttle:web-crud'])->name('store');
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])
            ->middleware('permission:expense.update')->name('edit');
        Route::put('/{expense}', [ExpenseController::class, 'update'])
            ->middleware(['permission:expense.update', 'throttle:web-crud'])->name('update');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])
            ->middleware(['permission:expense.delete', 'throttle:web-delete'])->name('destroy');
        Route::delete('/{id}/force-delete', [ExpenseController::class, 'forceDelete'])
            ->middleware(['permission:expense.force-delete', 'throttle:web-sensitive'])->name('force-delete');
        Route::patch('/{expense}/archive', [ExpenseController::class, 'archive'])
            ->middleware('permission:expense.archive')->name('archive');
        Route::patch('/{expense}/restore', [ExpenseController::class, 'restore'])
            ->middleware('permission:expense.restore')->name('restore');
        Route::resource('categories', ExpenseCategoryController::class)->except(['show']);
    });

    Route::prefix('debt')->name('debt.')->middleware(['permission:debt.view', 'plan.feature:debt', 'throttle:web-list'])->group(function () {
        Route::post('/bulk-delete', [DebtController::class, 'bulkDelete'])
            ->middleware('permission:debt.delete')->name('bulk-delete');
        Route::post('/bulk-restore', [DebtController::class, 'bulkRestore'])
            ->middleware('permission:debt.restore')->name('bulk-restore');
        Route::post('/bulk-force-delete', [DebtController::class, 'bulkForceDelete'])
            ->middleware('permission:debt.force-delete')->name('bulk-force-delete');
        Route::get('/', [DebtController::class, 'index'])->name('index');
        Route::get('/create', [DebtController::class, 'create'])
            ->middleware('permission:debt.create')->name('create');
        Route::post('/', [DebtController::class, 'store'])
            ->middleware(['permission:debt.create', 'throttle:web-crud'])->name('store');
        Route::get('/{debt}', [DebtController::class, 'show'])->name('show');
        Route::get('/{debt}/edit', [DebtController::class, 'edit'])
            ->middleware('permission:debt.update')->name('edit');
        Route::put('/{debt}', [DebtController::class, 'update'])
            ->middleware(['permission:debt.update', 'throttle:web-crud'])->name('update');
        Route::delete('/{debt}', [DebtController::class, 'destroy'])
            ->middleware(['permission:debt.delete', 'throttle:web-delete'])->name('destroy');
        Route::delete('/{id}/force-delete', [DebtController::class, 'forceDelete'])
            ->middleware(['permission:debt.force-delete', 'throttle:web-sensitive'])->name('force-delete');
        Route::post('/{debt}/payments', [DebtController::class, 'addPayment'])
            ->middleware(['permission:debt.create', 'throttle:web-crud'])->name('payments.store');
        Route::patch('/{debt}/restore', [DebtController::class, 'restore'])
            ->middleware('permission:debt.restore')->name('restore');
    });

    Route::prefix('asset')->name('asset.')->middleware(['permission:asset.view', 'plan.feature:debt', 'throttle:web-list'])->group(function () {
        Route::post('/bulk-delete', [AssetController::class, 'bulkDelete'])
            ->middleware('permission:asset.delete')->name('bulk-delete');
        Route::post('/bulk-restore', [AssetController::class, 'bulkRestore'])
            ->middleware('permission:asset.restore')->name('bulk-restore');
        Route::post('/bulk-force-delete', [AssetController::class, 'bulkForceDelete'])
            ->middleware('permission:asset.force-delete')->name('bulk-force-delete');
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::get('/create', [AssetController::class, 'create'])
            ->middleware('permission:asset.create')->name('create');
        Route::post('/', [AssetController::class, 'store'])
            ->middleware(['permission:asset.create', 'throttle:web-crud'])->name('store');
        Route::get('/{asset}/edit', [AssetController::class, 'edit'])
            ->middleware('permission:asset.update')->name('edit');
        Route::put('/{asset}', [AssetController::class, 'update'])
            ->middleware(['permission:asset.update', 'throttle:web-crud'])->name('update');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])
            ->middleware(['permission:asset.delete', 'throttle:web-delete'])->name('destroy');
        Route::delete('/{id}/force-delete', [AssetController::class, 'forceDelete'])
            ->middleware(['permission:asset.force-delete', 'throttle:web-sensitive'])->name('force-delete');
        Route::patch('/{asset}/restore', [AssetController::class, 'restore'])
            ->middleware('permission:asset.restore')->name('restore');
    });

    Route::prefix('budget')->name('budget.')->middleware(['permission:budget.view', 'plan.feature:budget', 'throttle:web-list'])->group(function () {
        Route::post('/bulk-delete', [BudgetController::class, 'bulkDelete'])
            ->middleware('permission:budget.delete')->name('bulk-delete');
        Route::post('/bulk-restore', [BudgetController::class, 'bulkRestore'])
            ->middleware('permission:budget.restore')->name('bulk-restore');
        Route::post('/bulk-force-delete', [BudgetController::class, 'bulkForceDelete'])
            ->middleware('permission:budget.force-delete')->name('bulk-force-delete');
        Route::get('/', [BudgetController::class, 'index'])->name('index');
        Route::get('/create', [BudgetController::class, 'create'])
            ->middleware('permission:budget.create')->name('create');
        Route::post('/', [BudgetController::class, 'store'])
            ->middleware(['permission:budget.create', 'throttle:web-crud'])->name('store');
        Route::get('/{budget}', [BudgetController::class, 'show'])->name('show');
        Route::get('/{budget}/edit', [BudgetController::class, 'edit'])
            ->middleware('permission:budget.update')->name('edit');
        Route::put('/{budget}', [BudgetController::class, 'update'])
            ->middleware(['permission:budget.update', 'throttle:web-crud'])->name('update');
        Route::delete('/{budget}', [BudgetController::class, 'destroy'])
            ->middleware(['permission:budget.delete', 'throttle:web-delete'])->name('destroy');
        Route::delete('/{id}/force-delete', [BudgetController::class, 'forceDelete'])
            ->middleware(['permission:budget.force-delete', 'throttle:web-sensitive'])->name('force-delete');
        Route::patch('/{budget}/restore', [BudgetController::class, 'restore'])
            ->middleware('permission:budget.restore')->name('restore');
    });

    Route::prefix('goal')->name('goal.')->middleware(['permission:goal.view', 'plan.feature:goals', 'throttle:web-list'])->group(function () {
        Route::post('/bulk-delete', [FinancialGoalController::class, 'bulkDelete'])
            ->middleware('permission:goal.delete')->name('bulk-delete');
        Route::post('/bulk-restore', [FinancialGoalController::class, 'bulkRestore'])
            ->middleware('permission:goal.restore')->name('bulk-restore');
        Route::post('/bulk-force-delete', [FinancialGoalController::class, 'bulkForceDelete'])
            ->middleware('permission:goal.force-delete')->name('bulk-force-delete');
        Route::get('/', [FinancialGoalController::class, 'index'])->name('index');
        Route::get('/create', [FinancialGoalController::class, 'create'])
            ->middleware('permission:goal.create')->name('create');
        Route::post('/', [FinancialGoalController::class, 'store'])
            ->middleware(['permission:goal.create', 'throttle:web-crud'])->name('store');
        Route::get('/{goal}/edit', [FinancialGoalController::class, 'edit'])
            ->middleware('permission:goal.update')->name('edit');
        Route::put('/{goal}', [FinancialGoalController::class, 'update'])
            ->middleware(['permission:goal.update', 'throttle:web-crud'])->name('update');
        Route::delete('/{goal}', [FinancialGoalController::class, 'destroy'])
            ->middleware(['permission:goal.delete', 'throttle:web-delete'])->name('destroy');
        Route::delete('/{id}/force-delete', [FinancialGoalController::class, 'forceDelete'])
            ->middleware(['permission:goal.force-delete', 'throttle:web-sensitive'])->name('force-delete');
        Route::patch('/{goal}/restore', [FinancialGoalController::class, 'restore'])
            ->middleware('permission:goal.restore')->name('restore');
    });

    Route::get('/search', [SearchController::class, 'search'])
        ->middleware('throttle:web-search')
        ->name('search');

    Route::prefix('zakat')->name('zakat.')->middleware(['permission:zakat.view', 'plan.feature:zakat'])->group(function () {
        Route::get('/', [ZakatController::class, 'calculator'])->name('calculator');
        Route::post('/calculate', [ZakatController::class, 'calculate'])
            ->middleware('permission:zakat.create')->name('calculate');
        Route::get('/history', [ZakatController::class, 'history'])->name('history');
        Route::get('/report/{zakatRecord}', [ZakatController::class, 'report'])->name('report');
    });

    Route::prefix('report')->name('report.')->middleware(['throttle:web-search', 'permission:report.view', 'plan.feature:reports'])->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/yearly', [ReportController::class, 'yearly'])->name('yearly');
    });

    Route::prefix('settings')->name('settings.')->middleware(['permission:workspace-setting.view', 'plan.feature:team_management', 'throttle:web-crud'])->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/', [SettingsController::class, 'update'])
            ->middleware('permission:workspace-setting.update')->name('update');
    });

    Route::permanentRedirect('/settings/subscriptions', '/account/subscriptions')->name('settings.subscriptions');

    Route::get('/coupon/validate/{code}/{amount?}', [CouponValidationController::class, 'check'])
        ->middleware('auth')->name('coupon.validate');

    Route::prefix('workspace')->name('settings.workspace.')->group(function () {
        Route::get('/create', [\App\Http\Controllers\Settings\WorkspaceController::class, 'create'])
            ->name('create');
        Route::post('/create', [\App\Http\Controllers\Settings\WorkspaceController::class, 'store'])
            ->name('store');
        Route::post('/update', [\App\Http\Controllers\Settings\WorkspaceController::class, 'update'])
            ->middleware('permission:workspace-setting.update')->name('update');

        Route::post('/members/invite', [\App\Http\Controllers\Settings\WorkspaceController::class, 'invite'])
            ->middleware(['permission:workspace-user.invite', 'plan.feature:team_management'])->name('members.invite');
        Route::put('/members/{user}/role', [\App\Http\Controllers\Settings\WorkspaceController::class, 'changeRole'])
            ->middleware(['permission:workspace-user.role', 'plan.feature:team_management'])->name('members.change-role');
        Route::delete('/members/{user}', [\App\Http\Controllers\Settings\WorkspaceController::class, 'remove'])
            ->middleware(['permission:workspace-user.remove', 'plan.feature:team_management'])->name('members.remove');
        Route::post('/members/transfer', [\App\Http\Controllers\Settings\WorkspaceController::class, 'transferOwnership'])
            ->middleware(['permission:workspace-user.role', 'plan.feature:team_management'])->name('members.transfer');
        Route::get('/roles', [\App\Http\Controllers\Settings\RoleController::class, 'index'])
            ->middleware(['permission:workspace-role.view', 'plan.feature:roles_permissions'])->name('roles.index');
        Route::get('/roles/{role}', [\App\Http\Controllers\Settings\RoleController::class, 'show'])
            ->middleware(['permission:workspace-role.view', 'plan.feature:roles_permissions'])->name('roles.show');
    });

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/profile', \App\Http\Controllers\Settings\ProfileController::class)->name('profile');
        Route::get('/subscriptions', [\App\Http\Controllers\Account\SubscriptionController::class, 'index'])->name('subscriptions');
        Route::get('/subscriptions/fee-breakdown', [\App\Http\Controllers\Account\SubscriptionController::class, 'feeBreakdown'])->name('subscriptions.fee-breakdown');
        Route::post('/subscriptions/change-plan', [\App\Http\Controllers\Settings\WorkspaceController::class, 'changePlan'])
            ->middleware('permission:workspace-user.role')->name('subscriptions.change-plan');
        Route::post('/subscriptions/cancel', [\App\Http\Controllers\Settings\WorkspaceController::class, 'cancel'])
            ->name('subscriptions.cancel');
        Route::post('/subscriptions/cancel-payment/{payment}', [\App\Http\Controllers\Account\SubscriptionController::class, 'cancelPayment'])
            ->name('subscriptions.cancel-payment');
        Route::post('/subscriptions/resume', [\App\Http\Controllers\Account\SubscriptionController::class, 'resumeSubscription'])
            ->name('subscriptions.resume');
        Route::post('/subscriptions/update-payment-method', [\App\Http\Controllers\Account\SubscriptionController::class, 'updatePaymentMethod'])
            ->name('subscriptions.update-payment-method');
        Route::get('/payments', [\App\Http\Controllers\Account\PaymentController::class, 'index'])->name('payments');
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Account\InvoiceController::class, 'index'])->name('index');
            Route::get('/{invoice}', [\App\Http\Controllers\Account\InvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/pdf', [\App\Http\Controllers\Account\InvoiceController::class, 'downloadPdf'])->name('pdf');
        });
        Route::get('/settings', [\App\Http\Controllers\Account\SettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Account\SettingsController::class, 'update'])->name('settings.update');
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::prefix('developer')->name('developer')->middleware('plan.feature:api_access')->group(function () {
                Route::get('/', [\App\Http\Controllers\Settings\DeveloperController::class, 'index'])->name('');
                Route::post('/tokens', [\App\Http\Controllers\Settings\DeveloperController::class, 'store'])->name('.store');
                Route::post('/tokens/{token}/show', [\App\Http\Controllers\Settings\DeveloperController::class, 'show'])->name('.show');
                Route::put('/tokens/{token}', [\App\Http\Controllers\Settings\DeveloperController::class, 'update'])->name('.update');
                Route::post('/tokens/{token}/regenerate', [\App\Http\Controllers\Settings\DeveloperController::class, 'regenerate'])->name('.regenerate');
                Route::post('/tokens/{token}/deactivate', [\App\Http\Controllers\Settings\DeveloperController::class, 'deactivate'])->name('.deactivate');
                Route::post('/tokens/{token}/activate', [\App\Http\Controllers\Settings\DeveloperController::class, 'activate'])->name('.activate');
                Route::delete('/tokens/{token}', [\App\Http\Controllers\Settings\DeveloperController::class, 'destroy'])->name('.revoke');
                Route::delete('/tokens', [\App\Http\Controllers\Settings\DeveloperController::class, 'destroyAll'])->name('.revoke-all');
                Route::post('/verify-password', [\App\Http\Controllers\Settings\DeveloperController::class, 'verifyPassword'])->name('.verify-password');
            });
        });
    });

    // Invitation management
    Route::prefix('invitations')->name('invitations.')->group(function () {
        Route::post('/{invitation}/accept', [\App\Http\Controllers\WorkspaceInvitationController::class, 'doAccept'])
            ->middleware('throttle:web-sensitive')
            ->name('do-accept');
        Route::post('/{invitation}/decline', [\App\Http\Controllers\WorkspaceInvitationController::class, 'doDecline'])
            ->middleware('throttle:web-sensitive')
            ->name('do-decline');
        Route::delete('/{invitation}', [\App\Http\Controllers\WorkspaceInvitationController::class, 'cancel'])
            ->middleware('throttle:web-sensitive')
            ->name('cancel');
        Route::post('/{invitation}/resend', [\App\Http\Controllers\WorkspaceInvitationController::class, 'resend'])
            ->middleware(['throttle:web-invite-resend', 'permission:workspace-user.invite', 'plan.feature:team_management'])
            ->name('resend');
    });

    Route::post('/workspace/switch/{workspace}', [WorkspaceSwitchController::class, 'switch'])
        ->name('workspace.switch');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->middleware(['permission:activity-log.view', 'plan.feature:activity_logs'])
        ->name('activity.logs');
    Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])
        ->middleware(['permission:activity-log.view', 'plan.feature:activity_logs'])
        ->name('activity.logs.show');

    Route::prefix('notifications')->name('notifications.')->middleware('permission:notification.view')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
    });

    Route::permanentRedirect('/profile', '/account/profile')->name('profile');

    require __DIR__.'/super-admin.php';

    Route::get('/receipts/{paymentVerification}', [ReceiptController::class, 'show'])
        ->middleware(['auth', 'throttle:web-proof'])->name('receipts.show');

    // Data export / import
    Route::prefix('data')->name('data.')->middleware(['plan.feature:export', 'throttle:web-crud'])->group(function () {
        Route::get('/{entity}/export/{format}', [DataController::class, 'export'])
            ->name('export');
        Route::post('/{entity}/import', [DataController::class, 'import'])
            ->name('import');
        Route::get('/{entity}/template', [DataController::class, 'template'])
            ->name('template');
    });

});
