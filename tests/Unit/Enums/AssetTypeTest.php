<?php

namespace Tests\Unit\Enums;

use App\Enums\AssetType;
use Tests\TestCase;

class AssetTypeTest extends TestCase
{
    public function test_all_cases_have_labels(): void
    {
        foreach (AssetType::cases() as $case) {
            $this->assertNotEmpty($case->label(), "AssetType {$case->value} has no label");
        }
    }

    public function test_all_cases_have_icons(): void
    {
        foreach (AssetType::cases() as $case) {
            $this->assertNotEmpty($case->icon(), "AssetType {$case->value} has no icon");
        }
    }

    public function test_all_cases_have_colors(): void
    {
        foreach (AssetType::cases() as $case) {
            $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $case->color(), "AssetType {$case->value} has invalid color");
        }
    }

    public function test_zakatable_returns_array(): void
    {
        $types = AssetType::zakatable();
        $this->assertIsArray($types);
        $this->assertNotEmpty($types);
    }

    public function test_cash_is_zakatable(): void
    {
        $this->assertTrue(AssetType::Cash->isZakatable());
    }

    public function test_real_estate_is_not_zakatable(): void
    {
        $this->assertFalse(AssetType::RealEstate->isZakatable());
    }
}
