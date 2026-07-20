<?php

namespace Tests\Feature\Api;

use App\Models\FinancialGoal;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalApiTest extends TestCase
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

    public function test_index(): void
    {
        FinancialGoal::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/goals');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_store(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/goals', [
                'name_en' => 'Save for House',
                'target_amount' => 500000,
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Save for House', 'target_amount' => 500000]);
    }

    public function test_show(): void
    {
        $goal = FinancialGoal::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/workspace/goals/{$goal->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $goal->id]);
    }

    public function test_update(): void
    {
        $goal = FinancialGoal::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->putJson("/api/workspace/goals/{$goal->id}", [
                'name_en' => 'Updated Goal',
                'target_amount' => 600000,
            ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Updated Goal', 'target_amount' => 600000]);
    }

    public function test_destroy(): void
    {
        $goal = FinancialGoal::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->deleteJson("/api/workspace/goals/{$goal->id}");

        $response->assertOk();
        $this->assertSoftDeleted($goal);
    }

    public function test_404(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/goals/99999');

        $response->assertStatus(404);
    }
}
