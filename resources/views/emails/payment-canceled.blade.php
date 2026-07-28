<x-mail::message>
# {{ __('emails.payment_canceled_subject') }}

{{ __('emails.hello') }}

{{ __('emails.payment_canceled_line') }}

<x-mail::panel>
- **{{ __('emails.amount') }}:** {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
- **{{ __('emails.method') }}:** {{ __('onboarding.method_' . ($payment->paymentMethod?->key)) }}
- **{{ __('emails.date') }}:** {{ $payment->created_at->format('Y-m-d H:i') }}
- **{{ __('emails.status') }}:** {{ $payment->status->label() }}
</x-mail::panel>

<x-mail::button :url="route('billing.subscriptions')">
{{ __('emails.retry_payment') }}
</x-mail::button>

{{ __('emails.regards') }}
{{ config('app.name') }}
</x-mail::message>
