<?php

namespace Tests\Unit\Models;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_secret_returns_default_when_decryption_fails(): void
    {
        $key = 'payment.test.secret';
        Setting::create(['key' => $key, 'value' => 'tampered-value']);

        $this->assertNull(Setting::getSecret($key));
        $this->assertSame('fallback', Setting::getSecret($key, 'fallback'));
    }
}
