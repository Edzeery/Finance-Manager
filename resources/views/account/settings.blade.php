<x-app-layout>
    <x-slot:title>{{ __('settings.account') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('settings.account') }}</x-slot>
    <x-slot:page-description>{{ __('profile.page_description') }}</x-slot>

    <div class="settings-tabs-layout mt-3">
        <div class="settings-tabs-sidebar">
            <x-tabs
                style="vertical"
                mode="server"
                route="settings.account.index"
                :current="$tab"
                :tabs="[
                    'profile' => ['label' => __('profile.profile_info'), 'icon' => 'bi-person', 'desc' => __('profile.account_info_help')],
                    'security' => ['label' => __('settings.security'), 'icon' => 'bi-shield-lock', 'desc' => __('messages.add_2fa_security')],
                ]"
            />
        </div>

        <div class="settings-tabs-main">
            @if($tab === 'profile')
                @include('account.partials._tab-profile')
            @elseif($tab === 'security')
                @include('account.partials._tab-security')
            @endif
        </div>
    </div>
</x-app-layout>
