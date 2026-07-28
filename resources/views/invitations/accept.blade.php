@extends('layouts.guest')

@section('title', __('workspace.invitation_title') . ' - ' . config('app.name'))

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="padding:2rem;">
    <div class="card shadow-sm" style="max-width:480px;width:100%;border-radius:16px;border:1px solid var(--border);">
        <div class="card-body p-4" style="text-align:center;">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                     style="width:64px;height:64px;background:rgba(21,183,108,0.1);">
                    <i class="bi bi-mailbox2" style="font-size:28px;color:var(--accent);"></i>
                </div>
                <h4 class="mb-2" style="font-weight:600;">{{ __('workspace.invitation_title') }}</h4>
                <p class="text-muted mb-0" style="font-size:14px;">
                    {{ __('workspace.invitation_description') }}
                </p>
            </div>

            <div class="mb-4 p-3 rounded-3"
                 style="background:var(--bg);border:1px solid var(--border);text-align:start;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($invitation->workspace->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:15px;">{{ $invitation->workspace->name }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">
                            {{ __('workspace.invited_by') }} {{ $invitation->inviter->name }}
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2" style="font-size:13px;color:var(--text-muted);">
                    <i class="bi bi-shield-check"></i>
                    <span>{{ __('workspace.role') }}: <strong>{{ __('workspace.role_' . $invitation->role) }}</strong></span>
                </div>
                <div class="d-flex align-items-center gap-2 mt-1" style="font-size:13px;color:var(--text-muted);">
                    <i class="bi bi-clock"></i>
                    <span>{{ __('workspace.invitation_expires', ['date' => $invitation->expires_at->format('Y-m-d')]) }}</span>
                </div>
            </div>

            <div class="d-grid gap-2">
                <form action="{{ route('invitations.do-accept', $invitation) }}" method="POST">
                    @csrf
                    <x-button submit variant="accent" icon="bi bi-check-lg" block style="padding:12px;font-size:15px;">{{ __('workspace.accept_invitation') }}</x-button>
                </form>
                <form action="{{ route('invitations.do-decline', $invitation) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn w-100"
                            style="padding:12px;font-size:15px;background:transparent;border:1px solid var(--border);color:var(--text-muted);"
                            onclick="return confirm('{{ __('workspace.decline_confirm') }}')">
                        {{ __('workspace.decline_invitation') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
