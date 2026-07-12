<x-mail::message>
# {{ __('emails.two_factor_code_subject', ['app' => config('app.name')]) }}

{{ __('emails.two_factor_code_line') }}

<div style="text-align:center;margin:30px 0;padding:20px;background:#f5f5f5;border-radius:8px;font-size:36px;font-weight:700;letter-spacing:8px;font-family:monospace">
    {{ $code }}
</div>

{{ __('emails.two_factor_code_expires') }}

{{ __('emails.two_factor_code_ignore') }}

{{ __('emails.regards') }}<br>
{{ config('app.name') }}
</x-mail::message>
