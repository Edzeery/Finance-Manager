<?php

namespace Tests\Unit\Services;

use App\Services\HijriDateService;
use Tests\TestCase;

class HijriDateServiceTest extends TestCase
{
    public function test_gregorian_to_hijri_known_date(): void
    {
        $date = new \DateTimeImmutable('2024-01-15');
        $hijri = HijriDateService::gregorianToHijri($date);

        $this->assertIsArray($hijri);
        $this->assertArrayHasKey('year', $hijri);
        $this->assertArrayHasKey('month', $hijri);
        $this->assertArrayHasKey('day', $hijri);
        $this->assertGreaterThan(1400, $hijri['year']);
        $this->assertLessThan(1500, $hijri['year']);
        $this->assertGreaterThanOrEqual(1, $hijri['month']);
        $this->assertLessThanOrEqual(12, $hijri['month']);
        $this->assertGreaterThanOrEqual(1, $hijri['day']);
        $this->assertLessThanOrEqual(30, $hijri['day']);
    }

    public function test_hijri_to_gregorian_known_date(): void
    {
        $gregorian = HijriDateService::hijriToGregorian(1445, 4, 15);

        $this->assertInstanceOf(\DateTimeImmutable::class, $gregorian);
        $this->assertGreaterThan(2020, (int) $gregorian->format('Y'));
        $this->assertLessThan(2030, (int) $gregorian->format('Y'));
    }

    public function test_round_trip_gregorian_to_hijri_to_gregorian(): void
    {
        $original = new \DateTimeImmutable('2024-06-15');
        $hijri = HijriDateService::gregorianToHijri($original);
        $back = HijriDateService::hijriToGregorian($hijri['year'], $hijri['month'], $hijri['day']);

        $this->assertEquals($original->format('Y-m-d'), $back->format('Y-m-d'));
    }

    public function test_format_hijri_date_arabic(): void
    {
        $hijri = ['year' => 1445, 'month' => 9, 'day' => 15];
        $formatted = HijriDateService::formatHijriDate($hijri, 'ar');

        $this->assertIsString($formatted);
        $this->assertStringContainsString('1445', $formatted);
        $this->assertStringContainsString('رمضان', $formatted);
    }

    public function test_format_hijri_date_english(): void
    {
        $hijri = ['year' => 1445, 'month' => 9, 'day' => 15];
        $formatted = HijriDateService::formatHijriDate($hijri, 'en');

        $this->assertIsString($formatted);
        $this->assertStringContainsString('1445', $formatted);
        $this->assertStringContainsString('Ramadan', $formatted);
    }

    public function test_hijri_month_days_odd_months(): void
    {
        foreach ([1, 3, 5, 7, 9, 11] as $month) {
            $this->assertEquals(30, HijriDateService::hijriMonthDays(1445, $month));
        }
    }

    public function test_hijri_month_days_even_months(): void
    {
        foreach ([2, 4, 6, 8, 10] as $month) {
            $this->assertEquals(29, HijriDateService::hijriMonthDays(1445, $month));
        }
    }

    public function test_hijri_format_short(): void
    {
        $hijri = ['year' => 1445, 'month' => 4, 'day' => 15];
        $result = HijriDateService::formatHijriShort($hijri);

        $this->assertEquals('1445-04-15', $result);
    }
}
