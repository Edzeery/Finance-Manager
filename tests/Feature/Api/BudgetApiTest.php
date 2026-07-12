<?php

namespace Tests\Feature\Api;

use App\Models\Budget;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetApiTest extends TestCase
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
        Budget::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/budgets');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_store(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/budgets', [
                'name_en' => 'Monthly Budget',
                'total_amount' => 100000,
                'type' => 'monthly',
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-30',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name_en' => 'Monthly Budget', 'total_amount' => '100000.00']);
    }

    public function test_show(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/workspace/budgets/{$budget->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $budget->id]);
    }

    public function test_update(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->putJson("/api/workspace/budgets/{$budget->id}", [
                'name_en' => 'Updated Budget',
                'total_amount' => 200000,
            ]);

        $response->assertOk()
            ->assertJsonFragment(['name_en' => 'Updated Budget', 'total_amount' => '200000.00']);
    }

    public function test_destroy(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->deleteJson("/api/workspace/budgets/{$budget->id}");

        $response->assertOk();
        $this->assertSoftDeleted($budget);
    }

    public function test_404(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/budgets/99999');

        $response->assertStatus(404);
    }
}
