<?php

namespace Tests\Unit\Enums;

use App\Enums\RecurringFrequency;
use Tests\TestCase;

class RecurringFrequencyTest extends TestCase
{
    public function test_all_cases_have_labels(): void
    {
        foreach (RecurringFrequency::cases() as $case) {
            $this->assertNotEmpty($case->label());
        }
    }
}
