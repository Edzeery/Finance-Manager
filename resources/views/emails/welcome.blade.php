<x-mail::message>
# {{ __('emails.welcome_subject', ['app' => config('app.name')]) }}

{{ __('emails.welcome_greeting', ['name' => $user->name]) }}

{{ __('emails.welcome_intro', ['app' => config('app.name')]) }}

**{{ __('emails.welcome_benefits_title') }}**

- {{ __('emails.welcome_benefits_track') }}
- {{ __('emails.welcome_benefits_manage') }}
- {{ __('emails.welcome_benefits_zakat') }}
- {{ __('emails.welcome_benefits_reports') }}

<x-mail::button :url="route('login')">
{{ __('emails.welcome_get_started') }}
</x-mail::button>

{{ __('emails.welcome_support') }}

{{ __('emails.welcome_team', ['app' => config('app.name')]) }}
</x-mail::message>
