<?php

namespace Tests\Feature\Api;

use App\Models\Debt;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebtApiTest extends TestCase
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
        Debt::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/debts');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_store(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/debts', [
                'type' => 'owed',
                'counterparty_name' => 'Bank',
                'total_amount' => 50000,
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['type' => 'owed', 'total_amount' => '50000.00']);
    }

    public function test_show(): void
    {
        $debt = Debt::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/workspace/debts/{$debt->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $debt->id]);
    }

    public function test_update(): void
    {
        $debt = Debt::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->putJson("/api/workspace/debts/{$debt->id}", [
                'counterparty_name' => 'Updated Bank',
                'total_amount' => 60000,
            ]);

        $response->assertOk()
            ->assertJsonFragment(['counterparty_name' => 'Updated Bank', 'total_amount' => '60000.00']);
    }

    public function test_destroy(): void
    {
        $debt = Debt::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->deleteJson("/api/workspace/debts/{$debt->id}");

        $response->assertOk();
        $this->assertSoftDeleted($debt);
    }

    public function test_404(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/debts/99999');

        $response->assertStatus(404);
    }
}
