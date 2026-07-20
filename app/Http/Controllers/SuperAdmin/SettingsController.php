<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Services\EnvWriter;
use App\Services\Payments\PaymentGatewayRegistry;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    use HasBreadcrumbs;

    public function index()
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.settings'));

        $settings = Setting::pluck('value', 'key')->toArray();
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'timezone' => config('app.timezone'),
            'locale' => app()->getLocale(),
            'session_same_site' => config('session.same_site'),
            'queue_driver' => config('queue.default'),
            'cache_driver' => config('cache.default'),
            'log_channel' => config('logging.default'),
            'app_url' => config('app.url'),
            'system_health' => config('app.debug') ? 'degraded' : 'healthy',
        ];

        if (request()->user()?->hasPermission('system.info.view', 'platform')) {
            $systemInfo['environment'] = config('app.env');
            $systemInfo['debug_mode'] = config('app.debug');
            $systemInfo['session_driver'] = config('session.driver');
        }

        $systemSettings = [
            'app_env' => Setting::get('system.app_env', config('app.env')),
            'app_debug' => Setting::get('system.app_debug', config('app.debug') ? 'true' : 'false'),
            'log_level' => Setting::get('system.log_level', config('logging.level', 'warning')),
            'log_channel' => Setting::get('system.log_channel', config('logging.default', 'daily')),
            'session_driver' => Setting::get('system.session_driver', config('session.driver', 'database')),
            'session_encrypt' => Setting::get('system.session_encrypt', config('session.encrypt') ? 'true' : 'false'),
            'session_secure_cookie' => Setting::get('system.session_secure_cookie', config('session.secure') !== null && config('session.secure') ? 'true' : 'false'),
            'session_same_site' => Setting::get('system.session_same_site', config('session.same_site', 'lax')),
            'app_url' => Setting::get('system.app_url', config('app.url')),
            'registration_enabled' => Setting::get('registration_enabled', '1'),
        ];

        $rateLimits = collect(config('finance.rate_limits', []))->mapWithKeys(fn ($value, $key) => [
            $key => Setting::get("rate_limit.{$key}", $value),
        ])->toArray();

        $user = request()->user();
        $twoFactorEnabled = $user && $user->two_factor_confirmed_at !== null;

        return view('super-admin.settings', $this->withBreadcrumbs(
            compact('settings', 'systemInfo', 'systemSettings', 'twoFactorEnabled', 'rateLimits')
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['nullable', 'string', 'max:255'],
            'base_currency' => ['nullable', 'string', 'size:3', 'alpha:ascii'],
            'registration_enabled' => ['nullable', 'boolean'],
            'default_locale' => ['nullable', 'in:en,ar,fr'],
        ]);

        foreach ($validated as $key => $value) {
            if (! is_null($value)) {
                try {
                    Setting::set($key, $value);
                } catch (\RuntimeException $e) {
                    Log::warning('Blocked attempt to update protected setting', [
                        'key' => $key,
                        'user_id' => $request->user()?->id,
                        'ip' => $request->ip(),
                        'exception' => $e->getMessage(),
                    ]);

                    return redirect()->back()
                        ->withErrors(['settings' => __('messages.settings_update_protected')])
                        ->withInput();
                }
            }
        }

        return redirect()->route('super.admin.settings.index')
            ->with('success', __('messages.settings_saved'));
    }

    public function updateSystem(Request $request, EnvWriter $envWriter)
    {
        $validated = $request->validate([
            'app_env' => ['required', Rule::in(['local', 'production', 'testing', 'staging'])],
            'app_url' => ['required', 'url', 'max:255'],
            'session_driver' => ['required', Rule::in(['database', 'redis', 'file', 'array', 'cookie'])],
            'log_level' => ['required', Rule::in(['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'])],
            'log_channel' => ['required', Rule::in(['single', 'daily', 'slack', 'syslog', 'errorlog', 'stack'])],
            'session_encrypt' => ['required', Rule::in(['true', 'false'])],
            'session_secure_cookie' => ['required', Rule::in(['true', 'false'])],
            'session_same_site' => ['required', Rule::in(['lax', 'strict', 'none'])],
        ]);

        $envMap = [
            'app_env' => 'APP_ENV',
            'app_url' => 'APP_URL',
            'session_driver' => 'SESSION_DRIVER',
            'log_level' => 'LOG_LEVEL',
            'log_channel' => 'LOG_CHANNEL',
            'session_encrypt' => 'SESSION_ENCRYPT',
            'session_secure_cookie' => 'SESSION_SECURE_COOKIE',
            'session_same_site' => 'SESSION_SAME_SITE',
        ];

        $updates = [];
        foreach ($envMap as $field => $envKey) {
            if (isset($validated[$field])) {
                $updates[$envKey] = $validated[$field];
            }
        }

        try {
            $changes = $envWriter->update($updates);
        } catch (\RuntimeException $e) {
            Log::error('Failed to update .env file', [
                'user_id' => $request->user()?->id,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['system' => __('messages.settings_update_failed')])
                ->withInput();
        }

        try {
            Artisan::call('config:clear');
        } catch (\Throwable $e) {
            Log::warning('Failed to clear config cache after .env update', [
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('super.admin.settings.index')
            ->with('success', __('messages.settings_saved'));
    }

    public function updateGateways(Request $request, PaymentGatewayRegistry $registry)
    {
        $rules = [];
        foreach ($registry->all() as $key => $def) {
            foreach ($def->fields as $field) {
                if ($field->key === 'enabled') {
                    continue;
                }
                $ruleKey = "gateways.{$key}.{$field->key}";
                $rules[$ruleKey] = $field->validationRules();
            }
        }

        $rules['gateways.chargily.mode'] = ['nullable', 'string', 'in:test,live'];
        $rules['gateways.baridimob.rip_number'] = ['nullable', 'string', 'max:50'];
        $rules['gateways.baridimob.account_holder_name'] = ['nullable', 'string', 'max:255'];
        $rules['gateways.redotpay.account_id'] = ['nullable', 'string', 'max:255'];
        $rules['gateways.wise_manual.account_email'] = ['nullable', 'email', 'max:255'];
        $rules['gateways.wise_manual.account_holder_name'] = ['nullable', 'string', 'max:255'];
        $rules['gateways.noest.base_url'] = ['nullable', 'string', 'max:255'];
        $rules['gateways.noest.api_token'] = ['nullable', 'string', 'max:500'];
        $rules['gateways.noest.user_guid'] = ['nullable', 'string', 'max:255'];

        $validated = $request->validate($rules);

        foreach ($validated['gateways'] ?? [] as $gateway => $fields) {
            $incoming = array_filter($fields, fn ($v) => $v !== null && $v !== '');

            $definition = $registry->find($gateway);

            PaymentMethod::updateOrCreate(
                ['key' => $gateway],
                [
                    'name' => $definition?->name ?? $gateway,
                    'is_active' => true,
                ]
            );

            $credentials = [];
            foreach ($incoming as $key => $value) {
                $field = $definition?->field($key);
                $credentials[$key] = ($field?->encrypted ?? false) ? encrypt($value) : $value;
            }

            $method = PaymentMethod::where('key', $gateway)->first();
            if ($method) {
                $merged = array_merge($method->credentials ?? [], $credentials);
                $method->update(['credentials' => $merged]);
            }
        }

        return redirect()->route('super.admin.settings.index')
            ->with('success', __('messages.settings_saved'));
    }

    public function updateExchangeRates(Request $request)
    {
        $validated = $request->validate([
            'rates' => ['required', 'array'],
            'rates.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $rates = array_filter($validated['rates'], fn ($v) => $v !== null && $v !== '');

        try {
            Setting::set('exchange_rates', json_encode($rates));
        } catch (\RuntimeException $e) {
            Log::warning('Blocked attempt to update protected exchange rates', [
                'user_id' => $request->user()?->id,
                'ip' => $request->ip(),
                'exception' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['settings' => __('messages.settings_update_protected')])
                ->withInput();
        }

        return redirect()->route('super.admin.settings.index')
            ->with('success', __('messages.settings_saved'));
    }

    public function disable2fa(TwoFactorAuthenticationService $twoFactor)
    {
        $user = request()->user();
        if (! $user || ! $user->two_factor_confirmed_at) {
            return redirect()->route('super.admin.settings.index')
                ->with('error', __('auth.2fa_not_enabled'));
        }

        $twoFactor->disable($user);

        return redirect()->route('super.admin.settings.index')
            ->with('success', __('auth.2fa_disabled_success'));
    }

    public function updateRateLimits(Request $request)
    {
        $keys = array_keys(config('finance.rate_limits', []));

        $validated = $request->validate([
            'rate_limits' => ['required', 'array'],
            'rate_limits.*' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        foreach ($validated['rate_limits'] as $key => $value) {
            if (in_array($key, $keys)) {
                Setting::set("rate_limit.{$key}", (string) $value);
            }
        }

        return redirect()->route('super.admin.settings.index')
            ->with('success', __('messages.settings_saved'));
    }

    public function updateZakatPrices(Request $request)
    {
        $validated = $request->validate([
            'zakat_manual_override' => ['nullable', 'boolean'],
            'zakat_gold_per_gram' => ['nullable', 'numeric', 'min:0'],
            'zakat_silver_per_gram' => ['nullable', 'numeric', 'min:0'],
            'zakat_gold_karat' => ['nullable', 'integer', 'in:24,22,21,18,14,10'],
        ]);

        Setting::set('zakat.manual_override', ($validated['zakat_manual_override'] ?? false) ? '1' : '0');
        Setting::set('zakat.gold_per_gram', (string) ($validated['zakat_gold_per_gram'] ?? 0));
        Setting::set('zakat.silver_per_gram', (string) ($validated['zakat_silver_per_gram'] ?? 0));
        Setting::set('zakat.default_karat', (string) ($validated['zakat_gold_karat'] ?? 24));

        \Illuminate\Support\Facades\Cache::forget('gold_24k_gram_usd');
        \Illuminate\Support\Facades\Cache::forget('silver_gram_usd');

        return redirect()->route('super.admin.settings.index')
            ->with('success', __('messages.settings_saved'));
    }

    public function updateCurrencies(Request $request)
    {
        $validated = $request->validate([
            'currencies' => ['required', 'array', 'min:1'],
            'currencies.*.code' => ['required', 'string', 'size:3', 'alpha:ascii'],
            'currencies.*.name' => ['required', 'string', 'max:255'],
            'currencies.*.symbol' => ['required', 'string', 'max:10'],
        ]);

        $codes = array_column($validated['currencies'], 'code');

        if (count($codes) !== count(array_unique($codes))) {
            return redirect()->back()
                ->withErrors(['currencies' => __('super-admin.currency_duplicate_code')])
                ->withInput();
        }

        $usedCodes = PaymentGateway::whereNotNull('supported_currencies')
            ->pluck('supported_currencies')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        $removed = array_diff($usedCodes, $codes);

        foreach ($removed as $code) {
            $gateways = PaymentGateway::whereJsonContains('supported_currencies', $code)
                ->pluck('key')
                ->toArray();

            if (! empty($gateways)) {
                return redirect()->back()
                    ->withErrors(['currencies' => __('super-admin.currency_in_use_by_gateways', [
                        'currency' => $code,
                        'gateways' => implode(', ', $gateways),
                    ])])
                    ->withInput();
            }
        }

        try {
            Setting::set('currencies', json_encode($validated['currencies']));
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withErrors(['currencies' => $e->getMessage()])
                ->withInput();
        }

        return redirect()->route('super.admin.settings.index')
            ->with('success', __('super-admin.currencies_saved'));
    }
}
