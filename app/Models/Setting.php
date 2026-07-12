<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    public const ALLOWED_RUNTIME_KEYS = [
        'app_name',
        'currencies',
        'default_locale',
        'exchange_rates',
        'registration_enabled',
    ];

    public const PROTECTED_RUNTIME_PREFIXES = [
        'system.',
        'app.',
        'session.',
        'logging.',
        'cache.',
        'queue.',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        return $setting && $setting->value !== null && $setting->value !== ''
            ? $setting->value
            : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        if (self::isProtectedRuntimeKey($key)) {
            throw new \RuntimeException("The setting {$key} is protected and cannot be modified at runtime.");
        }

        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * للقيم الحساسة (مفاتيح API، أرقام حسابات...) — تُخزَّن مشفّرة.
     */
    public static function getSecret(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();

        if (!$setting || $setting->value === null || $setting->value === '') {
            return $default;
        }

        try {
            return Crypt::decryptString($setting->value);
        } catch (DecryptException $e) {
            Log::warning('Failed to decrypt secret setting', ['key' => $key, 'exception' => $e->getMessage()]);

            return $default;
        }
    }

    public static function setSecret(string $key, ?string $value): void
    {
        // لو الحقل أُرسل فاضياً، لا نمسح القيمة المخزّنة سابقاً (تجنباً لمسحها بالخطأ)
        if ($value === null || $value === '') {
            return;
        }

        self::updateOrCreate(['key' => $key], ['value' => Crypt::encryptString($value)]);
    }

    public static function forgetSecret(string $key): void
    {
        self::where('key', $key)->delete();
    }

    public static function isAllowedRuntimeKey(string $key): bool
    {
        return in_array($key, self::ALLOWED_RUNTIME_KEYS, true);
    }

    public static function isProtectedRuntimeKey(string $key): bool
    {
        if (self::isAllowedRuntimeKey($key)) {
            return false;
        }

        foreach (self::PROTECTED_RUNTIME_PREFIXES as $prefix) {
            if (str_starts_with($key, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
