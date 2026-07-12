<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Workspace;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\Helpers\ChargilyWebhookPayload;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;
    use ChargilyWebhookPayload;

    private const CHARGILY_SECRET = 'test_chargily_secret_key_12345';

    private Workspace $workspace;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('payment.gateways.chargily.secret_key', self::CHARGILY_SECRET);
        Config::set('payment.webhook_secret', 'test_webhook_secret');

        $this->withHeader('X-Webhook-Token', 'test_webhook_secret');

        $this->workspace = Workspace::factory()->create();
        $this->user = User::factory()->create();
        $this->workspace->users()->attach($this->user->id, []);
    }

    private function signedChargilyRequest(string $rawBody, ?string $secret = null): array
    {
        $signature = hash_hmac('sha256', $rawBody, $secret ?? self::CHARGILY_SECRET);
        return ['signature' => $signature];
    }

    public function test_chargily_webhook_approves_payment(): void
    {
        $this->markTestSkipped('Chargily webhook requires real HTTP (ngrok) — library reads php://input which is empty in CLI');

        $payment = Payment::factory()->create([
            'method' => 'chargily',
            'transaction_id' => 'ch_12345',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $rawBody = $this->chargilyPayload([
            'type' => 'checkout.paid',
            'data' => [
                'id' => 'ch_12345',
                'status' => 'paid',
                'metadata' => ['payment_id' => (string) $payment->id],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [],
            [],
            [],
            $this->transformHeadersToServerVars($this->signedChargilyRequest($rawBody)),
            $rawBody,
        );

        $response->assertOk();
        $this->assertEquals(PaymentStatus::CheckoutPaid, $payment->fresh()->status);
    }

    public function test_chargily_webhook_rejects_failed_payment(): void
    {
        $this->markTestSkipped('Chargily webhook requires real HTTP (ngrok) — library reads php://input which is empty in CLI');

        $payment = Payment::factory()->create([
            'method' => 'chargily',
            'transaction_id' => 'ch_67890',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $rawBody = $this->chargilyPayload([
            'type' => 'checkout.failed',
            'data' => [
                'id' => 'ch_67890',
                'status' => 'failed',
                'metadata' => ['payment_id' => (string) $payment->id],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [],
            [],
            [],
            $this->transformHeadersToServerVars($this->signedChargilyRequest($rawBody)),
            $rawBody,
        );

        $response->assertOk();
        $this->assertEquals(PaymentStatus::CheckoutFailed, $payment->fresh()->status);
    }

    public function test_chargily_webhook_missing_signature(): void
    {
        $rawBody = $this->chargilyPayload();

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [],
            [],
            [],
            [],
            $rawBody,
        );

        $response->assertStatus(401);
    }

    public function test_chargily_webhook_invalid_signature(): void
    {
        $rawBody = $this->chargilyPayload();

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [],
            [],
            [],
            $this->transformHeadersToServerVars(['signature' => 'invalid_signature_value']),
            $rawBody,
        );

        $response->assertStatus(403);
    }

    public function test_chargily_webhook_no_secret_configured(): void
    {
        $this->markTestSkipped('Chargily webhook requires real HTTP (ngrok) — library reads php://input which is empty in CLI');

        Config::set('payment.gateways.chargily.secret_key', '');

        $rawBody = $this->chargilyPayload([
            'data' => ['metadata' => ['payment_id' => '99999']],
        ]);

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [],
            [],
            [],
            $this->transformHeadersToServerVars($this->signedChargilyRequest($rawBody)),
            $rawBody,
        );

        $response->assertStatus(401);
    }

    public function test_chargily_webhook_invalid_payload(): void
    {
        $this->markTestSkipped('Chargily webhook requires real HTTP (ngrok) — library reads php://input which is empty in CLI');

        $rawBody = '{}';

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [],
            [],
            [],
            $this->transformHeadersToServerVars($this->signedChargilyRequest($rawBody)),
            $rawBody,
        );

        $response->assertStatus(400);
    }

    public function test_chargily_webhook_payment_not_found(): void
    {
        $this->markTestSkipped('Chargily webhook requires real HTTP (ngrok) — library reads php://input which is empty in CLI');

        $rawBody = $this->chargilyPayload([
            'data' => [
                'id' => 'ch_nonexistent',
                'metadata' => ['payment_id' => '99999'],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [],
            [],
            [],
            $this->transformHeadersToServerVars($this->signedChargilyRequest($rawBody)),
            $rawBody,
        );

        $response->assertStatus(404);
    }

    public function test_paypal_webhook_approves_payment(): void
    {
        $payment = Payment::factory()->create([
            'method' => 'paypal',
            'transaction_id' => 'PAY-TEST123',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/paypal', [
            'event_type' => 'PAYMENT.SALE.COMPLETED',
            'resource' => ['id' => 'PAY-TEST123'],
        ]);

        $response->assertOk();
        $this->assertEquals(PaymentStatus::CheckoutPaid, $payment->fresh()->status);
    }

    public function test_paypal_webhook_rejects_denied_payment(): void
    {
        $payment = Payment::factory()->create([
            'method' => 'paypal',
            'transaction_id' => 'PAY-TEST456',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/paypal', [
            'event_type' => 'PAYMENT.SALE.DENIED',
            'resource' => ['id' => 'PAY-TEST456'],
        ]);

        $response->assertOk();
        $this->assertEquals(PaymentStatus::CheckoutFailed, $payment->fresh()->status);
    }

    public function test_paypal_webhook_invalid_payload(): void
    {
        $response = $this->postJson('/payment/webhook/paypal', []);

        $response->assertStatus(400);
    }

    public function test_paypal_webhook_payment_not_found(): void
    {
        $response = $this->postJson('/payment/webhook/paypal', [
            'event_type' => 'PAYMENT.SALE.COMPLETED',
            'resource' => ['id' => 'NONEXISTENT'],
        ]);

        $response->assertStatus(404);
    }

    public function test_stripe_webhook_approves_payment(): void
    {
        $payment = Payment::factory()->create([
            'method' => 'stripe',
            'transaction_id' => 'pi_test123',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/stripe', [
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_test123']],
        ]);

        $response->assertOk();
        $this->assertEquals(PaymentStatus::CheckoutPaid, $payment->fresh()->status);
    }

    public function test_stripe_webhook_rejects_failed_payment(): void
    {
        $payment = Payment::factory()->create([
            'method' => 'stripe',
            'transaction_id' => 'pi_test456',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/stripe', [
            'type' => 'payment_intent.payment_failed',
            'data' => ['object' => ['id' => 'pi_test456']],
        ]);

        $response->assertOk();
        $this->assertEquals(PaymentStatus::CheckoutFailed, $payment->fresh()->status);
    }

    public function test_stripe_webhook_invalid_payload(): void
    {
        $response = $this->postJson('/payment/webhook/stripe', []);

        $response->assertStatus(400);
    }

    public function test_stripe_webhook_payment_not_found(): void
    {
        $response = $this->postJson('/payment/webhook/stripe', [
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_nonexistent']],
        ]);

        $response->assertStatus(404);
    }

    public function test_wise_webhook_approves_payment(): void
    {
        $payment = Payment::factory()->create([
            'method' => 'wise',
            'transaction_id' => '12345',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/wise', [
            'data' => ['transfer_id' => 12345, 'status' => 'completed'],
        ]);

        $response->assertOk();
        $this->assertEquals(PaymentStatus::CheckoutPaid, $payment->fresh()->status);
    }

    public function test_wise_webhook_rejects_failed_payment(): void
    {
        $payment = Payment::factory()->create([
            'method' => 'wise',
            'transaction_id' => '67890',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/wise', [
            'data' => ['transfer_id' => 67890, 'status' => 'failed'],
        ]);

        $response->assertOk();
        $this->assertEquals(PaymentStatus::CheckoutFailed, $payment->fresh()->status);
    }

    public function test_wise_webhook_invalid_payload(): void
    {
        $response = $this->postJson('/payment/webhook/wise', []);

        $response->assertStatus(400);
    }

    public function test_wise_webhook_payment_not_found(): void
    {
        $response = $this->postJson('/payment/webhook/wise', [
            'data' => ['transfer_id' => 99999, 'status' => 'completed'],
        ]);

        $response->assertStatus(404);
    }

    public function test_payoneer_webhook_approves_payment(): void
    {
        $payment = Payment::factory()->create([
            'method' => 'payoneer',
            'transaction_id' => 'payout_test123',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/payoneer', [
            'payout_id' => 'payout_test123',
            'status' => 'paid',
        ]);

        $response->assertOk();
        $this->assertEquals(PaymentStatus::CheckoutPaid, $payment->fresh()->status);
    }

    public function test_payoneer_webhook_rejects_failed_payment(): void
    {
        $payment = Payment::factory()->create([
            'method' => 'payoneer',
            'transaction_id' => 'payout_test456',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/payoneer', [
            'payout_id' => 'payout_test456',
            'status' => 'failed',
        ]);

        $response->assertOk();
        $this->assertEquals(PaymentStatus::CheckoutFailed, $payment->fresh()->status);
    }

    public function test_payoneer_webhook_invalid_payload(): void
    {
        $response = $this->postJson('/payment/webhook/payoneer', []);

        $response->assertStatus(400);
    }

    public function test_payoneer_webhook_payment_not_found(): void
    {
        $response = $this->postJson('/payment/webhook/payoneer', [
            'payout_id' => 'nonexistent',
            'status' => 'paid',
        ]);

        $response->assertStatus(404);
    }

    public function test_webhook_activates_subscription_on_approval(): void
    {
        $subscription = Subscription::factory()->create([
            'workspace_id' => $this->workspace->id,
            'status' => \App\Enums\SubscriptionStatus::PastDue,
        ]);

        $payment = Payment::factory()->create([
            'method' => 'stripe',
            'transaction_id' => 'pi_sub123',
            'status' => PaymentStatus::CheckoutPending,
            'subscription_id' => $subscription->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->postJson('/payment/webhook/stripe', [
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_sub123']],
        ]);

        $response->assertOk();
        $this->assertEquals(PaymentStatus::CheckoutPaid, $payment->fresh()->status);
        $this->assertEquals(\App\Enums\SubscriptionStatus::Active, $subscription->fresh()->status);
    }
}
