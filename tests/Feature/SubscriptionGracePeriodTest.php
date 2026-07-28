<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use App\Services\SubscriptionService;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionGracePeriodTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Workspace $workspace;

    private Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EnterpriseRolePermissionSeeder::class);

        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory()->create();
        $this->workspace->users()->attach($this->user->id, []);
        $adminRole = Role::where('slug', 'workspace_admin')->first();
        if ($adminRole) {
            $this->user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $this->workspace->id]);
        }
        $this->user->current_workspace_id = $this->workspace->id;
        $this->user->save();

        $plan = SubscriptionPlan::factory()->create();
        $this->subscription = Subscription::factory()->create([
            'workspace_id' => $this->workspace->id,
            'subscription_plan_id' => $plan->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonth(),
        ]);
    }

    public function test_manual_cancellation_sets_grace_ends_at(): void
    {
        $service = app(SubscriptionService::class);
        $service->cancelSubscription($this->subscription);

        $this->subscription->refresh();

        $this->assertEquals(SubscriptionStatus::Canceled, $this->subscription->status);
        $this->assertNotNull($this->subscription->grace_ends_at);
        $this->assertTrue($this->subscription->grace_ends_at->isFuture());
        $this->assertTrue($this->subscription->isOnGrace());
        $this->assertGreaterThan(0, $this->subscription->graceDaysRemaining());
    }

    public function test_grace_period_allows_access_with_warning(): void
    {
        $this->subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'auto_renew' => false,
            'ends_at' => now(),
            'grace_ends_at' => now()->addDays(3),
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertSessionHas('subscription_warning');
    }

    public function test_expired_grace_period_blocks_access_except_allowed_routes(): void
    {
        $this->subscription->update([
            'status' => 'canceled',
            'canceled_at' => now()->subDays(4),
            'auto_renew' => false,
            'ends_at' => now()->subDays(4),
            'grace_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('billing.subscriptions'));

        $response = $this->get(route('billing.subscriptions'));
        $response->assertOk();

        $response = $this->post(route('logout'));
        $response->assertRedirect('/');
    }

    public function test_web_middleware_returns_402_for_expired_subscription_via_json(): void
    {
        $this->subscription->update([
            'status' => 'canceled',
            'canceled_at' => now()->subDays(4),
            'ends_at' => now()->subDays(4),
            'grace_ends_at' => now()->subDay(),
            'auto_renew' => false,
        ]);

        $this->actingAs($this->user);

        $response = $this->getJson(route('dashboard'));
        $response->assertStatus(402);
        $response->assertJson(['error' => 'subscription_required']);
    }

    public function test_scheduled_command_expires_subscriptions(): void
    {
        Subscription::where('id', $this->subscription->id)->update([
            'ends_at' => now()->subDay(),
            'status' => 'active',
            'grace_ends_at' => null,
        ]);

        $this->artisan('subscriptions:expire')->assertSuccessful();

        $this->subscription->refresh();
        $this->assertEquals(SubscriptionStatus::Expired, $this->subscription->status);
        $this->assertNotNull($this->subscription->grace_ends_at);
        $this->assertTrue($this->subscription->isOnGrace());
    }

    public function test_api_returns_402_for_expired_subscription(): void
    {
        $this->subscription->delete();

        $plan = SubscriptionPlan::factory()->create();
        $subscription = Subscription::factory()->create([
            'workspace_id' => $this->workspace->id,
            'subscription_plan_id' => $plan->id,
            'user_id' => $this->user->id,
            'status' => 'canceled',
            'ends_at' => now()->subDays(5),
            'grace_ends_at' => now()->subDays(2),
            'auto_renew' => false,
        ]);

        $token = $this->user->createToken('test', ['*'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/workspace/incomes');

        $response->assertStatus(402);
        $response->assertJson(['error' => 'subscription_required']);
    }

    public function test_api_allows_active_subscription(): void
    {
        $token = $this->user->createToken('test', ['*'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/workspace/incomes');

        $response->assertOk();
    }

    public function test_api_allows_grace_subscription(): void
    {
        $this->subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'ends_at' => now(),
            'grace_ends_at' => now()->addDays(3),
            'auto_renew' => false,
        ]);

        $token = $this->user->createToken('test', ['*'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/workspace/incomes');

        $response->assertOk();
    }

    public function test_is_active_returns_true_for_trialing(): void
    {
        $this->subscription->update([
            'status' => 'trialing',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $this->assertTrue($this->subscription->isActive());
        $this->assertFalse($this->subscription->isExpired());
        $this->assertFalse($this->subscription->isTrialExpired());
    }

    public function test_is_active_returns_false_for_expired_trial(): void
    {
        $this->subscription->update([
            'status' => 'trialing',
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->assertFalse($this->subscription->isActive());
        $this->assertTrue($this->subscription->isExpired());
        $this->assertTrue($this->subscription->isTrialExpired());
    }

    public function test_middleware_blocks_expired_trial(): void
    {
        $this->subscription->update([
            'status' => 'trialing',
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('billing.subscriptions'));
    }

    public function test_trialing_subscription_allows_access(): void
    {
        $this->subscription->update([
            'status' => 'trialing',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }
}
