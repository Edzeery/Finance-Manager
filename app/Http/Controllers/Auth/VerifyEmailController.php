<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Mail\WorkspaceWelcomeEmail;
use App\Models\Invitation;
use App\Services\WorkspaceInvitationService;
use App\Services\WorkspaceService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class VerifyEmailController extends Controller
{
    public function __construct(
        private WorkspaceInvitationService $invitationService,
        private WorkspaceService $workspaceService,
    ) {}

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $token = session('invitation_token');
        $invitation = $token ? $this->invitationService->validateToken($token) : null;

        if ($user->hasVerifiedEmail()) {
            if ($invitation) {
                session()->forget('invitation_token');
                $this->autoAcceptInvitation($invitation, $user);

                return redirect()->route('dashboard')
                    ->with('success', __('workspace.invitation_accepted_msg', [
                        'workspace' => $invitation->workspace->name,
                    ]));
            }

            return redirect()->route('dashboard')
                ->with('success', __('onboarding.email_verified'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            if ($invitation) {
                session()->forget('invitation_token');
                $this->autoAcceptInvitation($invitation, $user);

                return redirect()->route('dashboard')
                    ->with('success', __('workspace.invitation_accepted_msg', [
                        'workspace' => $invitation->workspace->name,
                    ]));
            }

            Mail::to($user)->queue(new WelcomeEmail($user));
        }

        return redirect()->route('dashboard')
            ->with('success', __('onboarding.email_verified'));
    }

    private function autoAcceptInvitation(Invitation $invitation, $user): void
    {
        $this->invitationService->accept($invitation, $user);

        if (! $user->current_workspace_id) {
            $user->update(['current_workspace_id' => $invitation->workspace_id]);
        }

        if (! $user->hasCompletedOnboarding()) {
            $user->markOnboardingComplete();
        }

        $workspace = $invitation->workspace;

        Mail::to($user)->queue(
            new WorkspaceWelcomeEmail($user, $workspace, $invitation->role)
        );
    }
}
