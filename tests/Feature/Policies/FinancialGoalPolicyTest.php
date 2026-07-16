<?php

namespace Tests\Feature\Policies;

use App\Models\FinancialGoal;
use App\Models\User;
use App\Models\Workspace;
use App\Policies\FinancialGoalPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialGoalPolicyTest extends TestCase
{
    use RefreshDatabase;

    private FinancialGoalPolicy $policy;

    private Workspace $workspace;

    private User $user;

    private User $otherUser;

    private FinancialGoal $goal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new FinancialGoalPolicy;
        $this->workspace = Workspace::factory()->create();
        $this->user = User::factory()->create(['current_workspace_id' => $this->workspace->id]);
        $this->user->workspaces()->attach($this->workspace->id);
        $this->otherUser = User::factory()->create();
        $this->goal = FinancialGoal::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_view_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->view($this->user, $this->goal));
    }

    public function test_view_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->view($this->otherUser, $this->goal));
    }

    public function test_create_returns_true(): void
    {
        $this->assertTrue($this->policy->create($this->user));
    }

    public function test_update_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->update($this->user, $this->goal));
    }

    public function test_update_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->update($this->otherUser, $this->goal));
    }

    public function test_delete_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->delete($this->user, $this->goal));
    }

    public function test_delete_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->delete($this->otherUser, $this->goal));
    }

    public function test_restore_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->restore($this->user, $this->goal));
    }

    public function test_restore_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->restore($this->otherUser, $this->goal));
    }
}
