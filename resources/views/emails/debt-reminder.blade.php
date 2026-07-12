<x-mail::message>
# {{ __('emails.debt_reminder_subject') }}

{{ __('emails.hello') }}

{{ __('emails.debt_reminder_line') }}

<x-mail::panel>
- **{{ __('emails.counterparty') }}:** {{ $debt->counterparty_name }}
- **{{ __('emails.total_amount') }}:** {{ number_format($debt->total_amount, 2) }}
- **{{ __('emails.paid_amount') }}:** {{ number_format($debt->paid_amount, 2) }}
- **{{ __('emails.remaining') }}:** {{ number_format($debt->total_amount - $debt->paid_amount, 2) }}
- **{{ __('emails.due_date') }}:** {{ $debt->due_date->format('Y-m-d') }}
- **{{ __('emails.type') }}:** {{ ucfirst($debt->type) }}
</x-mail::panel>

<x-mail::button :url="route('login')">
{{ __('emails.view_details') }}
</x-mail::button>

{{ __('emails.debt_reminder_line') }}

{{ __('emails.regards') }}
{{ config('app.name') }}
</x-mail::message>
