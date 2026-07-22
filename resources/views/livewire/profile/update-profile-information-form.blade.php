<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<div>
    @if (session('status') === 'verification-link-sent')
        <div class="alert mb-3 animate-fade-in" style="background:rgba(34,197,94,0.12); color:var(--success); border:none; border-radius:8px; font-size:13px; padding:10px 14px">
            <i class="bi bi-check-circlems-1"></i>{{ __('messages.verification_sent') }}
        </div>
    @endif

    <form wire:submit="updateProfileInformation">
        <div class="mb-3">
            <label class="form-label-custom">{{ __('general.name') }}</label>
            <input wire:model="name" type="text" class="form-custom @error('name') is-invalid @enderror" required autofocus>
            @error('name') <div class="text-danger mt-1" style="font-size:12px">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label-custom">{{ __('general.email') }}</label>
            <input wire:model="email" type="email" class="form-custom @error('email') is-invalid @enderror" required autocomplete="username">
            @error('email') <div class="text-danger mt-1" style="font-size:12px">{{ $message }}</div> @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-2">
                    <span style="font-size:13px; color:var(--text-muted)">{{ __('general.email_unverified') }}</span>
                    <button wire:click.prevent="sendVerification" type="button" class="btn btn-sm p-0 ms-1" style="color:var(--accent); text-decoration:underline; font-size:13px; background:none; border:none; vertical-align:baseline">
                        {{ __('general.resend_verification') }}
                    </button>
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-accent btn-custom"><i class="bi bi-check-lg ms-1"></i>{{ __('general.save') }}</button>
            <div wire:loading wire:target="updateProfileInformation" class="spinner-border spinner-border-sm" role="status" style="color:var(--accent)"></div>
            <span wire:loading.remove wire:target="updateProfileInformation" wire:transition
                  x-data="{ show: false }"
                  x-on:profile-updated.window="show = true; setTimeout(() => show = false, 2000)"
                  x-show="show" style="display:none; font-size:13px; color:var(--success)">
                <i class="bi bi-check-circlems-1"></i>{{ __('general.saved') }}
            </span>
        </div>
    </form>
</div>
