<?php

namespace Tests\Feature\Api;

use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\PlanFeature;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;

        $workspace = Workspace::factory()->create();
        $workspace->users()->attach($this->user->id, []);
        $adminRole = Role::where('slug', 'workspace_admin')->first();
        if ($adminRole) {
            $this->user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $workspace->id]);
        }
        $plan = SubscriptionPlan::create([
            'slug' => 'personal', 'name_en' => 'Personal', 'is_free' => true,
        ]);
        $feature = PlanFeature::firstOrCreate(
            ['slug' => 'transactions_per_month'],
            ['name_en' => 'Transactions Per Month', 'name_ar' => 'Transactions Per Month', 'name_fr' => 'Transactions Per Month', 'type' => 'value']
        );
        $plan->planFeatures()->syncWithoutDetaching([$feature->id => ['value' => '1000', 'sort_order' => 0]]);
        Subscription::create([
            'workspace_id' => $workspace->id,
            'user_id' => $this->user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);

        $this->user->current_workspace_id = $workspace->id;
        $this->user->save();
    }

    public function test_list_workspaces(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/workspaces');

        $response->assertOk()
            ->assertJsonCount(1);
    }

    public function test_create_workspace(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/workspaces', [
                'name_en' => 'New Workspace',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name_en' => 'New Workspace']);
    }

    public function test_income_crud(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/incomes', [
                'category_id' => $category->id,
                'amount' => 500,
                'date' => '2026-06-15',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['amount' => '500.00']);
    }

    public function test_workspace_switch(): void
    {
        $newWorkspace = Workspace::factory()->create();
        $newWorkspace->users()->attach($this->user->id, []);
        $adminRole = Role::where('slug', 'workspace_admin')->first();
        if ($adminRole) {
            $this->user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $newWorkspace->id]);
        }

        $response = $this->withToken($this->token)
            ->postJson("/api/workspaces/{$newWorkspace->id}/switch");

        $response->assertOk()
            ->assertJsonFragment(['workspace_id' => $newWorkspace->id]);
    }

    public function test_expense_crud(): void
    {
        $category = ExpenseCategory::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/expenses', [
                'category_id' => $category->id,
                'amount' => 300,
                'date' => '2026-06-15',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['amount' => '300.00']);
    }
}
