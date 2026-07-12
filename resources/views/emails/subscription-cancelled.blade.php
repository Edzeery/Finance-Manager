<x-mail::message>
# {{ __('emails.subscription_cancelled_subject') }}

{{ __('emails.hello') }}

{{ __('emails.subscription_cancelled_line', ['plan' => $subscription->plan->name]) }}

<x-mail::panel>
- **{{ __('emails.plan') }}:** {{ $subscription->plan->name }}
- **{{ __('emails.end_date') }}:** {{ $subscription->ends_at?->format('Y-m-d') ?? __('emails.immediately') }}
</x-mail::panel>

<x-mail::button :url="route('account.subscriptions')">
{{ __('emails.view_subscriptions') }}
</x-mail::button>

{{ __('emails.regards') }}
{{ config('app.name') }}
</x-mail::message>
