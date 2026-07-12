<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkspaceInvitation extends Notification
{
    use Queueable;

    public function __construct(
        public Invitation $invitation,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $workspace = $this->invitation->workspace;
        $inviter = $this->invitation->inviter;
        $acceptUrl = route('invitations.accept', $this->invitation->token);
        $declineUrl = route('invitations.decline', $this->invitation->token);

        return (new MailMessage)
            ->locale($notifiable->locale ?? config('app.fallback_locale'))
            ->subject(__('workspace.invitation_email_subject', ['workspace' => $workspace->name]))
            ->greeting(__('workspace.invitation_email_greeting', ['name' => $notifiable->name ?? $this->invitation->email]))
            ->line(__('workspace.invitation_email_intro', [
                'inviter' => $inviter->name,
                'workspace' => $workspace->name,
            ]))
            ->line(__('workspace.invitation_email_role', [
                'role' => __('workspace.role_' . $this->invitation->role),
            ]))
            ->action(__('workspace.invitation_email_accept'), $acceptUrl)
            ->line(__('workspace.invitation_email_expiry', [
                'date' => $this->invitation->expires_at->format('Y-m-d'),
            ]))
            ->line(__('workspace.invitation_email_decline', ['url' => $declineUrl]))
            ->line(__('workspace.invitation_email_footer'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'workspace_id' => $this->invitation->workspace_id,
            'inviter_id' => $this->invitation->inviter_id,
            'role' => $this->invitation->role,
            'expires_at' => $this->invitation->expires_at->toDateTimeString(),
        ];
    }
}
