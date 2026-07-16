<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\OnboardingService;
use App\Services\Payments\GatewayManager;
use App\Services\PaymentService;

class PaymentReturnController extends Controller
{
    public function __construct(
        private GatewayManager $gatewayManager,
        private OnboardingService $onboardingService,
    ) {}

    public function checkStatus(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $status = $payment->status;

        if ($status === PaymentStatus::CheckoutPaid) {
            session()->forget('pending_payment_id');

            return response()->json([
                'status' => 'completed',
                'redirect' => route('onboarding.setup'),
            ]);
        }

        if (in_array($status, [PaymentStatus::CheckoutFailed, PaymentStatus::CheckoutCanceled, PaymentStatus::CheckoutExpired])) {
            session()->forget('pending_payment_id');

            return response()->json([
                'status' => $status,
                'redirect' => route('onboarding.plan'),
            ]);
        }

        // For pending online payments with a transaction ID, try verifying with gateway API
        if ($payment->transaction_id) {
            try {
                $gateway = $this->gatewayManager->driver($payment->method);
                $result = $gateway->verify($payment);

                if ($result->success && ($result->metadata['status'] ?? '') === 'paid') {
                    $payment->update([
                        'webhook_payload' => $result->metadata,
                        'webhook_processed_at' => now(),
                    ]);

                    $payment->refresh();

                    app(PaymentService::class)->applyPaymentSideEffects($payment, 'approved');
                    $this->onboardingService->handlePaymentSuccess($payment->user, $payment);

                    session()->forget('pending_payment_id');

                    return response()->json([
                        'status' => 'completed',
                        'redirect' => route('onboarding.setup'),
                    ]);
                }
            } catch (\Throwable) {
                // Gateway unreachable — keep polling
            }
        }

        return response()->json([
            'status' => 'pending',
        ]);
    }
}
