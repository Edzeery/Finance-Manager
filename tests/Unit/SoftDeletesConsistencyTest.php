<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SoftDeletesConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_softdeletes_models_have_deleted_at_column(): void
    {
        $exitCode = Artisan::call('finance:validate-soft-deletes');

        $output = Artisan::output();

        $this->assertEquals(0, $exitCode, "SoftDeletes models without deleted_at:\n{$output}");
    }
}
