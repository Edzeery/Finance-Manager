<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\PaymentWebhookLogStatus;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PaymentWebhookLoggingTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspace;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('payment.webhook_secret', 'test_webhook_secret');
        $this->withHeader('X-Webhook-Token', 'test_webhook_secret');

        $this->workspace = Workspace::factory()->create();
        $this->user = User::factory()->create();
        $this->workspace->users()->attach($this->user->id, []);
    }

    public function test_paypal_webhook_creates_processed_log(): void
    {
        $payment = Payment::factory()->forMethod('paypal')->create([
            'transaction_id' => 'PAY-LOG1',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/paypal', [
            'event_type' => 'PAYMENT.SALE.COMPLETED',
            'resource' => ['id' => 'PAY-LOG1'],
        ]);

        $response->assertOk();

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('paypal', $log->gateway);
        $this->assertSame('PAYMENT.SALE.COMPLETED', $log->event_type);
        $this->assertSame('PAY-LOG1', $log->checkout_id);
        $this->assertSame($payment->id, $log->payment_id);
        $this->assertSame('PAYMENT.SALE.COMPLETED', $log->payload['event_type']);
        $this->assertSame(PaymentWebhookLogStatus::Processed, $log->fresh()->status);
    }

    public function test_paypal_webhook_logs_failed_when_payment_not_found(): void
    {
        $response = $this->postJson('/payment/webhook/paypal', [
            'event_type' => 'PAYMENT.SALE.COMPLETED',
            'resource' => ['id' => 'NONEXISTENT'],
        ]);

        $response->assertStatus(404);

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('paypal', $log->gateway);
        $this->assertNull($log->payment_id);
        $this->assertSame(PaymentWebhookLogStatus::Failed, $log->fresh()->status);
        $this->assertStringContainsString('Payment not found', (string) $log->notes);
    }

    public function test_paypal_webhook_logs_failed_when_invalid_payload(): void
    {
        $response = $this->postJson('/payment/webhook/paypal', []);

        $response->assertStatus(400);

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('paypal', $log->gateway);
        $this->assertSame(PaymentWebhookLogStatus::Failed, $log->fresh()->status);
    }

    public function test_stripe_webhook_creates_processed_log(): void
    {
        $payment = Payment::factory()->forMethod('stripe')->create([
            'transaction_id' => 'pi_log1',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/stripe', [
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_log1', 'status' => 'succeeded']],
        ]);

        $response->assertOk();

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('stripe', $log->gateway);
        $this->assertSame('payment_intent.succeeded', $log->event_type);
        $this->assertSame('pi_log1', $log->checkout_id);
        $this->assertSame($payment->id, $log->payment_id);
        $this->assertSame(PaymentWebhookLogStatus::Processed, $log->fresh()->status);
    }

    public function test_stripe_webhook_logs_failed_when_payment_not_found(): void
    {
        $response = $this->postJson('/payment/webhook/stripe', [
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_nonexistent']],
        ]);

        $response->assertStatus(404);

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertNull($log->payment_id);
        $this->assertSame(PaymentWebhookLogStatus::Failed, $log->fresh()->status);
    }

    public function test_stripe_webhook_logs_failed_when_invalid_payload(): void
    {
        $response = $this->postJson('/payment/webhook/stripe', []);

        $response->assertStatus(400);

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('stripe', $log->gateway);
        $this->assertSame(PaymentWebhookLogStatus::Failed, $log->fresh()->status);
    }

    public function test_wise_webhook_creates_processed_log(): void
    {
        $payment = Payment::factory()->forMethod('wise')->create([
            'transaction_id' => '424242',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/wise', [
            'data' => ['transfer_id' => 424242, 'status' => 'completed'],
        ]);

        $response->assertOk();

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('wise', $log->gateway);
        $this->assertSame('completed', $log->event_type);
        $this->assertSame('424242', $log->checkout_id);
        $this->assertSame($payment->id, $log->payment_id);
        $this->assertSame(PaymentWebhookLogStatus::Processed, $log->fresh()->status);
    }

    public function test_wise_webhook_logs_failed_when_payment_not_found(): void
    {
        $response = $this->postJson('/payment/webhook/wise', [
            'data' => ['transfer_id' => 99999, 'status' => 'completed'],
        ]);

        $response->assertStatus(404);

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertNull($log->payment_id);
        $this->assertSame(PaymentWebhookLogStatus::Failed, $log->fresh()->status);
    }

    public function test_wise_webhook_logs_failed_when_invalid_payload(): void
    {
        $response = $this->postJson('/payment/webhook/wise', []);

        $response->assertStatus(400);

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('wise', $log->gateway);
        $this->assertSame(PaymentWebhookLogStatus::Failed, $log->fresh()->status);
    }

    public function test_payoneer_webhook_creates_processed_log(): void
    {
        $payment = Payment::factory()->forMethod('payoneer')->create([
            'transaction_id' => 'payout_log1',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/payoneer', [
            'payout_id' => 'payout_log1',
            'status' => 'paid',
        ]);

        $response->assertOk();

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('payoneer', $log->gateway);
        $this->assertSame('paid', $log->event_type);
        $this->assertSame('payout_log1', $log->checkout_id);
        $this->assertSame($payment->id, $log->payment_id);
        $this->assertSame(PaymentWebhookLogStatus::Processed, $log->fresh()->status);
    }

    public function test_payoneer_webhook_logs_failed_when_payment_not_found(): void
    {
        $response = $this->postJson('/payment/webhook/payoneer', [
            'payout_id' => 'nonexistent',
            'status' => 'paid',
        ]);

        $response->assertStatus(404);

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertNull($log->payment_id);
        $this->assertSame(PaymentWebhookLogStatus::Failed, $log->fresh()->status);
    }

    public function test_payoneer_webhook_logs_failed_when_invalid_payload(): void
    {
        $response = $this->postJson('/payment/webhook/payoneer', []);

        $response->assertStatus(400);

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('payoneer', $log->gateway);
        $this->assertSame(PaymentWebhookLogStatus::Failed, $log->fresh()->status);
    }

    public function test_noest_webhook_creates_processed_log(): void
    {
        $payment = Payment::factory()->forMethod('noest')->create([
            'transaction_id' => 'noest_log1',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/noest', [
            'tracking' => 'noest_log1',
            'status' => 'delivered',
        ]);

        $response->assertOk();

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('noest', $log->gateway);
        $this->assertSame('delivered', $log->event_type);
        $this->assertSame('noest_log1', $log->checkout_id);
        $this->assertSame($payment->id, $log->payment_id);
        $this->assertSame(PaymentWebhookLogStatus::Processed, $log->fresh()->status);
    }

    public function test_noest_webhook_logs_failed_when_payment_not_found(): void
    {
        $response = $this->postJson('/payment/webhook/noest', [
            'tracking' => 'noest_missing',
            'status' => 'delivered',
        ]);

        $response->assertStatus(404);

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertNull($log->payment_id);
        $this->assertSame(PaymentWebhookLogStatus::Failed, $log->fresh()->status);
    }

    public function test_noest_webhook_logs_failed_when_invalid_payload(): void
    {
        $response = $this->postJson('/payment/webhook/noest', []);

        $response->assertStatus(400);

        $log = PaymentWebhookLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('noest', $log->gateway);
        $this->assertSame(PaymentWebhookLogStatus::Failed, $log->fresh()->status);
    }

    public function test_duplicate_webhook_event_updates_existing_log(): void
    {
        $payment = Payment::factory()->forMethod('paypal')->create([
            'transaction_id' => 'PAY-DUP1',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $payload = [
            'event_type' => 'PAYMENT.SALE.COMPLETED',
            'resource' => ['id' => 'PAY-DUP1'],
        ];

        $this->postJson('/payment/webhook/paypal', $payload)->assertOk();
        $this->postJson('/payment/webhook/paypal', $payload)->assertOk();

        $this->assertSame(1, PaymentWebhookLog::where('gateway', 'paypal')->where('checkout_id', 'PAY-DUP1')->count());
        $this->assertSame($payment->id, PaymentWebhookLog::query()->first()->payment_id);
        $this->assertSame(PaymentWebhookLogStatus::Processed, PaymentWebhookLog::query()->first()->fresh()->status);
    }
}
