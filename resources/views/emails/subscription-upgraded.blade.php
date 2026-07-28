<x-mail::message>
# {{ __('emails.subscription_upgraded_subject') }}

{{ __('emails.hello') }}

{{ __('emails.subscription_upgraded_line', ['old_plan' => $oldPlanName, 'new_plan' => $subscription->plan->name]) }}

<x-mail::panel>
- **{{ __('emails.old_plan') }}:** {{ $oldPlanName }}
- **{{ __('emails.new_plan') }}:** {{ $subscription->plan->name }}
- **{{ __('emails.next_billing') }}:** {{ $subscription->next_billing_at?->format('Y-m-d') ?? __('emails.na') }}
</x-mail::panel>

<x-mail::button :url="route('billing.subscriptions')">
{{ __('emails.view_subscriptions') }}
</x-mail::button>

{{ __('emails.regards') }}
{{ config('app.name') }}
</x-mail::message>
