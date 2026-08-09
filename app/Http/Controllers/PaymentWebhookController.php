<?php

namespace App\Http\Controllers;

use App\Enums\PaymentVerificationStatus;
use App\Enums\PaymentWebhookLogStatus;
use App\Events\PaymentCompleted;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use App\Models\User;
use App\Services\OnboardingService;
use App\Services\Payments\Chargily\ChargilyWebhookService;
use App\Services\Payments\Chargily\Exceptions\ChargilyException;
use App\Services\PaymentService;
use App\Services\Webhooks\WebhookSignatureManager;
use Illuminate\Database\QueryException;
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
        $this->paymentService->verifyPayment($payment, PaymentVerificationStatus::Approved, null, "Auto-verified via {$notes}");
        $this->maybeCompleteOnboarding($payment);
        $payment->refresh();

        event(new PaymentCompleted($payment));
    }

    private function handleRejected(Payment $payment, string $notes): void
    {
        $this->paymentService->verifyPayment($payment, PaymentVerificationStatus::Rejected, null, $notes);
    }

    private function maybeCompleteOnboarding(Payment $payment): void
    {
        if (! $payment->user_id) {
            return;
        }

        $user = User::find($payment->user_id);
        if ($user && $user->pending_plan_id) {
            $this->onboardingService->handlePaymentSuccess($user, $payment);
        }
    }

    public function paypal(Request $request)
    {
        if (! $this->signatureManager->validate('paypal', $request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();
        $eventType = $payload['event_type'] ?? null;
        $resource = $payload['resource'] ?? [];
        $transactionId = $resource['id'] ?? $resource['sale_id'] ?? null;

        $log = $this->logWebhookEvent('paypal', $payload, $eventType, $transactionId, $resource['state'] ?? null);

        if (! $transactionId) {
            $this->markWebhookFailed($log, 'Invalid payload');

            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (! $payment) {
            Log::warning('PayPal webhook: Payment not found', ['transaction_id' => $transactionId]);
            $this->markWebhookFailed($log, "Payment not found: {$transactionId}");

            return response()->json(['error' => 'Payment not found'], 404);
        }

        $log->update(['payment_id' => $payment->id]);

        try {
            if ($eventType === 'PAYMENT.SALE.COMPLETED') {
                $this->handleApproved($payment, 'PayPal webhook');
            } elseif (in_array($eventType, ['PAYMENT.SALE.DENIED', 'PAYMENT.SALE.REFUNDED'])) {
                $this->handleRejected($payment, "PayPal event: {$eventType}");
            }

            $this->markWebhookProcessed($log);
        } catch (\Throwable $e) {
            Log::error('PayPal webhook failed', ['transaction_id' => $transactionId, 'error' => $e->getMessage()]);
            $this->markWebhookFailed($log, $e->getMessage());

            return response()->json(['error' => 'Internal error'], 500);
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    public function stripe(Request $request)
    {
        if (! $this->signatureManager->validate('stripe', $request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();
        $eventType = $payload['type'] ?? null;
        $paymentIntent = $payload['data']['object'] ?? [];
        $transactionId = $paymentIntent['id'] ?? null;

        $log = $this->logWebhookEvent('stripe', $payload, $eventType, $transactionId, $paymentIntent['status'] ?? null);

        if (! $eventType || ! $transactionId) {
            $this->markWebhookFailed($log, 'Invalid payload');

            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (! $payment) {
            Log::warning('Stripe webhook: Payment not found', ['transaction_id' => $transactionId]);
            $this->markWebhookFailed($log, "Payment not found: {$transactionId}");

            return response()->json(['error' => 'Payment not found'], 404);
        }

        $log->update(['payment_id' => $payment->id]);

        try {
            if ($eventType === 'payment_intent.succeeded') {
                $this->handleApproved($payment, 'Stripe webhook');
            } elseif (in_array($eventType, ['payment_intent.payment_failed', 'payment_intent.canceled'])) {
                $this->handleRejected($payment, "Stripe event: {$eventType}");
            }

            $this->markWebhookProcessed($log);
        } catch (\Throwable $e) {
            Log::error('Stripe webhook failed', ['transaction_id' => $transactionId, 'error' => $e->getMessage()]);
            $this->markWebhookFailed($log, $e->getMessage());

            return response()->json(['error' => 'Internal error'], 500);
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    public function wise(Request $request)
    {
        if (! $this->signatureManager->validate('wise', $request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();
        $transactionId = $payload['data']['transfer_id'] ?? $payload['resource']['id'] ?? $payload['transfer_id'] ?? null;
        $status = $payload['data']['status'] ?? $payload['resource']['status'] ?? $payload['current_state'] ?? null;

        $log = $this->logWebhookEvent('wise', $payload, $status, $transactionId, $status);

        if (! $transactionId || ! $status) {
            $this->markWebhookFailed($log, 'Invalid payload');

            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $payment = Payment::where('transaction_id', (string) $transactionId)->first();

        if (! $payment) {
            Log::warning('Wise webhook: Payment not found', ['transaction_id' => $transactionId]);
            $this->markWebhookFailed($log, "Payment not found: {$transactionId}");

            return response()->json(['error' => 'Payment not found'], 404);
        }

        $log->update(['payment_id' => $payment->id]);

        try {
            if (in_array($status, ['completed', 'outgoing_payment_sent'])) {
                $this->handleApproved($payment, 'Wise webhook');
            } elseif (in_array($status, ['failed', 'cancelled', 'refunded'])) {
                $this->handleRejected($payment, "Wise status: {$status}");
            }

            $this->markWebhookProcessed($log);
        } catch (\Throwable $e) {
            Log::error('Wise webhook failed', ['transaction_id' => $transactionId, 'error' => $e->getMessage()]);
            $this->markWebhookFailed($log, $e->getMessage());

            return response()->json(['error' => 'Internal error'], 500);
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    private function logWebhookEvent(string $provider, array $payload, ?string $eventType = null, ?string $transactionId = null, ?string $status = null, ?Payment $payment = null): PaymentWebhookLog
    {
        $safeContext = array_filter([
            'provider' => $provider,
            'event_type' => $eventType,
            'transaction_id' => $transactionId,
            'status' => $status,
            'timestamp' => $payload['timestamp'] ?? $payload['created_at'] ?? $payload['date'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');

        Log::info('Webhook received', $safeContext);

        try {
            return PaymentWebhookLog::create([
                'gateway' => $provider,
                'event_type' => $eventType,
                'checkout_id' => $transactionId,
                'payment_id' => $payment?->id,
                'payload' => $payload,
                'status' => PaymentWebhookLogStatus::Received->value,
            ]);
        } catch (QueryException $e) {
            $message = $e->getMessage();
            if (str_contains($message, 'Duplicate entry') || str_contains($message, 'UNIQUE constraint')) {
                return PaymentWebhookLog::where('checkout_id', $transactionId)
                    ->where('event_type', $eventType)
                    ->latest()
                    ->first() ?? new PaymentWebhookLog;
            }

            throw $e;
        }
    }

    private function markWebhookProcessed(PaymentWebhookLog $log): void
    {
        if ($log->exists) {
            $log->update(['status' => PaymentWebhookLogStatus::Processed->value]);
        }
    }

    private function markWebhookFailed(PaymentWebhookLog $log, string $notes): void
    {
        if ($log->exists) {
            $log->update([
                'status' => PaymentWebhookLogStatus::Failed->value,
                'notes' => $notes,
            ]);
        }
    }

    public function payoneer(Request $request)
    {
        if (! $this->signatureManager->validate('payoneer', $request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();
        $transactionId = $payload['payout_id'] ?? $payload['resource']['id'] ?? null;
        $status = $payload['status'] ?? $payload['resource']['status'] ?? null;

        $log = $this->logWebhookEvent('payoneer', $payload, $status, $transactionId, $status);

        if (! $transactionId || ! $status) {
            $this->markWebhookFailed($log, 'Invalid payload');

            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (! $payment) {
            Log::warning('Payoneer webhook: Payment not found', ['transaction_id' => $transactionId]);
            $this->markWebhookFailed($log, "Payment not found: {$transactionId}");

            return response()->json(['error' => 'Payment not found'], 404);
        }

        $log->update(['payment_id' => $payment->id]);

        try {
            if ($status === 'paid') {
                $this->handleApproved($payment, 'Payoneer webhook');
            } elseif (in_array($status, ['failed', 'refunded', 'cancelled'])) {
                $this->handleRejected($payment, "Payoneer status: {$status}");
            }

            $this->markWebhookProcessed($log);
        } catch (\Throwable $e) {
            Log::error('Payoneer webhook failed', ['transaction_id' => $transactionId, 'error' => $e->getMessage()]);
            $this->markWebhookFailed($log, $e->getMessage());

            return response()->json(['error' => 'Internal error'], 500);
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    public function noest(Request $request)
    {
        if (! $this->signatureManager->validate('noest', $request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();
        $transactionId = $payload['tracking'] ?? $payload['data']['tracking'] ?? null;
        $status = $payload['status'] ?? $payload['event'] ?? $payload['data']['status'] ?? null;

        $log = $this->logWebhookEvent('noest', $payload, $status, $transactionId, $status);

        if (! $transactionId || ! $status) {
            $this->markWebhookFailed($log, 'Invalid payload');

            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (! $payment) {
            Log::warning('Noest webhook: Payment not found', ['tracking' => $transactionId]);
            $this->markWebhookFailed($log, "Payment not found: {$transactionId}");

            return response()->json(['error' => 'Payment not found'], 404);
        }

        $log->update(['payment_id' => $payment->id]);

        try {
            if (in_array(strtolower($status), ['delivered', 'completed', 'confirmed'])) {
                $payment->update(['gateway_payload' => $payload]);
                $this->handleApproved($payment, 'Noest webhook (delivery confirmed)');
            } elseif (in_array(strtolower($status), ['cancelled', 'canceled', 'returned', 'failed'])) {
                $this->handleRejected($payment, "Noest status: {$status}");
            }

            $this->markWebhookProcessed($log);
        } catch (\Throwable $e) {
            Log::error('Noest webhook failed', ['tracking' => $transactionId, 'error' => $e->getMessage()]);
            $this->markWebhookFailed($log, $e->getMessage());

            return response()->json(['error' => 'Internal error'], 500);
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
