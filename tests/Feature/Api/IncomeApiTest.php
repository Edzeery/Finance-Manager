<?php

namespace Tests\Feature\Api;

use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Workspace $workspace;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test', ['*'])->plainTextToken;

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
        Income::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/incomes');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_store(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/incomes', [
                'category_id' => IncomeCategory::factory()->create(['user_id' => $this->user->id])->id,
                'description' => 'Salary',
                'amount' => 5000,
                'date' => now()->format('Y-m-d'),
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['description' => 'Salary']);
    }

    public function test_show(): void
    {
        $income = Income::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/workspace/incomes/{$income->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $income->id]);
    }

    public function test_update(): void
    {
        $income = Income::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->putJson("/api/workspace/incomes/{$income->id}", [
                'category_id' => IncomeCategory::factory()->create(['user_id' => $this->user->id])->id,
                'description' => 'Updated Income',
                'amount' => 6000,
                'date' => now()->format('Y-m-d'),
            ]);

        $response->assertOk();
        $this->assertEquals('Updated Income', $income->fresh()->description);
    }

    public function test_destroy(): void
    {
        $income = Income::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->deleteJson("/api/workspace/incomes/{$income->id}");

        $response->assertOk();
        $this->assertSoftDeleted($income);
    }

    public function test_404(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/incomes/99999');

        $response->assertStatus(404);
    }
}
