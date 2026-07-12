<?php

namespace Tests\Unit\Enums;

use App\Enums\DebtStatus;
use Tests\TestCase;

class DebtStatusTest extends TestCase
{
    public function test_all_cases_have_labels(): void
    {
        foreach (DebtStatus::cases() as $case) {
            $this->assertNotEmpty($case->label());
        }
    }

    public function test_all_cases_have_colors(): void
    {
        foreach (DebtStatus::cases() as $case) {
            $this->assertNotEmpty($case->color());
        }
    }
}
