<x-app-layout>
    <x-slot:title>{{ __('report.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('report.title') }}</x-slot>

    <div class="row g-4">
        <div class="col-md-4">
            <x-kpi-card
                icon="bi-calendar-month"
                iconBg="var(--info-light)"
                iconColor="var(--info)"
                :value="__('report.monthly')"
                size="sm"
                center
                :href="route('report.monthly')"
            />
        </div>
        <div class="col-md-4">
            <x-kpi-card
                icon="bi-calendar-year"
                iconBg="var(--success-light)"
                iconColor="var(--success)"
                :value="__('report.yearly')"
                size="sm"
                center
                :href="route('report.yearly')"
            />
        </div>
        <div class="col-md-4">
            <x-kpi-card
                icon="bi-file-earmark-bar-graph"
                iconBg="var(--accent-light)"
                iconColor="var(--accent)"
                :value="__('report.custom')"
                size="sm"
                center
            />
        </div>
    </div>

    <div class="card-custom mt-4">
        <div class="card-body">
            @include('components.empty-state', [
                'icon' => 'bi-file-earmark-bar-graph-fill',
                'title' => __('general.no_data'),
            ])
        </div>
    </div>
</x-app-layout>
