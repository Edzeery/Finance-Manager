<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public User $user,
    ) {
        $this->locale($user->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.two_factor_code_subject', ['app' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.two-factor-code',
            with: ['code' => $this->code, 'user' => $this->user],
        );
    }
}
