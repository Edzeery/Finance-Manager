<?php

namespace Tests\Feature\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\Helpers\ChargilyWebhookPayload;
use Tests\TestCase;

class ChargilyPaymentMethodTest extends TestCase
{
    use ChargilyWebhookPayload;
    use RefreshDatabase;

    private const CHARGILY_SECRET = 'test_chargily_secret_key';

    private Workspace $workspace;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('payment.gateways.chargily.secret_key', self::CHARGILY_SECRET);

        $this->workspace = Workspace::factory()->create();
        $this->user = User::factory()->create();
        $this->workspace->users()->attach($this->user->id, []);
    }

    private function signedRequest(string $rawBody): array
    {
        return ['signature' => hash_hmac('sha256', $rawBody, self::CHARGILY_SECRET)];
    }

    public function test_webhook_stores_edahabia_payment_method_type(): void
    {
        $this->markTestSkipped('Chargily webhook requires real HTTP (ngrok) — library reads php://input which is empty in CLI');

        $payment = Payment::factory()->forMethod('chargily')->create([
            'transaction_id' => 'ch_edahabia_1',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
            'payment_method_type' => null,
        ]);

        $rawBody = $this->chargilyPayload([
            'data' => [
                'id' => 'ch_edahabia_1',
                'payment_method' => 'edahabia',
                'metadata' => ['payment_id' => (string) $payment->id],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [], [],
            [],
            $this->transformHeadersToServerVars($this->signedRequest($rawBody)),
            $rawBody,
        );

        $response->assertOk();
        $payment->refresh();

        $this->assertEquals(PaymentStatus::CheckoutPaid, $payment->status);
        $this->assertEquals('edahabia', $payment->payment_method_type);
    }

    public function test_webhook_stores_cib_payment_method_type(): void
    {
        $this->markTestSkipped('Chargily webhook requires real HTTP (ngrok) — library reads php://input which is empty in CLI');

        $payment = Payment::factory()->forMethod('chargily')->create([
            'transaction_id' => 'ch_cib_1',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
            'payment_method_type' => null,
        ]);

        $rawBody = $this->chargilyPayload([
            'data' => [
                'id' => 'ch_cib_1',
                'payment_method' => 'CIB',
                'metadata' => ['payment_id' => (string) $payment->id],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [], [],
            [],
            $this->transformHeadersToServerVars($this->signedRequest($rawBody)),
            $rawBody,
        );

        $response->assertOk();
        $payment->refresh();

        $this->assertEquals(PaymentStatus::CheckoutPaid, $payment->status);
        $this->assertEquals('cib', $payment->payment_method_type);
    }

    public function test_webhook_does_not_set_payment_method_type_when_missing(): void
    {
        $this->markTestSkipped('Chargily webhook requires real HTTP (ngrok) — library reads php://input which is empty in CLI');

        $payment = Payment::factory()->forMethod('chargily')->create([
            'transaction_id' => 'ch_no_method',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
            'payment_method_type' => null,
        ]);

        $rawBody = $this->chargilyPayload([
            'data' => [
                'id' => 'ch_no_method',
                'payment_method' => null,
                'metadata' => ['payment_id' => (string) $payment->id],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [], [],
            [],
            $this->transformHeadersToServerVars($this->signedRequest($rawBody)),
            $rawBody,
        );

        $response->assertOk();
        $payment->refresh();

        $this->assertEquals(PaymentStatus::CheckoutPaid, $payment->status);
        $this->assertNull($payment->payment_method_type);
    }

    public function test_webhook_does_not_overwrite_existing_payment_method_type(): void
    {
        $this->markTestSkipped('Chargily webhook requires real HTTP (ngrok) — library reads php://input which is empty in CLI');

        $payment = Payment::factory()->forMethod('chargily')->create([
            'transaction_id' => 'ch_existing',
            'status' => PaymentStatus::CheckoutPaid,
            'workspace_id' => $this->workspace->id,
            'payment_method_type' => 'edahabia',
        ]);

        $rawBody = $this->chargilyPayload([
            'data' => [
                'id' => 'ch_existing',
                'payment_method' => 'cib',
                'metadata' => ['payment_id' => (string) $payment->id],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [], [],
            [],
            $this->transformHeadersToServerVars($this->signedRequest($rawBody)),
            $rawBody,
        );

        // Already completed — webhook should no-op
        $response->assertOk();
        $payment->refresh();

        $this->assertEquals('edahabia', $payment->payment_method_type);
    }

    public function test_failed_webhook_does_not_set_payment_method_type(): void
    {
        $this->markTestSkipped('Chargily webhook requires real HTTP (ngrok) — library reads php://input which is empty in CLI');

        $payment = Payment::factory()->forMethod('chargily')->create([
            'transaction_id' => 'ch_fail_1',
            'status' => PaymentStatus::CheckoutPending,
            'workspace_id' => $this->workspace->id,
            'payment_method_type' => null,
        ]);

        $rawBody = $this->chargilyPayload([
            'type' => 'checkout.failed',
            'data' => [
                'id' => 'ch_fail_1',
                'status' => 'failed',
                'payment_method' => 'edahabia',
                'metadata' => ['payment_id' => (string) $payment->id],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/payment/webhook/chargily',
            [], [],
            [],
            $this->transformHeadersToServerVars($this->signedRequest($rawBody)),
            $rawBody,
        );

        $response->assertOk();
        $payment->refresh();

        $this->assertEquals(PaymentStatus::CheckoutFailed, $payment->status);
        $this->assertNull($payment->payment_method_type);
    }
}
