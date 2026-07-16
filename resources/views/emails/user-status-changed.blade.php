<x-mail::message>
# {{ __('emails.status_changed_subject', ['status' => __('account.' . strtolower($newStatus->value))]) }}

{{ __('emails.hello') }}

{{ __('emails.status_changed_line', ['status' => __('account.' . strtolower($newStatus->value))]) }}

@if($reason)
<x-mail::panel>
- **{{ __('account.reason') }}:** {{ $reason }}
</x-mail::panel>
@endif

<x-mail::button :url="$pageUrl">
{{ __('emails.view_details') }}
</x-mail::button>

{{ __('emails.status_changed_footer') }}
{{ config('app.name') }}
</x-mail::message>
