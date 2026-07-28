<?php

namespace Tests\Feature\SuperAdmin;

use App\Http\Controllers\SuperAdmin\SettingsController;
use App\Models\User;
use App\Services\EnvWriter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_settings_are_written_to_env_file(): void
    {
        $tmpEnv = tempnam(sys_get_temp_dir(), 'env_test_');
        file_put_contents($tmpEnv, "APP_ENV=local\nAPP_DEBUG=true\nAPP_URL=http://localhost\nLOG_LEVEL=warning\nLOG_CHANNEL=daily\nSESSION_DRIVER=database\nSESSION_ENCRYPT=false\nSESSION_SECURE_COOKIE=false\nSESSION_SAME_SITE=lax\n");

        $envWriter = new EnvWriter($tmpEnv);
        $controller = new SettingsController;
        $user = User::factory()->create();

        $request = Request::create('/super-admin/settings/system', 'PUT', [
            'app_env' => 'production',
            'app_url' => 'https://example.com',
            'session_driver' => 'redis',
            'log_level' => 'error',
            'log_channel' => 'single',
            'session_encrypt' => 'true',
            'session_secure_cookie' => 'true',
            'session_same_site' => 'strict',
        ]);
        $request->setUserResolver(fn () => $user);

        $response = $controller->updateSystem($request, $envWriter);

        $this->assertInstanceOf(RedirectResponse::class, $response);

        $envContent = file_get_contents($tmpEnv);
        $this->assertStringContainsString('APP_ENV=production', $envContent);
        $this->assertStringContainsString('APP_URL=https://example.com', $envContent);
        $this->assertStringContainsString('SESSION_DRIVER=redis', $envContent);
        $this->assertStringContainsString('LOG_LEVEL=error', $envContent);
        $this->assertStringContainsString('LOG_CHANNEL=single', $envContent);
        $this->assertStringContainsString('SESSION_ENCRYPT=true', $envContent);
        $this->assertStringContainsString('SESSION_SECURE_COOKIE=true', $envContent);
        $this->assertStringContainsString('SESSION_SAME_SITE=strict', $envContent);

        @unlink($tmpEnv);
    }

    public function test_system_settings_validation_rejects_invalid_values(): void
    {
        $tmpEnv = tempnam(sys_get_temp_dir(), 'env_test_');
        file_put_contents($tmpEnv, "APP_ENV=local\n");

        $envWriter = new EnvWriter($tmpEnv);
        $controller = new SettingsController;
        $user = User::factory()->create();

        $request = Request::create('/super-admin/settings/system', 'PUT', [
            'app_env' => 'invalid_env',
            'app_url' => 'not-a-url',
            'session_driver' => 'invalid',
            'log_level' => 'invalid',
            'log_channel' => 'invalid',
            'session_encrypt' => 'invalid',
            'session_secure_cookie' => 'invalid',
            'session_same_site' => 'invalid',
        ]);
        $request->setUserResolver(fn () => $user);

        try {
            $controller->updateSystem($request, $envWriter);
            $this->fail('Expected validation exception');
        } catch (ValidationException $e) {
            $this->assertTrue(true);
        }

        @unlink($tmpEnv);
    }

    public function test_system_settings_returns_error_when_env_file_missing(): void
    {
        $envWriter = new EnvWriter('/nonexistent/path/.env');
        $controller = new SettingsController;
        $user = User::factory()->create();

        $request = Request::create('/super-admin/settings/system', 'PUT', [
            'app_env' => 'production',
            'app_url' => 'https://example.com',
            'session_driver' => 'redis',
            'log_level' => 'error',
            'log_channel' => 'single',
            'session_encrypt' => 'true',
            'session_secure_cookie' => 'true',
            'session_same_site' => 'strict',
        ]);
        $request->setUserResolver(fn () => $user);

        $response = $controller->updateSystem($request, $envWriter);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->getSession()->get('errors')->has('system'));
    }
}
