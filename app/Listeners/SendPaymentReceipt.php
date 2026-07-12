<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use Illuminate\Support\Facades\Log;

class SendPaymentReceipt
{
    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment;
        $user = $payment->user;

        if (!$user || !$user->email) {
            return;
        }

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->queue(new \App\Mail\PaymentReceipt($payment));
        } catch (\Throwable $e) {
            Log::warning('Failed to send payment receipt', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
