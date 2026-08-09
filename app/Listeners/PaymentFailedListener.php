<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Services\AdminNotificationService;
use Illuminate\Support\Facades\Log;

class PaymentFailedListener
{
    public function __construct(
        private AdminNotificationService $notificationService,
    ) {}

    public function handle(PaymentFailed $event): void
    {
        try {
            $payment = $event->payment;
            $user = $payment->subscription?->workspace?->owner()?->first() ?? $payment->user;

            if ($user) {
                $this->notificationService->paymentFailed($payment, $user);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to create admin notification for failed payment', [
                'payment_id' => $event->payment->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
