<x-app-layout>
    <x-slot:title>{{ __('settings.workspace') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('settings.workspace') }}</x-slot>
    <x-slot:page-description>{{ __('settings.workspace_desc') }}</x-slot>

    <div class="settings-tabs-layout mt-3">
        <div class="settings-tabs-sidebar">
            <x-tabs
                style="vertical"
                mode="server"
                route="settings.workspace.index"
                :current="$tab"
                :tabs="[
                    'general' => ['label' => __('settings.general'), 'icon' => 'bi-gear', 'desc' => __('settings.workspace_desc')],
                    'team' => ['label' => __('workspace.team'), 'icon' => 'bi-people', 'desc' => __('workspace.members_desc')],
                    'roles' => ['label' => __('workspace.roles'), 'icon' => 'bi-shield-check', 'desc' => __('workspace.roles')],
                    'integrations' => ['label' => __('settings.integrations'), 'icon' => 'bi-plug', 'desc' => __('settings.integrations_desc')],
                ]"
            />
        </div>

        <div class="settings-tabs-main">
            @if($tab === 'general')
                @include('settings.partials._tab-general')
            @elseif($tab === 'team')
                @include('settings.partials._tab-team')
            @elseif($tab === 'roles')
                @include('settings.partials._tab-roles')
            @elseif($tab === 'integrations')
                @include('settings.partials._tab-integrations')
            @endif
        </div>
    </div>
</x-app-layout>
