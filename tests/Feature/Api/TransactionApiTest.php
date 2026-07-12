<?php

namespace Tests\Feature\Api;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TransactionApiTest extends TestCase
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

    public function test_transactions_combines_income_and_expenses(): void
    {
        Income::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'date' => '2026-06-15',
        ]);
        Expense::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'date' => '2026-06-14',
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/transactions');

        $response->assertOk()
            ->assertJsonCount(5);
    }

    public function test_transactions_sorted_by_date_desc(): void
    {
        Income::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'date' => '2026-06-10',
        ]);
        Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'date' => '2026-06-20',
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/transactions');

        $response->assertOk();
        $data = $response->json();
        $dates = array_column($data, 'date');
        $this->assertTrue($dates[0] >= $dates[1]);
    }
}
