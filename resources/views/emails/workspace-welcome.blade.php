<x-mail::message>
# {{ __('emails.workspace_welcome_subject', ['workspace' => $workspace->name, 'app' => config('app.name')]) }}

{{ __('emails.workspace_welcome_greeting', ['name' => $user->name]) }}

{{ __('emails.workspace_welcome_intro', ['workspace' => $workspace->name, 'role' => $role]) }}

{{ __('emails.workspace_welcome_inviter_intro') }}

<x-mail::button :url="route('login')">
{{ __('emails.workspace_welcome_cta') }}
</x-mail::button>

{{ __('emails.workspace_welcome_support') }}

{{ __('emails.workspace_welcome_team', ['app' => config('app.name')]) }}
</x-mail::message>
