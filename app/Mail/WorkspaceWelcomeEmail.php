<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkspaceWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Workspace $workspace,
        public string $role,
    ) {
        $this->locale($user->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.workspace_welcome_subject', [
                'workspace' => $this->workspace->name,
                'app' => config('app.name'),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.workspace-welcome',
            with: [
                'user' => $this->user,
                'workspace' => $this->workspace,
                'role' => __('workspace.role_'.$this->role),
            ],
        );
    }
}
