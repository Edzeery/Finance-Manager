<?php

namespace Tests\Feature\Api;

use App\Models\Asset;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetApiTest extends TestCase
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
        Asset::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/assets');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_store(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/workspace/assets', [
                'name' => 'Test Asset',
                'type' => 'cash',
                'total_value' => 10000,
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Asset', 'total_value' => 10000]);
    }

    public function test_show(): void
    {
        $asset = Asset::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/workspace/assets/{$asset->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $asset->id]);
    }

    public function test_update(): void
    {
        $asset = Asset::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->putJson("/api/workspace/assets/{$asset->id}", [
                'name' => 'Updated Asset',
                'total_value' => 20000,
            ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Updated Asset', 'total_value' => 20000]);
    }

    public function test_destroy(): void
    {
        $asset = Asset::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->withToken($this->token)
            ->deleteJson("/api/workspace/assets/{$asset->id}");

        $response->assertOk();
        $this->assertSoftDeleted($asset);
    }

    public function test_404(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/workspace/assets/99999');

        $response->assertStatus(404);
    }
}
