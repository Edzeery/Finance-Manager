<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use App\Services\SubscriptionActivationService;
use App\Services\SubscriptionProrationService;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Database\Seeders\SubscriptionPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionUpgradeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Workspace $workspace;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            EnterpriseRolePermissionSeeder::class,
            SubscriptionPlanSeeder::class,
        ]);

        $this->workspace = Workspace::factory()->create();
        $this->user = User::factory()->create();
        $this->user->current_workspace_id = $this->workspace->id;
        $this->user->save();

        $adminRole = Role::where('slug', 'workspace_admin')->first();
        $this->user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $this->workspace->id]);
    }

    private function createActiveSubscription(string $planSlug, string $billingPeriod = 'yearly', int $endsDaysAhead = 365): Subscription
    {
        $plan = SubscriptionPlan::where('slug', $planSlug)->firstOrFail();

        return Subscription::withoutWorkspace()->create([
            'workspace_id' => $this->workspace->id,
            'user_id' => $this->user->id,
            'subscription_plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subDays(30),
            'ends_at' => now()->addDays($endsDaysAhead),
            'billing_period' => $billingPeriod,
            'auto_renew' => true,
            'plan_price_amount' => $billingPeriod === 'yearly' ? $plan->yearly_price : $plan->monthly_price,
        ]);
    }

    public function test_proration_calculates_correctly_for_upgrade(): void
    {
        $this->createActiveSubscription('personal', 'yearly', 300);

        $service = app(SubscriptionProrationService::class);
        $targetPlan = SubscriptionPlan::where('slug', 'business')->firstOrFail();

        $proration = $service->calculateProration($this->workspace, $targetPlan, 'yearly');

        $this->assertGreaterThan(0, $proration['remaining_days'], 'Should have remaining days');
        $this->assertGreaterThan(0, $proration['total_days'], 'Should have total days');
        $this->assertTrue($proration['is_upgrade'], 'Should be an upgrade');
        $this->assertFalse($proration['is_downgrade'], 'Should not be a downgrade');
        $this->assertGreaterThan(0, $proration['amount_due'], 'Amount due should be positive for upgrade');
        $this->assertGreaterThan(0, $proration['remaining_value'], 'Remaining value should be positive');
        $this->assertGreaterThan(0, $proration['cost_at_new_rate'], 'Cost at new rate should be positive');
        $this->assertArrayHasKey('cost_at_new_rate', $proration);
    }

    public function test_proration_returns_zero_for_no_subscription(): void
    {
        $service = app(SubscriptionProrationService::class);
        $targetPlan = SubscriptionPlan::where('slug', 'personal')->firstOrFail();

        $proration = $service->calculateProration($this->workspace, $targetPlan, 'monthly');

        $this->assertEquals(0, $proration['remaining_days']);
        $this->assertEquals(0, $proration['amount_due']);
        $this->assertEquals(0, $proration['cost_at_new_rate']);
    }

    public function test_proration_handles_downgrade(): void
    {
        $this->createActiveSubscription('business', 'yearly', 300);

        $service = app(SubscriptionProrationService::class);
        $targetPlan = SubscriptionPlan::where('slug', 'personal')->firstOrFail();

        $proration = $service->calculateProration($this->workspace, $targetPlan, 'yearly');

        $this->assertTrue($proration['is_downgrade'], 'Should be a downgrade');
        $this->assertFalse($proration['is_upgrade'], 'Should not be an upgrade');
        $this->assertLessThan(0, $proration['amount_due'], 'Amount due should be negative for downgrade');
    }

    public function test_change_plan_creates_past_due_subscription_for_upgrade(): void
    {
        $this->createActiveSubscription('personal', 'yearly', 300);
        $this->actingAs($this->user);

        $businessPlan = SubscriptionPlan::where('slug', 'business')->firstOrFail();
        $cashMethod = PaymentMethod::where('key', 'cash')->firstOrFail();

        $response = $this->post(route('billing.subscriptions.change-plan'), [
            'plan_slug' => $businessPlan->slug,
            'billing' => 'yearly',
            'payment_method' => $cashMethod->key,
        ]);

        $response->assertRedirect();

        $newSub = Subscription::withoutWorkspace()
            ->where('workspace_id', $this->workspace->id)
            ->where('subscription_plan_id', $businessPlan->id)
            ->first();

        $this->assertNotNull($newSub, 'New subscription should be created');
        $this->assertEquals(SubscriptionStatus::PastDue, $newSub->status);
    }

    public function test_subscription_model_days_remaining(): void
    {
        $sub = $this->createActiveSubscription('personal', 'yearly', 150);

        $days = $sub->daysRemaining();
        $this->assertGreaterThanOrEqual(149, $days);
        $this->assertLessThanOrEqual(151, $days);
    }

    public function test_subscription_model_days_remaining_null_ends_at(): void
    {
        $plan = SubscriptionPlan::where('slug', 'personal')->firstOrFail();
        $sub = Subscription::withoutWorkspace()->create([
            'workspace_id' => $this->workspace->id,
            'user_id' => $this->user->id,
            'subscription_plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now(),
            'ends_at' => null,
            'billing_period' => 'yearly',
            'auto_renew' => true,
            'plan_price_amount' => $plan->yearly_price,
        ]);

        $this->assertEquals(365, $sub->daysRemaining());
    }

    public function test_invoice_contains_proration_credit_field(): void
    {
        $sub = $this->createActiveSubscription('personal', 'yearly', 300);
        $plan = SubscriptionPlan::where('slug', 'business')->firstOrFail();

        $service = app(SubscriptionActivationService::class);
        $invoice = $service->generateInvoice($sub, $this->workspace, $plan, null, 'yearly', 0, 0, 0, prorationCredit: 50.0);

        $this->assertNotNull($invoice);
        $this->assertEquals(50.0, (float) $invoice->proration_credit);
    }
}
