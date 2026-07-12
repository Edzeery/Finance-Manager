<?php

namespace Tests\Unit\Enums;

use App\Enums\DebtType;
use Tests\TestCase;

class DebtTypeTest extends TestCase
{
    public function test_all_cases_have_labels(): void
    {
        foreach (DebtType::cases() as $case) {
            $this->assertNotEmpty($case->label());
        }
    }

    public function test_owed_is_owed(): void
    {
        $this->assertEquals('owed', DebtType::Owed->value);
    }

    public function test_owing_is_owing(): void
    {
        $this->assertEquals('owing', DebtType::Owing->value);
    }
}
