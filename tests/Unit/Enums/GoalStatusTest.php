<?php

namespace Tests\Unit\Enums;

use App\Enums\GoalStatus;
use Tests\TestCase;

class GoalStatusTest extends TestCase
{
    public function test_all_cases_have_labels(): void
    {
        foreach (GoalStatus::cases() as $case) {
            $this->assertNotEmpty($case->label());
        }
    }
}
