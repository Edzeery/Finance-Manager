<?php

namespace Tests\Feature\Api;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseApiTest extends TestCase
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
        Expense::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/expenses');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_store(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/expenses', [
                'category_id' => ExpenseCategory::factory()->create(['user_id' => $this->user->id])->id,
                'description' => 'Office Supplies',
                'amount' => 200,
                'date' => now()->format('Y-m-d'),
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['description' => 'Office Supplies']);
    }

    public function test_show(): void
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/workspace/expenses/{$expense->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $expense->id]);
    }

    public function test_update(): void
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->putJson("/api/workspace/expenses/{$expense->id}", [
                'category_id' => ExpenseCategory::factory()->create(['user_id' => $this->user->id])->id,
                'description' => 'Updated Expense',
                'amount' => 300,
                'date' => now()->format('Y-m-d'),
            ]);

        $response->assertOk();
        $this->assertEquals('Updated Expense', $expense->fresh()->description);
    }

    public function test_destroy(): void
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->deleteJson("/api/workspace/expenses/{$expense->id}");

        $response->assertOk();
        $this->assertSoftDeleted($expense);
    }

    public function test_404(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/expenses/99999');

        $response->assertStatus(404);
    }
}
