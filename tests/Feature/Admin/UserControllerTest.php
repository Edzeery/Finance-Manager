<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EnterpriseRolePermissionSeeder::class);
        $this->admin = User::factory()->create(['two_factor_confirmed_at' => now()]);
        $this->admin->roles()->attach(Role::where('slug', 'super_admin')->first());
        $this->member = User::factory()->create();
    }

    public function test_index_requires_super_admin(): void
    {
        $this->actingAs($this->member)
            ->get(route('super.admin.users.index'))
            ->assertForbidden();
    }

    public function test_index_displays_users(): void
    {
        $this->actingAs($this->admin)
            ->get(route('super.admin.users.index'))
            ->assertOk()
            ->assertViewIs('super-admin.users')
            ->assertViewHas('users');
    }

    public function test_create_requires_super_admin(): void
    {
        $this->actingAs($this->member)
            ->get(route('super.admin.users.create'))
            ->assertForbidden();
    }

    public function test_create_displays_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('super.admin.users.create'))
            ->assertOk()
            ->assertViewIs('super-admin.user-create');
    }

    public function test_store_creates_user(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('super.admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password1',
        ]);

        $response->assertRedirect(route('super.admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_store_requires_super_admin(): void
    {
        $this->actingAs($this->member)
            ->post(route('super.admin.users.store'), [
                'name' => 'Test',
                'email' => 'test@example.com',
                'password' => 'Password1',
            ])
            ->assertForbidden();
    }

    public function test_edit_requires_super_admin(): void
    {
        $this->actingAs($this->member)
            ->get(route('super.admin.users.edit', $this->member))
            ->assertForbidden();
    }

    public function test_edit_displays_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('super.admin.users.edit', $this->member))
            ->assertOk()
            ->assertViewIs('super-admin.user-edit');
    }

    public function test_update_user(): void
    {
        $this->actingAs($this->admin);

        $this->put(route('super.admin.users.update', $this->member), [
            'name' => 'Updated Name',
            'email' => $this->member->email,
        ])->assertRedirect(route('super.admin.users.index'));

        $this->assertEquals('Updated Name', $this->member->fresh()->name);
    }

    public function test_toggle_status(): void
    {
        $this->actingAs($this->admin);
        $this->assertTrue($this->member->is_active);

        $this->post(route('super.admin.users.toggle-status', $this->member))
            ->assertRedirect(route('super.admin.users.index'));

        $this->assertFalse($this->member->fresh()->is_active);
    }

    public function test_destroy_user(): void
    {
        $this->actingAs($this->admin);

        $this->delete(route('super.admin.users.destroy', $this->member))
            ->assertRedirect(route('super.admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $this->member->id]);
    }
}
