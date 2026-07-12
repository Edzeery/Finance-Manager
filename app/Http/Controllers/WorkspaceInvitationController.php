<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Services\WorkspaceInvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class WorkspaceInvitationController extends Controller
{
    public function __construct(
        private WorkspaceInvitationService $invitationService,
    ) {}

    public function decline(string $token)
    {
        $invitation = $this->invitationService->validateToken($token);

        if (!$invitation) {
            return redirect()->route('login')
                ->with('error', __('workspace.invitation_invalid_or_expired'));
        }

        return redirect()->route('invitations.accept', $token)
            ->with('info', __('workspace.invitation_decline_info'));
    }

    public function accept(string $token)
    {
        $invitation = $this->invitationService->validateToken($token);

        if (!$invitation) {
            return redirect()->route('login')
                ->with('error', __('workspace.invitation_invalid_or_expired'));
        }

        if (!auth()->check()) {
            session(['invitation_token' => $token]);
            return redirect()->route('login')
                ->with('info', __('workspace.invitation_login_required'));
        }

        $user = auth()->user();

        if (strtolower($invitation->email) !== strtolower($user->email)) {
            return redirect()->route('dashboard')
                ->with('error', __('workspace.invitation_email_mismatch'));
        }

        return view('invitations.accept', [
            'invitation' => $invitation->load('workspace', 'inviter'),
        ]);
    }

    public function doAccept(Request $request, Invitation $invitation)
    {
        $this->ensureInvitationAccess($invitation);

        $key = 'accept-invitation-' . $invitation->id . '-' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->with('error', __('messages.throttle'));
        }
        RateLimiter::hit($key, 60);

        try {
            $this->invitationService->accept($invitation, auth()->user());

            if (!auth()->user()->current_workspace_id) {
                auth()->user()->update(['current_workspace_id' => $invitation->workspace_id]);
            }

            return redirect()->route('dashboard')
                ->with('success', __('workspace.invitation_accepted_msg', [
                    'workspace' => $invitation->workspace->name,
                ]));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function doDecline(Request $request, Invitation $invitation)
    {
        $this->ensureInvitationAccess($invitation);

        $key = 'decline-invitation-' . $invitation->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->with('error', __('messages.throttle'));
        }
        RateLimiter::hit($key, 60);

        try {
            $this->invitationService->decline($invitation);

            return redirect()->route('dashboard')
                ->with('success', __('workspace.invitation_declined_msg'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, Invitation $invitation)
    {
        try {
            $this->invitationService->cancel($invitation, auth()->user());

            return redirect()->route('settings.index')
                ->with('success', __('workspace.invitation_cancelled_msg'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resend(Request $request, Invitation $invitation)
    {
        if ($invitation->inviter_id !== auth()->id()) {
            return back()->with('error', __('messages.unauthorized'));
        }

        try {
            $this->invitationService->resend($invitation);

            return redirect()->route('settings.index')
                ->with('success', __('workspace.invitation_resent_msg'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function ensureInvitationAccess(Invitation $invitation): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        if (strtolower($invitation->email) !== strtolower($user->email)) {
            abort(403, __('workspace.invitation_email_mismatch'));
        }
    }
}
