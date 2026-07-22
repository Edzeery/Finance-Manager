<?php

namespace Tests\Feature\Zakat;

use App\Models\Asset;
use App\Models\User;
use App\Models\ZakatRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class ZakatControllerTest extends TestCase
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

    public function test_calculator_displays_form(): void
    {
        Asset::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'type' => 'cash',
        ]);

        $this->actingAs($this->user)
            ->get(route('zakat.calculator'))
            ->assertOk()
            ->assertViewIs('zakat.calculator');
    }

    public function test_guest_cannot_access_calculator(): void
    {
        $this->get(route('zakat.calculator'))
            ->assertRedirect(route('login'));
    }

    public function test_calculate_returns_results(): void
    {
        $this->user->update(['zakat_start_date' => now()->subYear()]);

        $data = [
            'gold_price' => 100,
            'silver_price' => 5,
            'cash_value' => 10000,
        ];

        $this->actingAs($this->user)
            ->post(route('zakat.calculate'), $data)
            ->assertOk()
            ->assertViewIs('zakat.calculator')
            ->assertViewHas('result');
    }

    public function test_calculate_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('zakat.calculate'), [])
            ->assertSessionHasErrors(['silver_price']);
    }

    public function test_calculate_with_save_creates_record(): void
    {
        $this->user->update(['zakat_start_date' => now()->subYear()]);

        $data = [
            'gold_price' => 100,
            'silver_price' => 5,
            'cash_value' => 10000,
            'save' => true,
        ];

        $this->actingAs($this->user)
            ->post(route('zakat.calculate'), $data)
            ->assertRedirect();

        $this->assertDatabaseHas('zakat_records', [
            'user_id' => $this->user->id,
        ]);
    }

    public function test_history_displays_records(): void
    {
        ZakatRecord::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('zakat.history'))
            ->assertOk()
            ->assertViewIs('zakat.history');
    }

    public function test_guest_cannot_access_history(): void
    {
        $this->get(route('zakat.history'))
            ->assertRedirect(route('login'));
    }

    public function test_report_displays_record(): void
    {
        $record = ZakatRecord::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('zakat.report', $record))
            ->assertOk()
            ->assertViewIs('zakat.report');
    }

    public function test_user_cannot_view_other_users_zakat_record(): void
    {
        $otherUser = User::factory()->create();
        $record = ZakatRecord::factory()->create([
            'user_id' => $otherUser->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('zakat.report', $record))
            ->assertForbidden();
    }
}
