<?php

namespace App\Http\Controllers;

use App\Models\PaymentVerification;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    public function show(PaymentVerification $paymentVerification)
    {
        if (! $paymentVerification->receipt_path || ! Storage::disk('local')->exists($paymentVerification->receipt_path)) {
            abort(404);
        }

        $user = auth()->user();

        if (! $user->hasPermission('payment.verify')) {
            abort(403);
        }

        $payment = $paymentVerification->payment;
        if ($payment && $payment->workspace_id !== $user->current_workspace_id) {
            abort(403);
        }

        return response()->file(Storage::disk('local')->path($paymentVerification->receipt_path));
    }
}
