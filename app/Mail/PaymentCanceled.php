<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentCanceled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment)
    {
        $this->locale($payment->user?->locale ?? config('app.fallback_locale'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.payment_canceled_subject', ['app' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-canceled',
            with: ['payment' => $this->payment],
        );
    }
}
