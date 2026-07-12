<x-mail::message>
# {{ __('emails.payment_receipt_subject') }}

{{ __('emails.hello') }}

{{ __('emails.payment_receipt_line') }}

<x-mail::panel>
- **{{ __('emails.amount') }}:** {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
- **{{ __('emails.method') }}:** {{ __('onboarding.method_' . $payment->method) }}
- **{{ __('emails.reference') }}:** {{ $payment->reference ?? __('emails.na') }}
- **{{ __('super-admin.payment_id') }}:** {{ $payment->uuid ?? __('emails.na') }}
- **{{ __('emails.date') }}:** {{ $payment->paid_at?->format('Y-m-d H:i') ?? $payment->created_at->format('Y-m-d H:i') }}
- **{{ __('emails.status') }}:** {{ $payment->status->label() }}
</x-mail::panel>

{{ __('emails.payment_receipt_line') }}

{{ __('emails.regards') }}
{{ config('app.name') }}
</x-mail::message>
