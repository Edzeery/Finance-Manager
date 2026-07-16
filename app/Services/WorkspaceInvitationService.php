<?php

namespace App\Services;

use App\Contracts\Services\ActivityLogServiceInterface;
use App\Enums\InvitationStatus;
use App\Events\InvitationAccepted;
use App\Events\InvitationCreated;
use App\Events\InvitationDeclined;
use App\Events\InvitationExpired;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Workspace;
use App\Notifications\WorkspaceInvitation as WorkspaceInvitationNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class WorkspaceInvitationService
{
    public function __construct(
        private WorkspaceService $workspaceService,
        private ActivityLogServiceInterface $activityLogService,
    ) {}

    public function invite(Workspace $workspace, User $inviter, string $email, string $role): Invitation
    {
        if (! $workspace->canAddUser()) {
            throw new \RuntimeException(__('workspace.user_limit_reached'));
        }

        $email = strtolower(trim($email));

        $existingUser = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($existingUser && $workspace->users()->where('user_id', $existingUser->id)->exists()) {
            throw new \RuntimeException(__('workspace.user_already_member'));
        }

        $pending = Invitation::pending()->forEmail($email)
            ->where('workspace_id', $workspace->id)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($pending) {
            throw new \RuntimeException(__('workspace.invitation_already_pending'));
        }

        $invitation = DB::transaction(function () use ($workspace, $inviter, $email, $role) {
            $invitation = Invitation::create([
                'workspace_id' => $workspace->id,
                'inviter_id' => $inviter->id,
                'email' => $email,
                'role' => $role,
                'token' => Invitation::generateToken(),
                'status' => InvitationStatus::Pending,
                'expires_at' => Invitation::defaultExpiry(),
            ]);

            InvitationCreated::dispatch($invitation);

            $this->activityLogService->log(
                $inviter->id,
                'invitation_sent',
                $invitation,
                "Invitation sent to {$email} for workspace {$workspace->name}",
                ['workspace_id' => $workspace->id, 'email' => $email, 'role' => $role]
            );

            return $invitation;
        });

        $this->sendNotification($invitation);

        return $invitation;
    }

    public function accept(Invitation $invitation, User $user): void
    {
        if (! $invitation->isAcceptable()) {
            throw new \RuntimeException(__('workspace.invitation_invalid_or_expired'));
        }

        if (strtolower($invitation->email) !== strtolower($user->email)) {
            throw new \RuntimeException(__('workspace.invitation_email_mismatch'));
        }

        $workspace = $invitation->workspace;

        if (! $workspace->canAddUser()) {
            throw new \RuntimeException(__('workspace.user_limit_reached'));
        }

        DB::transaction(function () use ($invitation, $user, $workspace) {
            $invitation->markAsAccepted();

            if (! $workspace->users()->where('user_id', $user->id)->exists()) {
                $workspace->users()->attach($user->id, []);
            }

            $this->workspaceService->syncWorkspaceRoleUser($workspace, $user, $invitation->role);

            $user->flushPermissionCache();

            InvitationAccepted::dispatch($invitation);

            $this->activityLogService->log(
                $user->id,
                'invitation_accepted',
                $invitation,
                "User {$user->email} accepted invitation to workspace {$workspace->name}",
                ['workspace_id' => $workspace->id, 'email' => $user->email, 'role' => $invitation->role]
            );
        });
    }

    public function decline(Invitation $invitation): void
    {
        if (! $invitation->isPending()) {
            throw new \RuntimeException(__('workspace.invitation_invalid_or_expired'));
        }

        DB::transaction(function () use ($invitation) {
            $invitation->markAsDeclined();

            InvitationDeclined::dispatch($invitation);

            $this->activityLogService->log(
                $invitation->inviter_id,
                'invitation_declined',
                $invitation,
                "Invitation declined by {$invitation->email} for workspace {$invitation->workspace->name}",
                ['workspace_id' => $invitation->workspace_id, 'email' => $invitation->email]
            );
        });
    }

    public function cancel(Invitation $invitation, User $user): void
    {
        if ($invitation->inviter_id !== $user->id) {
            throw new \RuntimeException(__('messages.unauthorized'));
        }

        if (! $invitation->isPending()) {
            throw new \RuntimeException(__('workspace.invitation_not_pending'));
        }

        DB::transaction(function () use ($invitation) {
            $invitation->markAsCancelled();

            $this->activityLogService->log(
                $invitation->inviter_id,
                'invitation_cancelled',
                $invitation,
                "Invitation cancelled for {$invitation->email}",
                ['workspace_id' => $invitation->workspace_id, 'email' => $invitation->email]
            );
        });
    }

    public function resend(Invitation $invitation): void
    {
        if (! $invitation->isPending()) {
            throw new \RuntimeException(__('workspace.invitation_not_pending'));
        }

        $invitation->update([
            'token' => Invitation::generateToken(),
            'expires_at' => Invitation::defaultExpiry(),
        ]);

        $this->sendNotification($invitation);

        $this->activityLogService->log(
            $invitation->inviter_id,
            'invitation_resent',
            $invitation,
            "Invitation resent to {$invitation->email}",
            ['workspace_id' => $invitation->workspace_id, 'email' => $invitation->email]
        );
    }

    public function validateToken(string $token): ?Invitation
    {
        $invitation = Invitation::where('token', $token)->first();

        if (! $invitation) {
            return null;
        }

        if ($invitation->isExpired() && $invitation->isPending()) {
            $invitation->markAsExpired();
            InvitationExpired::dispatch($invitation);

            return null;
        }

        if (! $invitation->isAcceptable()) {
            return null;
        }

        return $invitation;
    }

    public function getPendingForWorkspace(Workspace $workspace): iterable
    {
        return Invitation::pending()
            ->where('workspace_id', $workspace->id)
            ->where('expires_at', '>', Carbon::now())
            ->with('inviter')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function expireOverdue(): int
    {
        $count = 0;
        Invitation::pending()
            ->where('expires_at', '<=', Carbon::now())
            ->chunk(100, function ($invitations) use (&$count) {
                foreach ($invitations as $invitation) {
                    $invitation->markAsExpired();
                    InvitationExpired::dispatch($invitation);
                    $count++;
                }
            });

        return $count;
    }

    private function sendNotification(Invitation $invitation): void
    {
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($invitation->email)])->first();

        if ($user) {
            $user->notify(new WorkspaceInvitationNotification($invitation));
        } else {
            Notification::route('mail', $invitation->email)
                ->notify(new WorkspaceInvitationNotification($invitation));
        }
    }
}
