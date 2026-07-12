<?php

namespace Tests\Feature\Security;

use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_soft_deletes(): void
    {
        $workspace = Workspace::factory()->create();
        $workspace->delete();

        $this->assertSoftDeleted('workspaces', ['id' => $workspace->id]);
        $this->assertDatabaseHas('workspaces', ['id' => $workspace->id]);
    }

    public function test_workspace_can_be_restored(): void
    {
        $workspace = Workspace::factory()->create();
        $workspace->delete();
        $workspace->restore();

        $this->assertNotSoftDeleted('workspaces', ['id' => $workspace->id]);
    }
}
