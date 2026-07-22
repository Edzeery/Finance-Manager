<?php

namespace Tests\Feature\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PlanPrice;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use App\Services\OnboardingService;
use App\Services\Payments\Noest\NoestGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NoestPaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspace;

    private User $user;

    private SubscriptionPlan $paidPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::factory()->create();
        $this->user = User::factory()->create();
        $this->workspace->users()->attach($this->user->id, []);
        $this->user->update(['current_workspace_id' => $this->workspace->id]);

        $this->paidPlan = SubscriptionPlan::factory()->create([
            'yearly_discount_percent' => 17,
            'is_free' => false,
        ]);

        PlanPrice::create([
            'plan_id' => $this->paidPlan->id,
            'billing_period' => 'monthly',
            'currency' => 'USD',
            'price' => 20,
            'is_active' => true,
        ]);
    }

    public function test_gateway_name(): void
    {
        $gateway = app(NoestGateway::class);
        $this->assertEquals('noest', $gateway->name());
    }

    public function test_gateway_is_offline(): void
    {
        $gateway = app(NoestGateway::class);
        $this->assertFalse($gateway->isOnline());
        $this->assertTrue($gateway->isOffline());
    }

    public function test_charge_creates_noest_order_and_returns_tracking(): void
    {
        Http::fake([
            '*/create/order*' => Http::response([
                'success' => true,
                'data' => ['tracking' => 'NST-TEST-001'],
            ]),
        ]);

        $gateway = app(NoestGateway::class);

        $payment = Payment::factory()->forMethod('noest')->create([
            'workspace_id' => $this->workspace->id,
            'user_id' => $this->user->id,
            'reference' => 'NST-ABC123',
            'amount' => 5000,
            'currency' => 'DZD',
            'status' => PaymentStatus::CheckoutPending,
        ]);

        $result = $gateway->charge([
            'amount' => 5000,
            'payment_id' => $payment->id,
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'noest_phone' => '0550505050',
            'noest_adresse' => 'Test address',
        ]);

        $this->assertTrue($result->success);
        $this->assertTrue($result->isPending());
        $this->assertEquals('NST-TEST-001', $result->transactionId);
        $this->assertEquals('NST-ABC123', $result->reference);
        $this->assertArrayHasKey('awaiting_delivery', $result->metadata);
        $this->assertTrue($result->metadata['awaiting_delivery']);
    }

    public function test_charge_fails_when_payment_not_found(): void
    {
        $gateway = app(NoestGateway::class);

        $result = $gateway->charge([
            'amount' => 5000,
            'payment_id' => 999999,
        ]);

        $this->assertFalse($result->success);
    }

    public function test_charge_fails_when_api_returns_error(): void
    {
        Http::fake([
            '*/create/order*' => Http::response([
                'success' => false,
                'message' => 'Invalid data',
            ], 422),
        ]);

        $gateway = app(NoestGateway::class);

        $payment = Payment::factory()->forMethod('noest')->create([
            'workspace_id' => $this->workspace->id,
            'user_id' => $this->user->id,
            'status' => PaymentStatus::CheckoutPending,
        ]);

        $result = $gateway->charge([
            'amount' => 5000,
            'payment_id' => $payment->id,
        ]);

        $this->assertFalse($result->success);
    }

    public function test_refund_is_not_supported(): void
    {
        $gateway = app(NoestGateway::class);

        $payment = Payment::factory()->create();
        $result = $gateway->refund($payment);

        $this->assertFalse($result->success);
    }

    public function test_verify_retrieves_tracking_info(): void
    {
        Http::fake([
            '*/get/trackings/info' => Http::response([
                'success' => true,
                'data' => ['tracking' => 'NST-001', 'status' => 'delivered'],
            ]),
        ]);

        $gateway = app(NoestGateway::class);

        $payment = Payment::factory()->create([
            'transaction_id' => 'NST-001',
        ]);

        $result = $gateway->verify($payment);

        $this->assertTrue($result->success);
    }

    public function test_verify_fails_without_transaction_id(): void
    {
        $gateway = app(NoestGateway::class);

        $payment = Payment::factory()->create(['transaction_id' => null]);
        $result = $gateway->verify($payment);

        $this->assertFalse($result->success);
    }

    public function test_supports_only_dzd(): void
    {
        $gateway = app(NoestGateway::class);
        $this->assertEquals(['DZD'], $gateway->supportedCurrencies());
    }

    public function test_onboarding_initiate_with_noest_creates_order_and_returns_pending(): void
    {
        Http::fake([
            '*/create/order*' => Http::response([
                'success' => true,
                'data' => ['tracking' => 'NST-FLOW-001'],
            ]),
        ]);

        $this->user->update(['pending_plan_id' => $this->paidPlan->id]);

        $payment = app(OnboardingService::class)->initiatePaidPlanPayment(
            $this->user,
            paymentMethod: 'noest',
            billingPeriod: 'monthly',
            gatewayData: ['noest_wilaya' => '16'],
        );

        $this->assertNotNull($payment);
        $payment->refresh();

        $this->assertEquals('noest', $payment->paymentMethod?->key);
        $this->assertEquals(PaymentStatus::CheckoutPending, $payment->status);
        $this->assertNotNull($payment->transaction_id);
        $this->assertEquals('NST-FLOW-001', $payment->transaction_id);
        $this->assertArrayHasKey('awaiting_delivery', $payment->metadata['gateway_response'] ?? []);
    }

    public function test_onboarding_initiate_with_noest_handles_api_failure(): void
    {
        Http::fake([
            '*/create/order*' => Http::response([
                'success' => false,
            ], 422),
        ]);

        $this->user->update(['pending_plan_id' => $this->paidPlan->id]);

        $this->expectException(\RuntimeException::class);

        app(OnboardingService::class)->initiatePaidPlanPayment(
            $this->user,
            paymentMethod: 'noest',
            billingPeriod: 'monthly',
            gatewayData: ['noest_wilaya' => '16'],
        );
    }
}
