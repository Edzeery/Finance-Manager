<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <x-button variant="outline-danger" class="px-4" data-bs-toggle="modal" data-bs-target="#deleteAccountModal" icon="bi bi-trashms-1">{{ __('general.delete_account') }}</x-button>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:var(--card-bg); border:1px solid var(--border); border-radius:12px">
                <div class="modal-body p-4">
                    <h5 class="fw-bold mb-2" style="color:var(--text)">{{ __('profile.delete_confirm') }}</h5>
                    <p class="mb-4" style="font-size:13px; color:var(--text-muted)">{{ __('profile.delete_confirm_help') }}</p>

                    <form wire:submit="deleteUser">
                        <div class="mb-3">
                            <x-password-input wire:model="password" name="password" placeholder="{{ __('general.password') }}"
                                autocomplete="current-password" error="password" />
                            @error('password') <div class="text-danger mt-1" style="font-size:12px">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-flex gap-3 justify-content-end">
                            <x-button variant="outline" icon="bi bi-x-lg" data-bs-dismiss="modal">{{ __('general.cancel') }}</x-button>
                            <x-button submit variant="danger" icon="bi bi-trashms-1">{{ __('general.delete_account') }}</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
