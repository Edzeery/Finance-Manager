<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserZakatHaulTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'zakat_start_date' => now()->subDays(400),
            'calendar_type' => 'hijri',
            'last_zakat_date' => null,
        ]);
    }

    public function test_zakat_haul_days_returns_354_for_hijri(): void
    {
        $this->user->update(['calendar_type' => 'hijri']);
        $this->assertEquals(354, $this->user->zakatHaulDays());
    }

    public function test_zakat_haul_days_returns_365_for_gregorian(): void
    {
        $this->user->update(['calendar_type' => 'gregorian']);
        $this->assertEquals(365, $this->user->zakatHaulDays());
    }

    public function test_has_zakat_haul_started_returns_true_when_date_set(): void
    {
        $this->assertTrue($this->user->hasZakatHaulStarted());
    }

    public function test_has_zakat_haul_started_returns_false_when_null(): void
    {
        $this->user->update(['zakat_start_date' => null]);
        $this->assertFalse($this->user->hasZakatHaulStarted());
    }

    public function test_next_zakat_date_uses_start_date_when_no_last_zakat(): void
    {
        $this->user->update(['last_zakat_date' => null]);
        $expected = $this->user->zakat_start_date->copy()->addDays(354);
        $this->assertTrue($expected->eq($this->user->nextZakatDate()));
    }

    public function test_next_zakat_date_uses_last_zakat_when_set(): void
    {
        $lastZakat = now()->subDays(100)->startOfDay();
        $this->user->update(['last_zakat_date' => $lastZakat]);
        $expected = $lastZakat->copy()->addDays(354);
        $this->assertTrue($expected->eq($this->user->nextZakatDate()));
    }

    public function test_is_zakat_due_returns_true_when_past(): void
    {
        $this->user->update(['zakat_start_date' => now()->subDays(400)]);
        $this->assertTrue($this->user->isZakatDue());
    }

    public function test_is_zakat_due_returns_false_when_future(): void
    {
        $this->user->update(['zakat_start_date' => now()->subDays(100)]);
        $this->assertFalse($this->user->isZakatDue());
    }

    public function test_days_until_next_zakat_returns_positive_when_not_due(): void
    {
        $this->user->update(['zakat_start_date' => now()->subDays(100)]);
        $days = $this->user->daysUntilNextZakat();
        $this->assertGreaterThan(0, $days);
    }

    public function test_days_until_next_zakat_returns_zero_when_due(): void
    {
        $this->user->update(['zakat_start_date' => now()->subDays(400)]);
        $this->assertEquals(0, $this->user->daysUntilNextZakat());
    }

    public function test_days_until_next_zakat_returns_null_without_start_date(): void
    {
        $this->user->update(['zakat_start_date' => null]);
        $this->assertNull($this->user->daysUntilNextZakat());
    }

    public function test_next_zakat_date_returns_null_without_start_date(): void
    {
        $this->user->update(['zakat_start_date' => null]);
        $this->assertNull($this->user->nextZakatDate());
    }

    public function test_does_not_mutate_base_date(): void
    {
        $this->user->update(['last_zakat_date' => null]);
        $startBefore = $this->user->zakat_start_date->copy();
        $this->user->nextZakatDate();
        $this->assertTrue($startBefore->eq($this->user->zakat_start_date));
    }
}
