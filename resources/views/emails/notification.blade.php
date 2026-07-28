<x-mail::message>
# {{ $title }}

{{ __('emails.notification_greeting', ['name' => $user->name]) }}

{{ $message }}

<x-mail::button :url="route('notifications.index')">
{{ __('emails.notification_view') }}
</x-mail::button>

{{ __('emails.notification_team', ['app' => config('app.name')]) }}
</x-mail::message>
