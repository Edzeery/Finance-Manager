<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $appName = Setting::get('app_name');
            if ($appName) {
                config()->set('app.name', $appName);
            }

            $defaultLocale = Setting::get('default_locale');
            if ($defaultLocale) {
                config()->set('app.locale', $defaultLocale);
            }

            $registrationEnabled = Setting::get('registration_enabled');
            if ($registrationEnabled !== null) {
                config()->set('app.registration_enabled', $registrationEnabled === '1' || $registrationEnabled === 'true');
            }

            $baseCurrency = Setting::get('base_currency');
            if ($baseCurrency) {
                config()->set('finance.base_currency', strtoupper($baseCurrency));
            }

            $rateLimitKeys = array_keys(config('finance.rate_limits', []));
            foreach ($rateLimitKeys as $key) {
                $dbValue = Setting::get("rate_limit.{$key}");
                if ($dbValue !== null && is_numeric($dbValue)) {
                    config()->set("finance.rate_limits.{$key}", (int) $dbValue);
                }
            }

            $systemConfigMap = [
                'system.app_env' => 'app.env',
                'system.app_debug' => 'app.debug',
                'system.app_url' => 'app.url',
                'system.log_level' => 'logging.level',
                'system.log_channel' => 'logging.default',
                'system.session_driver' => 'session.driver',
                'system.session_encrypt' => 'session.encrypt',
                'system.session_secure_cookie' => 'session.secure',
                'system.session_same_site' => 'session.same_site',
            ];

            $booleanKeys = ['system.app_debug', 'system.session_encrypt', 'system.session_secure_cookie'];

            foreach ($systemConfigMap as $dbKey => $configKey) {
                $value = Setting::get($dbKey);
                if ($value !== null) {
                    if (in_array($dbKey, $booleanKeys, true)) {
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    }
                    config()->set($configKey, $value);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('SettingsServiceProvider: failed to load dynamic settings', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
