<?php

namespace Tests\Feature\Api;

use App\Enums\SubscriptionStatus;
use App\Models\Coupon;
use App\Models\PlanPrice;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Workspace $workspace;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['slug' => 'workspace_admin', 'name' => 'Admin', 'level' => 'workspace', 'is_system' => true, 'sort_order' => 1]);

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;

        $this->workspace = Workspace::factory()->create();
        $this->workspace->users()->attach($this->user->id, []);
        $this->user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $this->workspace->id]);
        $this->user->current_workspace_id = $this->workspace->id;
        $this->user->save();
    }

    public function test_list_plans(): void
    {
        SubscriptionPlan::factory()->count(3)->create(['is_active' => true, 'is_public' => true]);

        $response = $this->withToken($this->token)
            ->getJson('/api/plans');

        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function test_list_plans_excludes_inactive(): void
    {
        SubscriptionPlan::factory()->create(['is_active' => true, 'is_public' => true]);
        SubscriptionPlan::factory()->create(['is_active' => false, 'is_public' => true]);

        $response = $this->withToken($this->token)
            ->getJson('/api/plans');

        $response->assertOk()
            ->assertJsonCount(1);
    }

    public function test_show_current_subscription(): void
    {
        $plan = SubscriptionPlan::factory()->create();
        Subscription::factory()->create([
            'workspace_id' => $this->workspace->id,
            'user_id' => $this->user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/subscription');

        $response->assertOk()
            ->assertJsonFragment(['status' => 'active']);
    }

    public function test_current_subscription_returns_404_when_none(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/subscription');

        $response->assertStatus(404);
    }

    public function test_change_plan(): void
    {
        $plan = SubscriptionPlan::factory()->create([
            'slug' => 'test-plan',
            'sort_order' => 1,
            'is_active' => true,
            'is_public' => true,
        ]);
        PlanPrice::create([
            'plan_id' => $plan->id,
            'billing_period' => 'monthly',
            'currency' => 'USD',
            'price' => 9.99,
            'is_active' => true,
        ]);

        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/subscription/change-plan', [
                'plan_slug' => $plan->slug,
                'billing' => 'monthly',
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'past_due');
    }

    public function test_change_plan_validates_required_fields(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/subscription/change-plan', []);

        $response->assertStatus(422);
    }

    public function test_cancel_subscription(): void
    {
        $plan = SubscriptionPlan::factory()->create();
        Subscription::factory()->create([
            'workspace_id' => $this->workspace->id,
            'user_id' => $this->user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);

        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/subscription/cancel');

        $response->assertOk();
        $this->assertEquals(SubscriptionStatus::Canceled, $this->workspace->owner()?->first()?->activeSubscription()->fresh()->status);
    }

    public function test_cancel_subscription_returns_404_when_none(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/subscription/cancel');

        $response->assertStatus(404);
    }

    public function test_validate_coupon_valid(): void
    {
        Coupon::create([
            'code' => 'TEST10',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);

        $response = $this->withToken($this->token)
            ->postJson('/api/coupon/validate', [
                'code' => 'TEST10',
                'amount' => 100,
            ]);

        $response->assertOk()
            ->assertJsonFragment(['valid' => true, 'code' => 'TEST10', 'discount' => 10.00]);
    }

    public function test_validate_coupon_invalid(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/coupon/validate', [
                'code' => 'INVALID',
            ]);

        $response->assertStatus(404);
    }
}
