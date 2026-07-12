<x-app-layout>
    <x-slot:title>{{ __('report.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('report.title') }}</x-slot>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('report.monthly') }}" class="text-decoration-none">
                <div class="kpi-card text-center">
                    <div class="kpi-icon mx-auto" style="background: rgba(59,130,246,0.12); color: var(--info)">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                    <div class="kpi-value" style="font-size:18px">{{ __('report.monthly') }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('report.yearly') }}" class="text-decoration-none">
                <div class="kpi-card text-center">
                    <div class="kpi-icon mx-auto" style="background: rgba(34,197,94,0.12); color: var(--success)">
                        <i class="bi bi-calendar-year"></i>
                    </div>
                    <div class="kpi-value" style="font-size:18px">{{ __('report.yearly') }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <div class="kpi-card text-center">
                <div class="kpi-icon mx-auto" style="background: rgba(255,193,7,0.12); color: var(--accent)">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </div>
                <div class="kpi-value" style="font-size:18px">{{ __('report.custom') }}</div>
            </div>
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
