<?php

use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('payment/webhook')->name('payment.webhook.')->middleware('throttle:webhook')->group(function () {
    Route::post('/chargily', [PaymentWebhookController::class, 'chargily'])->name('chargily');
    Route::post('/paypal', [PaymentWebhookController::class, 'paypal'])->name('paypal');
    Route::post('/stripe', [PaymentWebhookController::class, 'stripe'])->name('stripe');
    Route::post('/wise', [PaymentWebhookController::class, 'wise'])->name('wise');
    Route::post('/payoneer', [PaymentWebhookController::class, 'payoneer'])->name('payoneer');
    Route::post('/noest', [PaymentWebhookController::class, 'noest'])->name('noest');
});
