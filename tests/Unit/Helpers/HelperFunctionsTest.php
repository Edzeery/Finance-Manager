<?php

namespace Tests\Unit\Helpers;

use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\TestCase;

class HelperFunctionsTest extends TestCase
{
    public function test_currency_format_exists(): void
    {
        $this->assertTrue(function_exists('currency_format'));
    }

    public function test_locale_name_exists(): void
    {
        $this->assertTrue(function_exists('locale_name'));
    }
}
