<?php

namespace App\Services;

use App\Contracts\Services\ActivityLogServiceInterface;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FALaravel\Google2FA;

class TwoFactorAuthenticationService
{
    public const METHOD_APP = 'app';

    public const METHOD_EMAIL = 'email';

    public function __construct(
        private readonly Google2FA $google2fa,
        private readonly ActivityLogServiceInterface $activityLog,
    ) {}

    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function getQrCodeInline(User $user, string $secret): string
    {
        $qrCode = $this->google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        if (! str_starts_with($qrCode, 'data:')) {
            $qrCode = 'data:image/svg+xml;base64,'.base64_encode($qrCode);
        }

        return $qrCode;
    }

    public function verify(string $secret, string $oneTimePassword): bool
    {
        return $this->google2fa->verifyKey($secret, $oneTimePassword);
    }

    public function generateEmailCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function sendEmailCode(User $user): string
    {
        $code = $this->generateEmailCode();

        $user->forceFill([
            'two_factor_email_code' => $code,
            'two_factor_email_code_at' => now(),
        ])->save();

        Mail::to($user)->send(new TwoFactorCodeMail($code, $user));

        return $code;
    }

    public function verifyEmailCode(User $user, string $code): bool
    {
        if (! $user->two_factor_email_code || ! $user->two_factor_email_code_at) {
            return false;
        }

        if (now()->diffInMinutes($user->two_factor_email_code_at) > 10) {
            return false;
        }

        if (! hash_equals($user->two_factor_email_code, $code)) {
            return false;
        }

        $user->forceFill([
            'two_factor_email_code' => null,
            'two_factor_email_code_at' => null,
        ])->save();

        return true;
    }

    public function enable(User $user, ?string $method = null): void
    {
        $methods = $user->two_factor_methods ?? [];
        $method = $method ?? self::METHOD_APP;

        if (! in_array($method, $methods)) {
            $methods[] = $method;
        }

        $user->forceFill([
            'two_factor_confirmed_at' => $user->two_factor_confirmed_at ?? now(),
            'two_factor_methods' => $methods,
        ])->save();

        $this->activityLog->log(
            $user->id,
            'two_factor_enabled',
            $user,
            __('auth.2fa_enabled_log'),
        );
    }

    public function disable(User $user, ?string $method = null): void
    {
        if ($method) {
            $methods = array_filter($user->two_factor_methods ?? [], fn ($m) => $m !== $method);
            $user->forceFill(['two_factor_methods' => array_values($methods)])->save();

            if ($method === self::METHOD_EMAIL) {
                $user->forceFill([
                    'two_factor_email_code' => null,
                    'two_factor_email_code_at' => null,
                ])->save();
            }
            if ($method === self::METHOD_APP) {
                $user->forceFill(['google2fa_secret' => null])->save();
            }

            if (empty($methods)) {
                $user->forceFill([
                    'two_factor_recovery_codes' => null,
                    'two_factor_confirmed_at' => null,
                ])->save();
            }

            $this->activityLog->log(
                $user->id,
                'two_factor_disabled',
                $user,
                __('auth.2fa_disabled_log'),
            );

            return;
        }

        $user->forceFill([
            'google2fa_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_methods' => null,
            'two_factor_email_code' => null,
            'two_factor_email_code_at' => null,
        ])->save();

        $this->activityLog->log(
            $user->id,
            'two_factor_disabled',
            $user,
            __('auth.2fa_disabled_log'),
        );
    }

    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(implode('-', [
                substr(bin2hex(random_bytes(3)), 0, 6),
                substr(bin2hex(random_bytes(3)), 0, 6),
            ]));
        }

        return $codes;
    }

    public function verifyRecoveryCode(User $user, string $code): bool
    {
        $codes = $user->two_factor_recovery_codes ?? [];

        $index = array_search($code, $codes);

        if ($index === false) {
            return false;
        }

        unset($codes[$index]);
        $user->forceFill([
            'two_factor_recovery_codes' => array_values($codes),
        ])->save();

        return true;
    }
}
