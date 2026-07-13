<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPlanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EnterpriseRolePermissionSeeder::class);
        $this->admin = User::factory()->create(['two_factor_confirmed_at' => now(), 'locale' => 'en']);
        $this->admin->roles()->attach(Role::where('slug', 'super_admin')->first());
        $this->member = User::factory()->create();
    }

    public function test_index_requires_super_admin(): void
    {
        $this->actingAs($this->member)
            ->get(route('super.admin.plans.index'))
            ->assertForbidden();
    }

    public function test_index_displays_plans(): void
    {
        SubscriptionPlan::factory()->create(['name_en' => 'Gold']);

        $this->actingAs($this->admin)
            ->get(route('super.admin.plans.index'))
            ->assertOk()
            ->assertViewIs('super-admin.plans')
            ->assertViewHas('plans')
            ->assertSee('Gold');
    }

    public function test_create_requires_super_admin(): void
    {
        $this->actingAs($this->member)
            ->get(route('super.admin.plans.create'))
            ->assertForbidden();
    }

    public function test_create_displays_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('super.admin.plans.create'))
            ->assertOk()
            ->assertViewIs('super-admin.plans-form');
    }

    public function test_store_creates_plan(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('super.admin.plans.store'), [
            'name_en' => 'Silver Plan',
            'slug' => 'silver-plan',
            'description_en' => 'A silver plan',
            'yearly_discount_percent' => 17,
            'is_active' => true,
            'is_public' => true,
        ]);

        $response->assertRedirect(route('super.admin.plans.index'));
        $this->assertDatabaseHas('subscription_plans', ['slug' => 'silver-plan']);
    }

    public function test_store_requires_super_admin(): void
    {
        $this->actingAs($this->member)
            ->post(route('super.admin.plans.store'), [
                'name_en' => 'Test',
                'slug' => 'test',
            ])
            ->assertForbidden();
    }

    public function test_edit_requires_super_admin(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        $this->actingAs($this->member)
            ->get(route('super.admin.plans.edit', $plan))
            ->assertForbidden();
    }

    public function test_edit_displays_form(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('super.admin.plans.edit', $plan))
            ->assertOk()
            ->assertViewIs('super-admin.plans-form')
            ->assertSee($plan->name_en);
    }

    public function test_update_plan(): void
    {
        $this->actingAs($this->admin);
        $plan = SubscriptionPlan::factory()->create(['name_en' => 'Old Name']);

        $this->put(route('super.admin.plans.update', $plan), [
            'name_en' => 'New Name',
            'slug' => $plan->slug,
        ])->assertRedirect(route('super.admin.plans.index'));

        $this->assertEquals('New Name', $plan->fresh()->name_en);
    }

    public function test_destroy_plan(): void
    {
        $this->actingAs($this->admin);
        $plan = SubscriptionPlan::factory()->create();

        $this->delete(route('super.admin.plans.destroy', $plan))
            ->assertRedirect(route('super.admin.plans.index'));

        $this->assertDatabaseMissing('subscription_plans', ['id' => $plan->id]);
    }

    public function test_destroy_plan_with_subscriptions_fails(): void
    {
        $this->actingAs($this->admin);
        $plan = SubscriptionPlan::factory()->create();
        $workspace = Workspace::factory()->create();
        $plan->subscriptions()->create([
            'user_id' => $this->member->id,
            'workspace_id' => $workspace->id,
            'starts_at' => now(),
        ]);

        $this->delete(route('super.admin.plans.destroy', $plan))
            ->assertRedirect(route('super.admin.plans.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('subscription_plans', ['id' => $plan->id]);
    }
}
