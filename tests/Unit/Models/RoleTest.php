<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EnterpriseRolePermissionSeeder::class);
    }

    public function test_role_has_permissions(): void
    {
        $role = Role::where('slug', 'super_admin')->first();
        $this->assertTrue($role->permissions->isNotEmpty());
    }

    public function test_role_has_permission_method(): void
    {
        $role = Role::where('slug', 'super_admin')->first();
        $this->assertTrue($role->hasPermission('tenant.view'));
    }

    public function test_viewer_role_has_no_write_permissions(): void
    {
        $viewer = Role::where('slug', 'workspace_viewer')->first();
        $this->assertTrue($viewer->permissions->isNotEmpty());
        $this->assertFalse($viewer->hasPermission('income.create'));
    }

    public function test_user_has_role(): void
    {
        $user = User::factory()->create();
        $role = Role::where('slug', 'workspace_viewer')->first();
        $user->roles()->attach($role);

        $this->assertTrue($user->hasRole('workspace_viewer'));
        $this->assertFalse($user->hasRole('super_admin'));
    }

    public function test_user_has_role_with_array(): void
    {
        $user = User::factory()->create();
        $role = Role::where('slug', 'workspace_viewer')->first();
        $user->roles()->attach($role);

        $this->assertTrue($user->hasRole(['super_admin', 'workspace_viewer']));
    }

    public function test_user_has_workspace_permission_through_role(): void
    {
        $user = User::factory()->create();
        $workspace = \App\Models\Workspace::factory()->create();
        $adminRole = Role::where('slug', 'workspace_admin')->first();
        $user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $workspace->id]);
        $user->current_workspace_id = $workspace->id;
        $user->save();

        $this->assertTrue($user->hasPermission('income.view'));
        $this->assertTrue($user->hasPermission('income.create'));
    }

    public function test_user_without_role_has_no_permission(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->hasPermission('income.view'));
    }

    public function test_role_can_have_multiple_users(): void
    {
        $role = Role::where('slug', 'workspace_viewer')->first();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $role->users()->attach([$user1->id, $user2->id]);

        $this->assertEquals(2, $role->users()->count());
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create();
        $roles = Role::where('level', 'platform')->get();
        $user->roles()->attach($roles->pluck('id'));

        $this->assertEquals($roles->count(), $user->roles()->count());
    }
}
