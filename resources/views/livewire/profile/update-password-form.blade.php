<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<div>
    <form wire:submit="updatePassword">
        <div class="mb-3">
            <label class="form-label-custom">{{ __('general.current_password') }}</label>
            <x-password-input name="current_password" wire:model="current_password" autocomplete="current-password"
                error="current_password" />
            @error('current_password') <div class="text-danger mt-1" style="font-size:12px">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label-custom">{{ __('general.new_password') }}</label>
            <x-password-input name="password" wire:model="password" autocomplete="new-password"
                error="password" />
            @error('password') <div class="text-danger mt-1" style="font-size:12px">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label-custom">{{ __('general.confirm_password') }}</label>
            <x-password-input name="password_confirmation" wire:model="password_confirmation" autocomplete="new-password" />
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-accent btn-custom"><i class="bi bi-check-lgms-1"></i>{{ __('general.save') }}</button>
            <div wire:loading wire:target="updatePassword" class="spinner-border spinner-border-sm" role="status" style="color:var(--accent)"></div>
            <span wire:loading.remove wire:target="updatePassword" wire:transition
                  x-data="{ show: false }"
                  x-on:password-updated.window="show = true; setTimeout(() => show = false, 2000)"
                  x-show="show" style="display:none; font-size:13px; color:var(--success)">
                <i class="bi bi-check-circlems-1"></i>{{ __('general.saved') }}
            </span>
        </div>
    </form>
</div>
