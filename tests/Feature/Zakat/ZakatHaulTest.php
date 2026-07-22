<?php

namespace Tests\Feature\Zakat;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class ZakatHaulTest extends TestCase
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

    public function test_calculate_blocked_when_no_start_date(): void
    {
        $this->user->update(['zakat_start_date' => null]);

        $data = ['silver_price' => 5, 'cash_value' => 10000];

        $this->actingAs($this->user)
            ->post(route('zakat.calculate'), $data)
            ->assertRedirect()
            ->assertSessionHasErrors('zakat_start_date');
    }

    public function test_calculate_blocked_when_haul_not_due(): void
    {
        $this->user->update([
            'zakat_start_date' => now()->subDays(100),
            'calendar_type' => 'hijri',
        ]);

        $data = ['silver_price' => 5, 'cash_value' => 10000];

        $this->actingAs($this->user)
            ->post(route('zakat.calculate'), $data)
            ->assertRedirect()
            ->assertSessionHasErrors('haul');
    }

    public function test_calculate_allowed_when_haul_due(): void
    {
        $this->user->update([
            'zakat_start_date' => now()->subDays(400),
            'calendar_type' => 'hijri',
        ]);

        $data = ['silver_price' => 5, 'cash_value' => 10000];

        $this->actingAs($this->user)
            ->post(route('zakat.calculate'), $data)
            ->assertOk()
            ->assertViewIs('zakat.calculator')
            ->assertViewHas('result');
    }

    public function test_update_haul_settings_succeeds(): void
    {
        $this->actingAs($this->user)
            ->put(route('zakat.haul-settings'), [
                'zakat_start_date_hijri_year' => '1445',
                'zakat_start_date_hijri_month' => '4',
                'zakat_start_date_hijri_day' => '15',
                'calendar_type' => 'hijri',
            ])
            ->assertRedirect();

        $this->user->refresh();
        $this->assertNotNull($this->user->zakat_start_date);
        $this->assertEquals('hijri', $this->user->calendar_type);
    }

    public function test_update_haul_settings_rejects_future_date(): void
    {
        $this->actingAs($this->user)
            ->put(route('zakat.haul-settings'), [
                'zakat_start_date_hijri_year' => '1450',
                'zakat_start_date_hijri_month' => '6',
                'zakat_start_date_hijri_day' => '15',
                'calendar_type' => 'hijri',
            ])
            ->assertSessionHasErrors('zakat_start_date_hijri_year');
    }

    public function test_update_haul_settings_gregorian(): void
    {
        $this->actingAs($this->user)
            ->put(route('zakat.haul-settings'), [
                'zakat_start_date' => '2024-01-15',
                'calendar_type' => 'gregorian',
            ])
            ->assertRedirect();

        $this->user->refresh();
        $this->assertEquals('2024-01-15', $this->user->zakat_start_date->format('Y-m-d'));
        $this->assertEquals('gregorian', $this->user->calendar_type);
    }

    public function test_update_haul_settings_rejects_invalid_calendar_type(): void
    {
        $this->actingAs($this->user)
            ->put(route('zakat.haul-settings'), [
                'zakat_start_date' => '2024-01-15',
                'calendar_type' => 'invalid',
            ])
            ->assertSessionHasErrors('calendar_type');
    }

    public function test_save_updates_last_zakat_date(): void
    {
        $this->user->update([
            'zakat_start_date' => now()->subDays(400),
            'calendar_type' => 'hijri',
        ]);

        $data = [
            'silver_price' => 5,
            'cash_value' => 10000,
            'save' => true,
        ];

        $this->actingAs($this->user)
            ->post(route('zakat.calculate'), $data)
            ->assertRedirect();

        $this->user->refresh();
        $this->assertNotNull($this->user->last_zakat_date);
        $this->assertTrue($this->user->last_zakat_date->isToday());
    }

    public function test_save_stores_calendar_type_in_record(): void
    {
        $this->user->update([
            'zakat_start_date' => now()->subDays(400),
            'calendar_type' => 'hijri',
        ]);

        $data = [
            'silver_price' => 5,
            'cash_value' => 10000,
            'save' => true,
        ];

        $this->actingAs($this->user)
            ->post(route('zakat.calculate'), $data);

        $this->assertDatabaseHas('zakat_records', [
            'user_id' => $this->user->id,
            'calendar_type' => 'hijri',
        ]);
    }
}
