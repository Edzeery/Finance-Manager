<?php

namespace Tests\Feature;

use App\Models\PlanFeature;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use App\Services\SubscriptionService;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Database\Seeders\SubscriptionPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPlanModelTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Workspace $workspace;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EnterpriseRolePermissionSeeder::class);

        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory()->create();
        $this->workspace->users()->attach($this->user->id, []);
        $adminRole = \App\Models\Role::where('slug', 'workspace_admin')->first();
        if ($adminRole) {
            $this->user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $this->workspace->id]);
        }
        $this->user->current_workspace_id = $this->workspace->id;
        $this->user->save();
    }

    public function test_seeder_creates_all_four_plans(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);

        $this->assertDatabaseCount('subscription_plans', 4);
        $this->assertDatabaseHas('subscription_plans', ['slug' => 'personal']);
        $this->assertDatabaseHas('subscription_plans', ['slug' => 'business']);
        $this->assertDatabaseHas('subscription_plans', ['slug' => 'professional']);
        $this->assertDatabaseHas('subscription_plans', ['slug' => 'enterprise']);
    }

    public function test_personal_plan_is_free(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $plan = SubscriptionPlan::where('slug', 'personal')->first();

        $this->assertTrue($plan->isFree());
        $this->assertTrue($plan->is_free);
        $this->assertEquals(0, $plan->monthly_price);
    }

    public function test_enterprise_plan_is_not_free_and_not_public(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $plan = SubscriptionPlan::where('slug', 'enterprise')->first();

        $this->assertFalse($plan->isFree());
        $this->assertFalse($plan->is_public);
        $this->assertNull($plan->max_users);
        $this->assertNull($plan->max_workspaces);
    }

    public function test_enterprise_not_in_available_plans(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $service = app(SubscriptionService::class);
        $available = $service->getAvailablePlans();

        $slugs = array_column($available, 'slug');
        $this->assertNotContains('enterprise', $slugs);
        $this->assertContains('personal', $slugs);
        $this->assertContains('business', $slugs);
        $this->assertContains('professional', $slugs);
    }

    public function test_features_loaded_for_all_plans(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $plans = SubscriptionPlan::all();

        foreach ($plans as $plan) {
            $featureSlugs = $plan->featureSlugs();
            $this->assertContains('transactions_per_month', $featureSlugs);
            $this->assertContains('income_expense', $featureSlugs);
        }
    }

    public function test_all_public_plans_have_features(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $plans = SubscriptionPlan::where('is_public', true)->get();

        foreach ($plans as $plan) {
            $this->assertNotEmpty($plan->planFeatures);
        }
    }

    public function test_cannot_create_transaction_when_monthly_limit_exceeded(): void
    {
        $plan = SubscriptionPlan::factory()->create(['is_free' => false]);

        $feature = PlanFeature::firstOrCreate(
            ['slug' => 'transactions_per_month'],
            ['name_en' => 'Transactions Per Month', 'type' => 'value']
        );
        $plan->planFeatures()->syncWithoutDetaching([$feature->id => ['value' => '5', 'sort_order' => 0]]);

        $subscription = Subscription::factory()->create([
            'workspace_id' => $this->workspace->id,
            'subscription_plan_id' => $plan->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $service = app(SubscriptionService::class);

        $this->assertEquals(5, $service->maxTransactionsPerMonth($this->workspace));

        \App\Models\Income::withoutWorkspace()->getQuery()->delete();
        \App\Models\Expense::withoutWorkspace()->getQuery()->delete();

        \App\Models\Income::factory()->count(3)->create([
            'workspace_id' => $this->workspace->id,
            'user_id' => $this->user->id,
        ]);
        \App\Models\Expense::factory()->count(3)->create([
            'workspace_id' => $this->workspace->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertFalse($service->canCreateTransaction($this->workspace));
    }

    public function test_can_create_transaction_when_under_monthly_limit(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        $feature = PlanFeature::firstOrCreate(
            ['slug' => 'transactions_per_month'],
            ['name_en' => 'Transactions Per Month', 'type' => 'value']
        );
        $plan->planFeatures()->syncWithoutDetaching([$feature->id => ['value' => '100', 'sort_order' => 0]]);

        Subscription::factory()->create([
            'workspace_id' => $this->workspace->id,
            'subscription_plan_id' => $plan->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $service = app(SubscriptionService::class);

        $this->assertTrue($service->canCreateTransaction($this->workspace));
    }

    public function test_cannot_create_workspace_when_at_plan_limit(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        $feature = PlanFeature::firstOrCreate(
            ['slug' => 'workspaces'],
            ['name_en' => 'Workspaces', 'type' => 'value']
        );
        $plan->planFeatures()->syncWithoutDetaching([$feature->id => ['value' => '1', 'sort_order' => 0]]);

        Subscription::factory()->create([
            'workspace_id' => $this->workspace->id,
            'subscription_plan_id' => $plan->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $service = app(SubscriptionService::class);

        $this->assertFalse($service->canCreateWorkspace($this->user));
    }

    public function test_can_create_workspace_without_limit(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        $feature = PlanFeature::firstOrCreate(
            ['slug' => 'workspaces'],
            ['name_en' => 'Workspaces', 'type' => 'value']
        );
        $plan->planFeatures()->syncWithoutDetaching([$feature->id => ['value' => null, 'sort_order' => 0]]);

        Subscription::factory()->create([
            'workspace_id' => $this->workspace->id,
            'subscription_plan_id' => $plan->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $service = app(SubscriptionService::class);

        $this->assertTrue($service->canCreateWorkspace($this->user));
    }

    public function test_all_plans_ordered_by_sort_order(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $plans = SubscriptionPlan::active()->public()->orderBy('sort_order')->get();

        $expectedOrder = ['personal', 'business', 'professional'];
        foreach ($plans as $i => $plan) {
            $this->assertEquals($expectedOrder[$i], $plan->slug);
        }
    }

    public function test_free_plan_cannot_be_mistaken_for_enterprise(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);
        $personal = SubscriptionPlan::where('slug', 'personal')->first();
        $enterprise = SubscriptionPlan::where('slug', 'enterprise')->first();

        $this->assertTrue($personal->isFree());
        $this->assertFalse($enterprise->isFree());
    }
}
