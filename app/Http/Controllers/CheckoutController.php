<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payments\GatewayManager;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly GatewayManager $gatewayManager,
    ) {}

    public function redirect(Payment $payment): RedirectResponse
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $payment->isPending()) {
            return redirect()->route('dashboard')
                ->with('info', __('payment.already_processed'));
        }

        $gateway = $this->gatewayManager->driver($payment->paymentMethod?->key);

        if (! $gateway->isOnline()) {
            return redirect()->route('onboarding.manual-proof', ['payment' => $payment->id]);
        }

        $result = $gateway->charge([
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'workspace_id' => $payment->workspace_id,
        ]);

        if (! $result->success) {
            $fallbackRoute = $payment->subscription_id
                ? 'account.subscriptions'
                : 'onboarding.plan';

            return redirect()->route($fallbackRoute)
                ->with('error', $result->message);
        }

        $payment->update([
            'transaction_id' => $result->transactionId,
            'gateway_reference' => $result->reference,
            'gateway_payload' => $result->metadata,
            'chargily_checkout_id' => $result->metadata['chargily_checkout_id'] ?? null,
        ]);

        if ($result->redirectUrl) {
            return redirect()->away($result->redirectUrl);
        }

        return redirect()->route('onboarding.manual-proof', ['payment' => $payment->id]);
    }
}
