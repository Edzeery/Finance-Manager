<?php

namespace App\Livewire;

use App\Services\NotificationService;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RevokeAllSessions extends Component
{
    public bool $showModal = false;

    public string $password = '';

    public string $twoFactorCode = '';

    public bool $hasTwoFactor = false;

    public function mount(): void
    {
        $this->hasTwoFactor = Auth::user()->hasTwoFactorEnabled();
    }

    public function openModal(): void
    {
        $this->showModal = true;
        $this->password = '';
        $this->twoFactorCode = '';
        $this->hasTwoFactor = Auth::user()->hasTwoFactorEnabled();
        $this->resetErrorBag();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->password = '';
        $this->twoFactorCode = '';
        $this->resetErrorBag();
    }

    public function confirmRevokeAll(TwoFactorAuthenticationService $twoFactor, NotificationService $notificationService): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (! Auth::guard('web')->validate([
            'email' => $user->email,
            'password' => $this->password,
        ])) {
            $this->addError('password', __('auth.password'));

            return;
        }

        if ($user->hasTwoFactorEnabled()) {
            $this->validate([
                'twoFactorCode' => ['required', 'string', 'size:6'],
            ]);

            if ($user->hasTwoFactorMethod(TwoFactorAuthenticationService::METHOD_APP)) {
                $secret = $user->getGoogle2faSecret();
                if (! $secret || ! $twoFactor->verify($secret, $this->twoFactorCode)) {
                    $this->addError('twoFactorCode', __('auth.invalid_2fa_code'));

                    return;
                }
            } elseif ($user->hasTwoFactorMethod(TwoFactorAuthenticationService::METHOD_EMAIL)) {
                if (! $twoFactor->verifyEmailCode($user, $this->twoFactorCode)) {
                    $this->addError('twoFactorCode', __('auth.invalid_2fa_code'));

                    return;
                }
            }
        }

        $currentSessionId = session()->getId();

        $revokedCount = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        $notificationService->sessionRevoked(
            $user->id,
            __('notifications.all_sessions_revoked_count', ['count' => $revokedCount])
        );

        $this->showModal = false;
        $this->password = '';
        $this->twoFactorCode = '';

        session()->flash('success', __('settings.all_sessions_revoked'));

        $this->dispatch('refreshSessions');
    }

    public function render()
    {
        return view('livewire.revoke-all-sessions');
    }
}
