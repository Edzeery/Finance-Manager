<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $title,
        public string $message,
        public string $icon = 'bi-bell',
        public string $color = '#15b76c',
    ) {
        $this->locale($user->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name').' — '.$this->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notification',
            with: [
                'user' => $this->user,
                'title' => $this->title,
                'message' => $this->message,
                'icon' => $this->icon,
                'color' => $this->color,
            ],
        );
    }
}
