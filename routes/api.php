<?php

use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DebtController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\IncomeCategoryController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:api-auth');
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:api-auth');

Route::middleware(['auth:sanctum', 'throttle:api', 'quota'])->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::middleware(['subscription.api', 'ability'])->group(function () {
        Route::get('/workspaces', [WorkspaceController::class, 'index']);
        Route::post('/workspaces', [WorkspaceController::class, 'store']);
        Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show']);
        Route::put('/workspaces/{workspace}', [WorkspaceController::class, 'update']);
        Route::delete('/workspaces/{workspace}', [WorkspaceController::class, 'destroy']);
        Route::post('/workspaces/{workspace}/switch', [WorkspaceController::class, 'switch']);

        Route::prefix('workspace')->middleware(['workspace', 'throttle:api-workspace'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index']);

            Route::apiResource('incomes', IncomeController::class);
            Route::apiResource('income-categories', IncomeCategoryController::class);
            Route::apiResource('expenses', ExpenseController::class);
            Route::apiResource('expense-categories', ExpenseCategoryController::class);
            Route::apiResource('assets', AssetController::class);
            Route::apiResource('debts', DebtController::class);
            Route::apiResource('goals', GoalController::class);
            Route::apiResource('budgets', BudgetController::class);

            Route::get('transactions', [TransactionController::class, 'index']);

            Route::get('subscription', [SubscriptionController::class, 'current']);
            Route::post('subscription/change-plan', [SubscriptionController::class, 'changePlan'])->middleware('throttle:api-sensitive');
            Route::post('subscription/cancel', [SubscriptionController::class, 'cancel'])->middleware('throttle:api-sensitive');
        });
    });

    Route::middleware('ability')->group(function () {
        Route::get('plans', [SubscriptionController::class, 'plans']);
        Route::post('coupon/validate', [SubscriptionController::class, 'validateCoupon'])->middleware('throttle:api-sensitive');
    });
});
