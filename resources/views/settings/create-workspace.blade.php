<x-app-layout>
    <x-slot:title>{{ __('workspace.create') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('workspace.create_new') }}</x-slot>
    <x-slot:page-description>{{ __('workspace.create_desc') }}</x-slot>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="settings-card">
                <form action="{{ route('settings.workspace.store') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label-custom">{{ __('workspace.create_name') }}</label>
                            <input type="text" name="name" class="form-custom" placeholder="{{ __('workspace.create_placeholder') }}" required maxlength="100" autofocus>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <x-button submit>{{ __('workspace.create_btn') }}</x-button>
                        <x-button href="{{ route('settings.workspace.index') }}" variant="outline">{{ __('general.cancel') }}</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
