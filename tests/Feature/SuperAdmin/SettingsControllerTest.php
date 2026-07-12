<?php

namespace Tests\Feature\SuperAdmin;

use App\Http\Controllers\SuperAdmin\SettingsController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_system_setting_update_is_rejected(): void
    {
        $user = User::factory()->create();
        $controller = new SettingsController;
        $request = Request::create('/super-admin/settings/system', 'PUT', [
            'app_env' => 'local',
            'app_debug' => 'true',
            'app_url' => 'https://example.com',
            'log_level' => 'debug',
            'log_channel' => 'daily',
            'session_encrypt' => 'true',
            'session_secure_cookie' => 'true',
            'session_same_site' => 'lax',
        ]);
        $request->setUserResolver(fn () => $user);

        $response = $controller->updateSystem($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertTrue($response->getSession()->get('errors')->has('system'));
    }
}
