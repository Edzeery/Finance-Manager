<?php

namespace Tests\Feature\Policies;

use App\Models\Income;
use App\Models\User;
use App\Models\Workspace;
use App\Policies\IncomePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomePolicyTest extends TestCase
{
    use RefreshDatabase;

    private IncomePolicy $policy;
    private Workspace $workspace;
    private User $user;
    private User $otherUser;
    private Income $income;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new IncomePolicy;
        $this->workspace = Workspace::factory()->create();
        $this->user = User::factory()->create(['current_workspace_id' => $this->workspace->id]);
        $this->user->workspaces()->attach($this->workspace->id);
        $this->otherUser = User::factory()->create();
        $this->income = Income::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_view_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->view($this->user, $this->income));
    }

    public function test_view_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->view($this->otherUser, $this->income));
    }

    public function test_create_returns_true(): void
    {
        $this->assertTrue($this->policy->create($this->user));
    }

    public function test_update_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->update($this->user, $this->income));
    }

    public function test_update_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->update($this->otherUser, $this->income));
    }

    public function test_delete_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->delete($this->user, $this->income));
    }

    public function test_delete_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->delete($this->otherUser, $this->income));
    }

    public function test_restore_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->restore($this->user, $this->income));
    }

    public function test_restore_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->restore($this->otherUser, $this->income));
    }

    public function test_archive_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->archive($this->user, $this->income));
    }

    public function test_archive_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->archive($this->otherUser, $this->income));
    }
}
