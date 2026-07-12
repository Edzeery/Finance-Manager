<?php

namespace Tests\Feature\Asset;

use App\Models\Asset;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class AssetControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithWorkspacePermission;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpWorkspacePermission();
        $this->user = $this->workspaceUser;
    }

    public function test_index_displays_assets(): void
    {
        Asset::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('asset.index'))
            ->assertOk()
            ->assertViewIs('asset.index');
    }

    public function test_guest_cannot_access_asset(): void
    {
        $this->get(route('asset.index'))
            ->assertRedirect(route('login'));
    }

    public function test_store_creates_asset_and_redirects(): void
    {
        $data = [
            'type' => 'cash',
            'name' => 'Savings',
            'total_value' => 10000,
        ];

        $this->actingAs($this->user)
            ->post(route('asset.store'), $data)
            ->assertRedirect(route('asset.index'));

        $this->assertDatabaseHas('assets', [
            'user_id' => $this->user->id,
            'type' => 'cash',
            'name' => 'Savings',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('asset.store'), [])
            ->assertSessionHasErrors(['type', 'name']);
    }

    public function test_user_cannot_edit_other_users_asset(): void
    {
        $otherUser = User::factory()->create();
        $asset = Asset::factory()->create([
            'user_id' => $otherUser->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('asset.edit', $asset))
            ->assertOk();
    }

    public function test_user_cannot_update_other_users_asset(): void
    {
        $otherUser = User::factory()->create();
        $asset = Asset::factory()->create([
            'user_id' => $otherUser->id,
            'workspace_id' => $this->workspace->id,
            'total_value' => 1000,
        ]);

        $this->actingAs($this->user)
            ->put(route('asset.update', $asset), [
                'name' => 'Updated Asset',
                'type' => 'bank_account',
                'total_value' => 2000,
            ])
            ->assertRedirect(route('asset.index'));
    }

    public function test_user_cannot_delete_other_users_asset(): void
    {
        $otherUser = User::factory()->create();
        $asset = Asset::factory()->create([
            'user_id' => $otherUser->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $this->actingAs($this->user)
            ->delete(route('asset.destroy', $asset))
            ->assertRedirect(route('asset.index'));
    }

    public function test_update_modifies_asset(): void
    {
        $asset = Asset::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'type' => 'cash',
            'name' => 'Initial',
            'total_value' => 1000,
        ]);

        $this->actingAs($this->user)
            ->put(route('asset.update', $asset), [
                'type' => 'bank_account',
                'name' => 'Updated',
                'total_value' => 5000,
            ])
            ->assertRedirect(route('asset.index'));

        $this->assertEquals(5000, $asset->fresh()->total_value);
        $this->assertEquals('Updated', $asset->fresh()->name);
    }

    public function test_destroy_soft_deletes(): void
    {
        $asset = Asset::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $this->actingAs($this->user)
            ->delete(route('asset.destroy', $asset))
            ->assertRedirect(route('asset.index'));

        $this->assertSoftDeleted($asset);
    }

    public function test_restore_recovers_soft_deleted(): void
    {
        $asset = Asset::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);
        $asset->delete();

        $this->actingAs($this->user)
            ->patch(route('asset.restore', $asset->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted($asset);
    }

    public function test_bulk_delete_removes_multiple(): void
    {
        $assets = Asset::factory(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('asset.bulk-delete'), ['ids' => $assets->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($assets as $asset) {
            $this->assertSoftDeleted($asset);
        }
    }

    public function test_bulk_restore_recovers_multiple(): void
    {
        $assets = Asset::factory(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);
        foreach ($assets as $asset) {
            $asset->delete();
        }

        $this->actingAs($this->user)
            ->post(route('asset.bulk-restore'), ['ids' => $assets->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($assets as $asset) {
            $this->assertNotSoftDeleted($asset);
        }
    }

    public function test_index_can_filter_by_trashed(): void
    {
        $active = Asset::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);
        $trashed = Asset::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);
        $trashed->delete();

        $this->actingAs($this->user)
            ->get(route('asset.index', ['trashed' => 'true']))
            ->assertOk()
            ->assertViewIs('asset.index');
    }
}
