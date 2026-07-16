<?php

namespace Tests\Feature\Api;

use App\Models\IncomeCategory;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\CategorySeeder;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/auth/user');

        $response->assertOk()
            ->assertJsonFragment(['id' => $user->id]);
    }

    public function test_unauthenticated_request_gets_401(): void
    {
        $response = $this->getJson('/api/workspaces');

        $response->assertStatus(401);
    }

    public function test_token_with_limited_ability_cannot_write_income(): void
    {
        $this->seed([CategorySeeder::class, EnterpriseRolePermissionSeeder::class]);

        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();
        $user->workspaces()->attach($workspace, ['joined_at' => now()]);
        $adminRole = Role::where('slug', 'workspace_admin')->first();
        $user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $workspace->id]);
        $user->current_workspace_id = $workspace->id;
        $user->save();
        $category = IncomeCategory::first();

        $token = $user->createToken('limited', ['income:read'])->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/workspace/incomes', [
                'category_id' => $category->id,
                'amount' => 100,
                'date' => '2026-06-19',
                'description' => 'Test income',
            ])
            ->assertStatus(403)
            ->assertJsonFragment(['required_ability' => 'income:write']);

        $this->withToken($token)
            ->getJson('/api/workspace/incomes')
            ->assertOk();
    }

    public function test_health_endpoint(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertJsonFragment(['status' => 'healthy']);
    }

    public function test_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/auth/logout');

        $response->assertOk();
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $user->tokens()->first()?->id ?? 0]);
    }

    public function test_token_with_full_ability_can_write_income(): void
    {
        $this->seed([CategorySeeder::class, EnterpriseRolePermissionSeeder::class]);

        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();
        $user->workspaces()->attach($workspace, ['joined_at' => now()]);
        $adminRole = Role::where('slug', 'workspace_admin')->first();
        $user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $workspace->id]);
        $user->current_workspace_id = $workspace->id;
        $user->save();
        $category = IncomeCategory::first();

        $token = $user->createToken('full', ['*'])->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/workspace/incomes', [
                'category_id' => $category->id,
                'amount' => 100,
                'date' => '2026-06-19',
                'description' => 'Test income',
            ])
            ->assertCreated();
    }
}
