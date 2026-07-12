<?php

namespace App\Http\Controllers;

use App\Contracts\Webhooks\WebhookSignatureValidator;
use App\Models\Payment;
use App\Services\OnboardingService;
use App\Services\PaymentService;
use App\Services\Payments\Chargily\ChargilyWebhookService;
use App\Services\Payments\Chargily\Exceptions\ChargilyException;
use App\Services\Webhooks\WebhookSignatureManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly OnboardingService $onboardingService,
        private readonly ChargilyWebhookService $chargilyWebhookService,
        private readonly WebhookSignatureManager $signatureManager,
    ) {}

    public function chargily(Request $request)
    {
        try {
            $this->chargilyWebhookService->process();

            return response()->json(['received' => true], 200);
        } catch (ChargilyException $e) {
            Log::warning('Chargily webhook rejected', [
                'reason' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            $status = match (true) {
                str_contains($e->getMessage(), 'missing signature') => 401,
                str_contains($e->getMessage(), 'not configured') => 401,
                str_contains($e->getMessage(), 'signature') => 403,
                str_contains($e->getMessage(), 'Invalid payload') => 400,
                str_contains($e->getMessage(), 'not found') => 404,
                default => 422,
            };

            return response()->json(['error' => $e->getMessage()], $status);
        } catch (\Throwable $e) {
            Log::error('Chargily webhook error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    private function handleApproved(Payment $payment, string $notes): void
    {
        $this->paymentService->verifyPayment($payment, 'approved', null, "Auto-verified via {$notes}");
        $this->maybeCompleteOnboarding($payment);
        $payment->refresh();

        event(new \App\Events\PaymentCompleted($payment));
    }

    private function handleRejected(Payment $payment, string $notes): void
    {
        $this->paymentService->verifyPayment($payment, 'rejected', null, $notes);
    }

    private function maybeCompleteOnboarding(Payment $payment): void
    {
        if (!$payment->user_id) return;

        $user = \App\Models\User::find($payment->user_id);
        if ($user && $user->pending_plan_id) {
            $this->onboardingService->handlePaymentSuccess($user, $payment);
        }
    }

    public function paypal(Request $request)
    {
        if (!$this->signatureManager->validate('paypal', $request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();
        $this->logWebhookEvent('paypal', $payload, $payload['event_type'] ?? null, $payload['resource']['id'] ?? $payload['resource']['sale_id'] ?? null, $payload['resource']['state'] ?? null);

        $eventType = $payload['event_type'] ?? null;
        $resource = $payload['resource'] ?? [];

        $transactionId = $resource['id'] ?? $resource['sale_id'] ?? null;

        if (!$transactionId) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            Log::warning('PayPal webhook: Payment not found', ['transaction_id' => $transactionId]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($eventType === 'PAYMENT.SALE.COMPLETED') {
            $this->handleApproved($payment, 'PayPal webhook');
        } elseif (in_array($eventType, ['PAYMENT.SALE.DENIED', 'PAYMENT.SALE.REFUNDED'])) {
            $this->handleRejected($payment, "PayPal event: {$eventType}");
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    public function stripe(Request $request)
    {
        if (!$this->signatureManager->validate('stripe', $request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();
        $this->logWebhookEvent('stripe', $payload, $payload['type'] ?? null, $payload['data']['object']['id'] ?? null, $payload['data']['object']['status'] ?? null);

        $eventType = $payload['type'] ?? null;
        $paymentIntent = $payload['data']['object'] ?? [];

        $transactionId = $paymentIntent['id'] ?? null;

        if (!$eventType || !$transactionId) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            Log::warning('Stripe webhook: Payment not found', ['transaction_id' => $transactionId]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($eventType === 'payment_intent.succeeded') {
            $this->handleApproved($payment, 'Stripe webhook');
        } elseif (in_array($eventType, ['payment_intent.payment_failed', 'payment_intent.canceled'])) {
            $this->handleRejected($payment, "Stripe event: {$eventType}");
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    public function wise(Request $request)
    {
        if (!$this->signatureManager->validate('wise', $request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();
        $this->logWebhookEvent('wise', $payload, $payload['data']['status'] ?? $payload['resource']['status'] ?? null, $payload['data']['transfer_id'] ?? $payload['resource']['id'] ?? $payload['transfer_id'] ?? null, $payload['data']['status'] ?? $payload['resource']['status'] ?? $payload['current_state'] ?? null);

        $transactionId = $payload['data']['transfer_id'] ?? $payload['resource']['id'] ?? $payload['transfer_id'] ?? null;
        $status = $payload['data']['status'] ?? $payload['resource']['status'] ?? $payload['current_state'] ?? null;

        if (!$transactionId || !$status) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $payment = Payment::where('transaction_id', (string) $transactionId)->first();

        if (!$payment) {
            Log::warning('Wise webhook: Payment not found', ['transaction_id' => $transactionId]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if (in_array($status, ['completed', 'outgoing_payment_sent'])) {
            $this->handleApproved($payment, 'Wise webhook');
        } elseif (in_array($status, ['failed', 'cancelled', 'refunded'])) {
            $this->handleRejected($payment, "Wise status: {$status}");
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    private function logWebhookEvent(string $provider, array $payload, ?string $eventType = null, ?string $transactionId = null, ?string $status = null): void
    {
        $safeContext = array_filter([
            'provider' => $provider,
            'event_type' => $eventType,
            'transaction_id' => $transactionId,
            'status' => $status,
            'timestamp' => $payload['timestamp'] ?? $payload['created_at'] ?? $payload['date'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');

        Log::info('Webhook received', $safeContext);
    }

    public function payoneer(Request $request)
    {
        if (!$this->signatureManager->validate('payoneer', $request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();
        $this->logWebhookEvent('payoneer', $payload, $payload['status'] ?? $payload['resource']['status'] ?? null, $payload['payout_id'] ?? $payload['resource']['id'] ?? null, $payload['status'] ?? $payload['resource']['status'] ?? null);

        $transactionId = $payload['payout_id'] ?? $payload['resource']['id'] ?? null;
        $status = $payload['status'] ?? $payload['resource']['status'] ?? null;

        if (!$transactionId || !$status) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            Log::warning('Payoneer webhook: Payment not found', ['transaction_id' => $transactionId]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($status === 'paid') {
            $this->handleApproved($payment, 'Payoneer webhook');
        } elseif (in_array($status, ['failed', 'refunded', 'cancelled'])) {
            $this->handleRejected($payment, "Payoneer status: {$status}");
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    public function noest(Request $request)
    {
        if (!$this->signatureManager->validate('noest', $request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();
        $this->logWebhookEvent('noest', $payload, $payload['status'] ?? $payload['event'] ?? null, $payload['tracking'] ?? $payload['data']['tracking'] ?? null, $payload['status'] ?? null);

        $transactionId = $payload['tracking'] ?? $payload['data']['tracking'] ?? null;
        $status = $payload['status'] ?? $payload['event'] ?? $payload['data']['status'] ?? null;

        if (!$transactionId || !$status) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            Log::warning('Noest webhook: Payment not found', ['tracking' => $transactionId]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if (in_array(strtolower($status), ['delivered', 'completed', 'confirmed'])) {
            $payment->update(['gateway_payload' => $payload]);
            $this->handleApproved($payment, 'Noest webhook (delivery confirmed)');
        } elseif (in_array(strtolower($status), ['cancelled', 'canceled', 'returned', 'failed'])) {
            $this->handleRejected($payment, "Noest status: {$status}");
        }

        return response()->json(['message' => 'Webhook processed']);
    }

}
