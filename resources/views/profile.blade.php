<x-app-layout>
    <x-slot:title>{{ __('profile.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('general.profile') }}</x-slot>
    <x-slot:page-description>{{ __('profile.page_description') }}</x-slot>

    @php
        $user = Auth::user();
        $initials = implode('', array_map(fn($n) => $n[0] ?? '', array_filter(explode(' ', $user->name))));
        $incomeCount = \App\Models\Income::where('user_id', $user->id)->count();
        $expenseCount = \App\Models\Expense::where('user_id', $user->id)->count();
        $goalCount = \App\Models\FinancialGoal::where('user_id', $user->id)->count();
        $debtCount = \App\Models\Debt::where('user_id', $user->id)->count();
        $assetCount = \App\Models\Asset::where('user_id', $user->id)->count();
        $budgetCount = \App\Models\Budget::where('user_id', $user->id)->count();
    @endphp

    <div class="profile-grid">
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar">
                    <div class="avatar-circle">{{ $initials }}</div>
                    <div class="avatar-online"></div>
                </div>
                <h4 class="profile-name mt-4">{{ $user->name }}</h4>
                <p class="profile-email">{{ $user->email }}</p>
                <span class="profile-joined">{{ __('profile.member_since') }} {{ $user->created_at->translatedFormat('F Y') }}</span>
                <a href="{{ route('settings.account.index') }}" class="profile-settings-btn">
                    <i class="bi bi-gearms-1"></i>{{ __('general.settings') }}
                </a>
            </div>

            <div class="profile-card">
                <div class="profile-card-header">
                    <i class="bi bi-link-45deg"></i>
                    <span>{{ __('profile.quick_links') }}</span>
                </div>
                <nav class="profile-nav">
                    <a href="{{ route('dashboard') }}" wire:navigate class="profile-nav-item">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>{{ __('general.dashboard') }}</span>
                    </a>
                    <a href="{{ route('income.index') }}" wire:navigate class="profile-nav-item">
                        <i class="bi bi-cash-stack"></i>
                        <span>{{ __('general.income') }}</span>
                    </a>
                    <a href="{{ route('expense.index') }}" wire:navigate class="profile-nav-item">
                        <i class="bi bi-cart"></i>
                        <span>{{ __('general.expense') }}</span>
                    </a>
                    <a href="{{ route('report.index') }}" wire:navigate class="profile-nav-item">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i>
                        <span>{{ __('general.report') }}</span>
                    </a>
                    <a href="{{ route('settings.workspace.index') }}" wire:navigate class="profile-nav-item">
                        <i class="bi bi-gear-fill"></i>
                        <span>{{ __('general.settings') }}</span>
                    </a>
                </nav>
            </div>

            <div class="profile-card">
                <div class="profile-card-header">
                    <i class="bi bi-clock-history"></i>
                    <span>{{ __('profile.recent_activity') }}</span>
                    <a href="{{ route('activity.logs') }}" wire:navigate class="profile-card-link">{{ __('profile.view_all') }}</a>
                </div>
                <div class="profile-activity">
                    @php
                        $recentLogs = \App\Models\ActivityLog::where('user_id', $user->id)
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    @if ($recentLogs->count())
                        @foreach ($recentLogs as $log)
                            @php
                                $actionIcon = match($log->action) {
                                    'created' => 'bi-plus-circle text-success',
                                    'updated' => 'bi-pencil text-info',
                                    'deleted' => 'bi-trash text-danger',
                                    'restored' => 'bi-arrow-counterclockwise text-warning',
                                    default => 'bi-circle text-muted',
                                };
                            @endphp
                            <div class="profile-activity-item">
                                <i class="bi {{ $actionIcon }}"></i>
                                <div>
                                    <p>{{ $log->description ?: __('general.unknown') }}</p>
                                    <span>{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="profile-activity-empty">
                            <i class="bi bi-clock-history"></i>
                            <p>{{ __('profile.no_activity') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="profile-main">
            <div class="profile-stats mb-4" role="list">

                    <x-kpi-card icon="bi-cash-stack" iconBg="rgba(34,197,94,0.12)" iconColor="var(--success)" :label="__('profile.stats_income')" :value="$incomeCount" />

                    <x-kpi-card icon="bi-cart" iconBg="rgba(239,68,68,0.12)" iconColor="var(--danger)" :label="__('profile.stats_expense')" :value="$expenseCount" />

                    <x-kpi-card icon="bi-flag" iconBg="rgba(139,92,246,0.12)" iconColor="#8B5CF6" :label="__('profile.stats_goals')" :value="$goalCount" />


                    <x-kpi-card icon="bi-credit-card-2-front" iconBg="rgba(245,158,11,0.12)" iconColor="var(--warning)" :label="__('profile.stats_debts')" :value="$debtCount" />

                    <x-kpi-card icon="bi-pie-chart" iconBg="rgba(59,130,246,0.12)" iconColor="var(--info)" :label="__('profile.stats_assets')" :value="$assetCount" />

                    <x-kpi-card icon="bi-calculator" iconBg="rgba(21,183,108,0.12)" iconColor="var(--accent)" :label="__('profile.stats_budgets')" :value="$budgetCount" />

            </div>

            <div class="settings-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                        <i class="bi bi-person" style="color:var(--accent);font-size:16px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('profile.account_info') }}</h5>
                        <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('profile.account_info_help') }}</p>
                    </div>
                </div>
                <livewire:profile.update-profile-information-form />
            </div>

            <div class="text-center mt-4">
                <x-button href="{{ route('settings.account.index') }}" variant="accent" icon="bi bi-gear">{{ __('settings.preferences') }}</x-button>
            </div>
        </div>
    </div>
</x-app-layout>
