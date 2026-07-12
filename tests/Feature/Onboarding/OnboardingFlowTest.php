<?php

namespace Tests\Feature\Onboarding;

use App\Models\Permission;
use App\Models\PlanPrice;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\OnboardingService;
use App\Services\WorkspaceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Volt;
use Tests\TestCase;

class OnboardingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['slug' => 'workspace_admin', 'name' => 'Admin', 'level' => 'workspace', 'is_system' => true, 'sort_order' => 1]);
        Role::create(['slug' => 'workspace_deputy_admin', 'name' => 'Deputy Admin', 'level' => 'workspace', 'is_system' => true, 'sort_order' => 2]);

        $dashboardPerm = Permission::create(['slug' => 'dashboard.view', 'name' => 'View Dashboard', 'module' => 'dashboard']);
        $adminRole->permissions()->attach($dashboardPerm->id);

        SubscriptionPlan::create([
            'slug' => 'personal',
            'name' => 'Personal',
            'description' => 'Free personal plan',
            'is_free' => true,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 1,
        ]);

        $premium = SubscriptionPlan::create([
            'slug' => 'premium',
            'name' => 'Premium',
            'description' => 'Premium plan',
            'is_free' => false,
            'yearly_discount_percent' => 17,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 2,
        ]);

        PlanPrice::create([
            'plan_id' => $premium->id,
            'billing_period' => 'monthly',
            'currency' => 'USD',
            'price' => 19.99,
            'is_active' => true,
        ]);
    }

    public function test_new_user_is_redirected_to_onboarding_after_login(): void
    {
        $user = User::factory()->unonboarded()->create();
        app(WorkspaceService::class)->createForUser($user);
        $this->actingAs($user);

        $this->get(route('dashboard'))->assertRedirect(route('onboarding.plan'));
    }

    public function test_onboarded_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $workspace = app(WorkspaceService::class)->createForUser($user);
        $plan = SubscriptionPlan::first();
        if ($plan) {
            \App\Models\Subscription::withoutWorkspace()->create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'billing_period' => 'monthly',
                'auto_renew' => true,
            ]);
        }
        $this->actingAs($user);

        $this->get(route('dashboard'))->assertOk();
    }

    public function test_onboarding_flow_creates_subscription_on_free_plan_selection(): void
    {
        $user = User::factory()->unonboarded()->create();
        app(WorkspaceService::class)->createForUser($user);
        $this->actingAs($user);

        $freePlan = SubscriptionPlan::where('is_free', true)->first();
        $this->assertNotNull($freePlan);

        $user->update(['pending_plan_id' => $freePlan->id]);
        $service = app(OnboardingService::class);
        $result = $service->processFreePlan($user);

        $this->assertTrue($result);
        $user->refresh();
        $this->assertNotNull($user->currentWorkspace);
        $subscription = $user->currentWorkspace->allSubscriptions()->first();
        $this->assertNotNull($subscription);
        $this->assertEquals('personal', $subscription->plan->slug);
    }

    public function test_subscription_not_created_before_plan_selection(): void
    {
        $user = User::factory()->unonboarded()->create();
        app(WorkspaceService::class)->createForUser($user);
        $this->actingAs($user);

        $this->assertNull($user->currentWorkspace->allSubscriptions()->first());
    }

    public function test_onboarding_plan_page_shows_available_plans(): void
    {
        $user = User::factory()->unonboarded()->create();
        app(WorkspaceService::class)->createForUser($user);
        $this->actingAs($user);

        $response = $this->get(route('onboarding.plan'));
        $response->assertOk();

        $response->assertSee('Personal');
        $response->assertSee('Premium');
    }

    public function test_user_cannot_access_onboarding_after_completion(): void
    {
        $user = User::factory()->create();
        $workspace = app(WorkspaceService::class)->createForUser($user);
        $plan = SubscriptionPlan::where('is_free', true)->first();
        if ($plan) {
            \App\Models\Subscription::withoutWorkspace()->create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'billing_period' => 'monthly',
            ]);
        }
        \App\Models\User::withoutTimestamps(function () use ($user) {
            $user->update(['onboarding_completed_at' => now()]);
        });
        $this->actingAs($user);

        $this->get(route('onboarding.plan'))->assertRedirect(route('dashboard'));
    }

    public function test_user_cannot_access_onboarding_after_payment(): void
    {
        $user = User::factory()->create();
        $workspace = app(WorkspaceService::class)->createForUser($user);
        $plan = SubscriptionPlan::first();
        if ($plan) {
            \App\Models\Subscription::withoutWorkspace()->create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
            ]);
        }
        $this->actingAs($user);

        $this->get(route('onboarding.plan'))->assertRedirect(route('dashboard'));
    }

    public function test_onboarding_flow_completes_successfully(): void
    {
        $user = User::factory()->unonboarded()->create();
        app(WorkspaceService::class)->createForUser($user);
        $this->actingAs($user);

        $freePlan = SubscriptionPlan::where('is_free', true)->first();
        $this->assertNotNull($freePlan);

        $user->update(['pending_plan_id' => $freePlan->id]);
        $service = app(OnboardingService::class);
        $service->processFreePlan($user);

        $user->refresh();
        $this->assertTrue($user->hasConfirmedPlan());
        $this->assertNotNull($user->currentWorkspace->allSubscriptions()->first());
    }
}
