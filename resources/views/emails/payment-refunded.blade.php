<x-mail::message>
# {{ __('emails.payment_refunded_subject') }}

{{ __('emails.hello') }}

{{ __('emails.payment_refunded_line') }}

<x-mail::panel>
- **{{ __('emails.amount') }}:** {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
- **{{ __('super-admin.refunded_amount') }}:** {{ number_format($payment->refund_amount ?? $payment->amount, 2) }} {{ $payment->currency }}
- **{{ __('emails.method') }}:** {{ __('onboarding.method_' . ($payment->paymentMethod?->key)) }}
- **{{ __('super-admin.refund_reason') }}:** {{ $payment->refund_reason ?? __('general.na') }}
- **{{ __('emails.date') }}:** {{ $payment->created_at->format('Y-m-d H:i') }}
</x-mail::panel>

<x-mail::button :url="route('account.payments')">
{{ __('settings.view_all_payments') }}
</x-mail::button>

{{ __('emails.regards') }}
{{ config('app.name') }}
</x-mail::message>
