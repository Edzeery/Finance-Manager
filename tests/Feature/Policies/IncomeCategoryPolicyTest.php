<?php

namespace Tests\Feature\Policies;

use App\Models\IncomeCategory;
use App\Models\User;
use App\Models\Workspace;
use App\Policies\IncomeCategoryPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeCategoryPolicyTest extends TestCase
{
    use RefreshDatabase;

    private IncomeCategoryPolicy $policy;
    private Workspace $workspace;
    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new IncomeCategoryPolicy;
        $this->workspace = Workspace::factory()->create();
        $this->user = User::factory()->create(['current_workspace_id' => $this->workspace->id]);
        $this->user->workspaces()->attach($this->workspace->id);
        $this->otherUser = User::factory()->create();
    }

    public function test_view_returns_true_for_owner(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => $this->user->id]);
        $this->assertTrue($this->policy->view($this->user, $category));
    }

    public function test_view_returns_true_for_global_category(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => null]);
        $this->assertTrue($this->policy->view($this->user, $category));
    }

    public function test_view_returns_true_for_other_user_global(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => null]);
        $this->assertTrue($this->policy->view($this->otherUser, $category));
    }

    public function test_view_returns_false_for_other_user_private(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => $this->user->id]);
        $this->assertFalse($this->policy->view($this->otherUser, $category));
    }

    public function test_create_returns_true(): void
    {
        $this->assertTrue($this->policy->create($this->user));
    }

    public function test_update_returns_true_for_owner(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => $this->user->id]);
        $this->assertTrue($this->policy->update($this->user, $category));
    }

    public function test_update_returns_false_for_global_category(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => null]);
        $this->assertFalse($this->policy->update($this->user, $category));
    }

    public function test_update_returns_false_for_other_user(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => $this->user->id]);
        $this->assertFalse($this->policy->update($this->otherUser, $category));
    }

    public function test_delete_returns_true_for_owner(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => $this->user->id]);
        $this->assertTrue($this->policy->delete($this->user, $category));
    }

    public function test_delete_returns_false_for_global_category(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => null]);
        $this->assertFalse($this->policy->delete($this->user, $category));
    }

    public function test_delete_returns_false_for_other_user(): void
    {
        $category = IncomeCategory::factory()->create(['user_id' => $this->user->id]);
        $this->assertFalse($this->policy->delete($this->otherUser, $category));
    }
}
