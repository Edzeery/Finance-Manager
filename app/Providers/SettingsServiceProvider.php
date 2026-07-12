<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            if (!Schema::hasTable('settings')) {
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

            // Load rate limits from database into config
            $rateLimitKeys = array_keys(config('finance.rate_limits', []));
            foreach ($rateLimitKeys as $key) {
                $dbValue = Setting::get("rate_limit.{$key}");
                if ($dbValue !== null && is_numeric($dbValue)) {
                    config()->set("finance.rate_limits.{$key}", (int) $dbValue);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('SettingsServiceProvider: failed to load dynamic settings', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
