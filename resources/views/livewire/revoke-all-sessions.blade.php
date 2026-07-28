<div>
    <x-button variant="danger" size="sm" wire-click="openModal" icon="bi bi-box-arrow-right">{{ __('settings.revoke_all_others') }}</x-button>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);" wire:click.self="closeModal">
            <div class="modal-dialog modal-dialog-centered" wire:click.stop>
                <div class="modal-content" style="background:var(--bg);border:1px solid var(--border-color);border-radius:var(--radius-lg,12px);box-shadow:0 20px 60px rgba(0,0,0,0.3);">
                    <div class="modal-header" style="border-bottom:1px solid var(--border-color);padding:16px 20px;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                style="width:40px;height:40px;background:rgba(239,68,68,0.1);flex-shrink:0;">
                                <i class="bi bi-exclamation-triangle" style="color:var(--danger);font-size:18px;"></i>
                            </div>
                            <h5 class="mb-0" style="font-weight:600;font-size:16px;">{{ __('settings.revoke_all_others') }}</h5>
                        </div>
                        <button type="button" wire:click="closeModal" style="background:none;border:none;font-size:20px;color:var(--text-muted);cursor:pointer;padding:0;line-height:1;">&times;</button>
                    </div>
                    <div class="modal-body" style="padding:20px;">
                        <p style="font-size:14px;color:var(--text-muted);margin-bottom:16px;">
                            {{ __('settings.confirm_revoke_all') }}
                        </p>

                        <form wire:submit="confirmRevokeAll">
                            <div class="mb-3">
                                <label for="revokePassword" class="form-label-custom" style="font-size:13px;font-weight:500;">
                                    {{ __('general.password') }}
                                </label>
                                <x-password-input wire:model="password" id="revokePassword" name="password"
                                    autocomplete="current-password" error="password" />
                                @error('password')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            @if ($hasTwoFactor)
                                <div class="mb-3">
                                    <label for="revoke2faCode" class="form-label-custom" style="font-size:13px;font-weight:500;">
                                        {{ __('general.verification_code') }}
                                    </label>
                                    <div class="input-icon-wrap">
                                        <i class="bi bi-grid-3x2-gap input-icon-left"></i>
                                        <input wire:model="twoFactorCode" id="revoke2faCode" type="text"
                                            inputmode="numeric" autocomplete="one-time-code"
                                            class="form-custom has-icon-left text-center tracking-wide @error('twoFactorCode') is-invalid @enderror"
                                            placeholder="000 000" maxlength="6"
                                            style="letter-spacing:.3em;font-size:1.15rem;font-weight:600;" />
                                    </div>
                                    @error('twoFactorCode')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <x-button size="sm" wire-click="closeModal" style="padding:8px 16px;font-size:13px;border:1px solid var(--border-color);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);cursor:pointer;">{{ __('general.cancel') }}</x-button>
                                <x-button submit size="sm" variant="danger" icon="bi bi-box-arrow-right" wire-target="confirmRevokeAll" style="padding:8px 16px;font-size:13px;border:none;border-radius:var(--radius-sm);cursor:pointer;">{{ __('settings.revoke_all_others') }}</x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
