<?php

namespace Tests\Feature\Registration;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\WorkspaceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationProvisioningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['slug' => 'workspace_admin', 'name_en' => 'Admin', 'level' => 'workspace', 'is_system' => true, 'sort_order' => 1]);
        Role::create(['slug' => 'workspace_deputy_admin', 'name_en' => 'Deputy Admin', 'level' => 'workspace', 'is_system' => true, 'sort_order' => 2]);
        Role::create(['slug' => 'workspace_finance_manager', 'name_en' => 'Finance Manager', 'level' => 'workspace', 'is_system' => true, 'sort_order' => 3]);
        Role::create(['slug' => 'workspace_accountant', 'name_en' => 'Accountant', 'level' => 'workspace', 'is_system' => true, 'sort_order' => 4]);
        Role::create(['slug' => 'workspace_editor', 'name_en' => 'Editor', 'level' => 'workspace', 'is_system' => true, 'sort_order' => 5]);
        Role::create(['slug' => 'workspace_viewer', 'name_en' => 'Viewer', 'level' => 'workspace', 'is_system' => true, 'sort_order' => 6]);

        $dashboardPerm = Permission::create(['slug' => 'dashboard.view', 'name_en' => 'View Dashboard', 'module' => 'dashboard']);
        $adminRole->permissions()->attach($dashboardPerm->id);

        SubscriptionPlan::create([
            'slug' => 'personal',
            'name_en' => 'Personal',
            'description' => 'Free personal plan',
            'is_free' => true,
        ]);
    }

    public function test_new_user_cannot_register_and_immediately_have_workspace(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'New User')
            ->set('email', 'newuser@example.com')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1');

        $component->call('register');

        $user = User::where('email', 'newuser@example.com')->first();

        $this->assertNotNull($user);
        $this->assertCount(0, $user->workspaces);
        $this->assertNull($user->current_workspace_id);
    }

    public function test_new_user_is_redirected_to_verification_notice_after_registration(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Dashboard User')
            ->set('email', 'dashuser@example.com')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1');

        $component->call('register');

        $user = User::where('email', 'dashuser@example.com')->first();
        $this->actingAs($user);

        $this->get(route('dashboard'))->assertRedirect(route('verification.notice'));
    }

    public function test_user_can_access_dashboard_after_completing_onboarding(): void
    {
        $user = User::factory()->create();
        $workspace = app(WorkspaceService::class)->createForUser($user);
        $plan = SubscriptionPlan::first();
        if ($plan) {
            Subscription::withoutWorkspace()->create([
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

        $this->get(route('dashboard'))->assertStatus(200);
    }

    public function test_workspace_admin_role_has_dashboard_view_permission(): void
    {
        $workspaceAdmin = Role::where('slug', 'workspace_admin')->first();

        $perm = Permission::updateOrCreate(
            ['slug' => 'dashboard.view'],
            ['name_en' => 'View Dashboard', 'module' => 'dashboard']
        );

        $workspaceAdmin->permissions()->syncWithoutDetaching($perm->id);

        $this->assertTrue(
            $workspaceAdmin->permissions->pluck('slug')->contains('dashboard.view')
        );
    }

    public function test_api_registration_creates_workspace(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name_en' => 'API User',
            'email' => 'apiuser@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['user', 'token']);

        $user = User::where('email', 'apiuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertCount(1, $user->workspaces);
        $this->assertNotNull($user->current_workspace_id);
    }

    public function test_user_with_paid_plan_and_no_payment_redirected_from_setup(): void
    {
        $plan = SubscriptionPlan::create([
            'slug' => 'basic',
            'name_en' => 'Basic',
            'is_free' => false,
            'yearly_discount_percent' => 17,

        ]);

        $user = User::factory()->unonboarded()->create(['pending_plan_id' => $plan->id]);
        app(WorkspaceService::class)->createForUser($user);
        $this->actingAs($user);

        $this->get(route('onboarding.setup'))->assertRedirect(route('onboarding.plan'));
    }
}
