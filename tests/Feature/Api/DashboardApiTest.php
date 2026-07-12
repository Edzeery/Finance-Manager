<?php

namespace Tests\Feature\Api;

use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Workspace $workspace;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;

        $this->workspace = Workspace::factory()->create();
        $this->workspace->users()->attach($this->user->id, []);
        $adminRole = Role::where('slug', 'workspace_admin')->first();
        if ($adminRole) {
            $this->user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $this->workspace->id]);
        }
        $this->user->current_workspace_id = $this->workspace->id;
        $this->user->save();
    }

    public function test_dashboard_returns_kpis_and_chart(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/dashboard');

        $response->assertOk()
            ->assertJsonStructure(['kpis', 'chart']);
    }
}
