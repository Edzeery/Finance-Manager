<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionDowngraded extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
        public string $oldPlanName,
    ) {
        $this->locale($subscription->user?->locale ?? config('app.fallback_locale'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.subscription_downgraded_subject', ['app' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscription-downgraded',
            with: [
                'subscription' => $this->subscription,
                'oldPlanName' => $this->oldPlanName,
            ],
        );
    }
}
