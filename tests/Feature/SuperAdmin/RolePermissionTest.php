<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_roles(): void
    {
        $superAdminRole = Role::create([
            'name' => 'Super Admin', 'slug' => 'super_admin',
            'level' => 'platform', 'is_system' => true, 'sort_order' => 1,
        ]);
        $viewPerm = Permission::create([
            'name' => 'View Roles', 'slug' => 'platform-role.view', 'module' => 'platform-role',
        ]);
        $superAdminRole->permissions()->attach($viewPerm->id);

        $user = User::factory()->create(['two_factor_confirmed_at' => now()]);
        $user->roles()->attach($superAdminRole->id);

        $response = $this->actingAs($user)->get(route('super.admin.roles.index'));
        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_roles(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('super.admin.roles.index'));
        $response->assertStatus(403);
    }
}
