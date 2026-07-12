<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment)
    {
        $this->locale($payment->user?->locale ?? config('app.fallback_locale'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.payment_receipt_subject_with_ref', ['ref' => $this->payment->reference ?? $this->payment->id]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-receipt',
            with: ['payment' => $this->payment],
        );
    }
}
