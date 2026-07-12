<?php

namespace Tests\Feature;

use Tests\TestCase;

class DebugModeSafetyTest extends TestCase
{
    public function test_debug_mode_is_disabled_in_production(): void
    {
        if (app()->environment('production')) {
            $this->assertFalse(
                config('app.debug'),
                'CRITICAL: APP_DEBUG=true in production environment! This exposes sensitive information.'
            );
        } else {
            $this->markTestSkipped('Not in production environment — debug mode check skipped.');
        }
    }

    public function test_env_example_has_debug_disabled(): void
    {
        $example = file_get_contents(base_path('.env.example'));

        $this->assertStringContainsString(
            'APP_DEBUG=false',
            $example,
            '.env.example must have APP_DEBUG=false for production safety.'
        );
    }
}
