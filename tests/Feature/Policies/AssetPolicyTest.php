<?php

namespace Tests\Feature\Policies;

use App\Models\Asset;
use App\Models\User;
use App\Models\Workspace;
use App\Policies\AssetPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetPolicyTest extends TestCase
{
    use RefreshDatabase;

    private AssetPolicy $policy;
    private Workspace $workspace;
    private User $user;
    private User $otherUser;
    private Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new AssetPolicy;
        $this->workspace = Workspace::factory()->create();
        $this->user = User::factory()->create(['current_workspace_id' => $this->workspace->id]);
        $this->user->workspaces()->attach($this->workspace->id);
        $this->otherUser = User::factory()->create();
        $this->asset = Asset::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_view_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->view($this->user, $this->asset));
    }

    public function test_view_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->view($this->otherUser, $this->asset));
    }

    public function test_create_returns_true(): void
    {
        $this->assertTrue($this->policy->create($this->user));
    }

    public function test_update_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->update($this->user, $this->asset));
    }

    public function test_update_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->update($this->otherUser, $this->asset));
    }

    public function test_delete_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->delete($this->user, $this->asset));
    }

    public function test_delete_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->delete($this->otherUser, $this->asset));
    }

    public function test_restore_returns_true_for_owner(): void
    {
        $this->assertTrue($this->policy->restore($this->user, $this->asset));
    }

    public function test_restore_returns_false_for_other_user(): void
    {
        $this->assertFalse($this->policy->restore($this->otherUser, $this->asset));
    }
}
