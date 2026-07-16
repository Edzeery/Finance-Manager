<?php

namespace Tests\Unit\Services;

use App\Helpers\CurrencyFormatter;
use Tests\TestCase;

class CurrencyFormatterTest extends TestCase
{
    public function test_format_returns_string(): void
    {
        $result = CurrencyFormatter::format(1000);
        $this->assertIsString($result);
    }

    public function test_format_handles_zero(): void
    {
        $result = CurrencyFormatter::format(0);
        $this->assertIsString($result);
    }

    public function test_locale_name_returns_fallback_when_model_empty(): void
    {
        $model = new \stdClass;
        $result = CurrencyFormatter::localeName($model);
        $this->assertEquals('—', $result);
    }
}
