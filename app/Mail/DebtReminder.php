<?php

namespace App\Mail;

use App\Models\Debt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DebtReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Debt $debt)
    {
        $this->locale($debt->user?->locale ?? config('app.fallback_locale'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.debt_reminder_subject_with_name', ['name' => $this->debt->counterparty_name]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.debt-reminder',
            with: ['debt' => $this->debt],
        );
    }
}
