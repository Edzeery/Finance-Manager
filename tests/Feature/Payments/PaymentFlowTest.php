<?php

namespace Tests\Feature\Payments;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Exceptions\PaymentException;
use App\Models\PlanFeature;
use App\Models\PlanPrice;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use App\Services\OnboardingService;
use App\Services\Payments\Chargily\ChargilyClient;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use App\Services\WorkspaceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionPlan $paidPlan;

    private SubscriptionPlan $freePlan;

    protected function tearDown(): void
    {
        ChargilyClient::forgetInstance();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        ChargilyClient::forgetInstance();
        Cache::forget('payment_gateway_config.chargily');

        Role::create(['slug' => 'workspace_admin', 'name' => 'Admin', 'level' => 'workspace', 'is_system' => true, 'sort_order' => 1]);

        $this->freePlan = SubscriptionPlan::create([
            'slug' => 'personal', 'name_en' => 'Personal', 'is_free' => true,
            'is_active' => true, 'is_public' => true, 'sort_order' => 1,
        ]);
        $workspaceFeature = PlanFeature::firstOrCreate(
            ['slug' => 'workspaces'],
            ['name_en' => 'Workspaces', 'name_ar' => 'مساحات العمل', 'name_fr' => 'Espaces de travail', 'type' => 'number', 'is_core' => false]
        );
        $this->freePlan->planFeatures()->attach($workspaceFeature->id, ['value' => '1', 'sort_order' => 0]);

        $this->paidPlan = SubscriptionPlan::create([
            'slug' => 'premium', 'name_en' => 'Premium', 'is_free' => false,
            'yearly_discount_percent' => 17,
            'is_active' => true, 'is_public' => true, 'sort_order' => 2,
        ]);

        PlanPrice::create([
            'plan_id' => $this->paidPlan->id,
            'billing_period' => 'monthly',
            'currency' => 'USD',
            'price' => 19.99,
            'is_active' => true,
        ]);

        PlanPrice::create([
            'plan_id' => $this->paidPlan->id,
            'billing_period' => 'yearly',
            'currency' => 'USD',
            'price' => 199.00,
            'is_active' => true,
        ]);
    }

    public function test_payment_service_charge_for_plan_throws_on_null_workspace(): void
    {
        $service = app(PaymentService::class);
        $user = User::factory()->create();

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Workspace is required to process payment.');

        $service->chargeForPlan(
            workspace: null,
            plan: $this->paidPlan,
            billingPeriod: 'monthly',
            couponCode: null,
            paymentMethod: 'chargily',
            userId: $user->id,
        );
    }

    public function test_payment_service_charge_for_plan_throws_on_workspace_no_id(): void
    {
        $service = app(PaymentService::class);
        $user = User::factory()->create();
        $workspace = new Workspace;

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Workspace is required to process payment.');

        $service->chargeForPlan(
            workspace: $workspace,
            plan: $this->paidPlan,
            billingPeriod: 'monthly',
            couponCode: null,
            paymentMethod: 'chargily',
            userId: $user->id,
        );
    }

    public function test_payment_service_creates_payment_with_valid_workspace(): void
    {
        $service = app(PaymentService::class);
        $workspace = Workspace::factory()->create();
        $user = User::factory()->create();
        $workspace->users()->attach($user->id, []);

        $payment = $service->chargeForPlan(
            workspace: $workspace,
            plan: $this->paidPlan,
            billingPeriod: 'monthly',
            couponCode: null,
            paymentMethod: 'chargily',
            userId: $user->id,
        );

        $this->assertNotNull($payment);
        $this->assertEquals($workspace->id, $payment->workspace_id);
        $this->assertEquals('chargily', $payment->method);
        $this->assertEquals(4997.5, (float) $payment->amount);
        $this->assertEquals('DZD', $payment->currency);
        $this->assertEquals(PaymentStatus::CheckoutPending, $payment->status);
    }

    public function test_onboarding_service_creates_workspace_when_user_has_none(): void
    {
        $service = app(OnboardingService::class);
        $user = User::factory()->unonboarded()->create();

        $user->update(['pending_plan_id' => $this->freePlan->id]);
        $result = $service->processFreePlan($user);

        $this->assertTrue($result);
        $user->refresh();
        $this->assertNotNull($user->currentWorkspace);
        $this->assertTrue($user->hasConfirmedPlan());

        // Fetch latest subscription directly by ID ordering
        $latestSub = Subscription::where('workspace_id', $user->currentWorkspace->id)
            ->orderBy('id', 'desc')
            ->first();
        $this->assertNotNull($latestSub);
        $this->assertEquals(SubscriptionStatus::Active, $latestSub->status);
        $this->assertEquals('free', $latestSub->payment_method);
    }

    public function test_onboarding_ensure_workspace_uses_existing_workspace(): void
    {
        $service = app(OnboardingService::class);
        $user = User::factory()->unonboarded()->create();

        // Pre-create a workspace for the user
        $workspace = app(WorkspaceService::class)->createForUser($user);
        $user->refresh();

        $user->update(['pending_plan_id' => $this->freePlan->id]);
        $result = $service->processFreePlan($user);

        $this->assertTrue($result);
        $user->refresh();
        $this->assertEquals($workspace->id, $user->currentWorkspace->id);

        // Should still have an active subscription (order by id desc to get newest)
        $latestSub = Subscription::where('workspace_id', $workspace->id)
            ->orderBy('id', 'desc')
            ->first();
        $this->assertNotNull($latestSub);
        $this->assertEquals(SubscriptionStatus::Active, $latestSub->status);
    }

    public function test_can_create_workspace_allows_first_workspace_for_new_user(): void
    {
        $service = app(SubscriptionService::class);
        $user = User::factory()->unonboarded()->create();

        $this->assertTrue($service->canCreateWorkspace($user));
    }

    public function test_can_create_workspace_denies_when_limit_reached(): void
    {
        $service = app(SubscriptionService::class);

        $workspace = Workspace::factory()->create();
        $user = User::factory()->create();
        $workspace->users()->attach($user->id, []);

        $workspace->allSubscriptions()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $this->freePlan->id,
            'status' => 'active',
            'starts_at' => now(),
            'payment_method' => 'free',
        ]);

        $user->current_workspace_id = $workspace->id;
        $user->save();
        $user->load('currentWorkspace');

        $this->assertFalse($service->canCreateWorkspace($user));
    }

    public function test_onboarding_initiate_paid_plan_payment_creates_workspace(): void
    {
        $service = app(OnboardingService::class);
        $user = User::factory()->unonboarded()->create();
        $user->update(['pending_plan_id' => $this->paidPlan->id]);

        $payment = $service->initiatePaidPlanPayment(
            $user,
            paymentMethod: 'chargily',
            billingPeriod: 'monthly',
        );

        $this->assertNotNull($payment);
        $user->refresh();
        $this->assertNotNull($user->currentWorkspace);
        $this->assertEquals($user->currentWorkspace->id, $payment->workspace_id);
    }

    public function test_free_plan_processing_creates_trial_subscription(): void
    {
        $service = app(OnboardingService::class);
        $user = User::factory()->unonboarded()->create();
        $user->update(['pending_plan_id' => $this->freePlan->id]);

        $result = $service->processFreePlan($user);

        $this->assertTrue($result);
        $user->refresh();

        // Query directly by ID desc to avoid created_at collision with latest()
        $subscription = Subscription::where('workspace_id', $user->currentWorkspace->id)
            ->orderBy('id', 'desc')
            ->first();
        $this->assertNotNull($subscription);
        $this->assertEquals(SubscriptionStatus::Active, $subscription->status);
        $this->assertEquals('free', $subscription->payment_method);
        $this->assertTrue($user->hasConfirmedPlan());
    }

    public function test_onboarding_returns_null_for_invalid_plan(): void
    {
        $service = app(OnboardingService::class);
        $user = User::factory()->create();

        // No pending plan set
        $payment = $service->initiatePaidPlanPayment($user, paymentMethod: 'chargily');
        $this->assertNull($payment);

        // Set pending_plan_id to the free plan — initiatePaidPlanPayment returns null for free plans
        $user->update(['pending_plan_id' => $this->freePlan->id]);

        $payment = $service->initiatePaidPlanPayment($user, paymentMethod: 'chargily');
        $this->assertNull($payment);
    }
}
