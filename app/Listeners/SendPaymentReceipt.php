<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Mail\PaymentReceipt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentReceipt
{
    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment;
        $user = $payment->user;

        if (! $user || ! $user->email) {
            return;
        }

        try {
            Mail::to($user->email)
                ->queue(new PaymentReceipt($payment));
        } catch (\Throwable $e) {
            Log::warning('Failed to send payment receipt', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
