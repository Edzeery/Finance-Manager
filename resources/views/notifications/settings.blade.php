<x-app-layout>
    <x-slot:title>{{ __('notifications.preferences') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('notifications.preferences') }}</x-slot>
    <x-slot:page-description>{{ __('notifications.preferences_help') }}</x-slot>

    @php
        $types = \App\Models\NotificationPreference::getAllTypes();

        $typeMeta = [
            'budget_exceeded' => [
                'icon' => 'bi-exclamation-triangle',
                'color' => 'var(--danger)',
                'bg' => 'rgba(239,68,68,0.1)',
                'label' => __('notifications.budget_exceeded'),
                'desc' => __('notifications.budget_exceeded_desc'),
                'group' => 'financial',
            ],
            'budget_nearing_limit' => [
                'icon' => 'bi-exclamation-circle',
                'color' => 'var(--warning)',
                'bg' => 'rgba(245,158,11,0.1)',
                'label' => __('notifications.budget_nearing_limit'),
                'desc' => __('notifications.budget_nearing_limit_desc'),
                'group' => 'financial',
            ],
            'debt_reminder' => [
                'icon' => 'bi-credit-card-2-front',
                'color' => 'var(--warning)',
                'bg' => 'rgba(245,158,11,0.1)',
                'label' => __('notifications.debt_reminder'),
                'desc' => __('notifications.debt_reminder_desc'),
                'group' => 'financial',
            ],
            'goal_achieved' => [
                'icon' => 'bi-flag-fill',
                'color' => 'var(--success)',
                'bg' => 'rgba(34,197,94,0.1)',
                'label' => __('notifications.goal_achieved'),
                'desc' => __('notifications.goal_achieved_desc'),
                'group' => 'goals',
            ],
            'goal_milestone' => [
                'icon' => 'bi-flag',
                'color' => 'var(--success)',
                'bg' => 'rgba(34,197,94,0.1)',
                'label' => __('notifications.goal_milestone'),
                'desc' => __('notifications.goal_milestone_desc'),
                'group' => 'goals',
            ],
            'goal_deadline' => [
                'icon' => 'bi-clock-history',
                'color' => 'var(--info)',
                'bg' => 'rgba(59,130,246,0.1)',
                'label' => __('notifications.goal_deadline'),
                'desc' => __('notifications.goal_deadline_desc'),
                'group' => 'goals',
            ],
            'zakat_reminder' => [
                'icon' => 'bi-heart-fill',
                'color' => 'var(--sa-indigo)',
                'bg' => 'rgba(139,92,246,0.1)',
                'label' => __('notifications.zakat_reminder'),
                'desc' => __('notifications.zakat_reminder_desc'),
                'group' => 'zakat',
            ],
            'zakat_approaching' => [
                'icon' => 'bi-hourglass-split',
                'color' => '#6366F1',
                'bg' => 'rgba(99,102,241,0.1)',
                'label' => __('notifications.zakat_approaching'),
                'desc' => __('notifications.zakat_approaching_desc'),
                'group' => 'zakat',
            ],
            'login_new_device' => [
                'icon' => 'bi-phone',
                'color' => 'var(--info)',
                'bg' => 'rgba(59,130,246,0.1)',
                'label' => __('notifications.login_new_device'),
                'desc' => __('notifications.login_new_device_desc'),
                'group' => 'security',
            ],
            'login_suspicious' => [
                'icon' => 'bi-shield-exclamation',
                'color' => 'var(--danger)',
                'bg' => 'rgba(239,68,68,0.1)',
                'label' => __('notifications.login_suspicious'),
                'desc' => __('notifications.login_suspicious_desc'),
                'group' => 'security',
            ],
            'password_changed' => [
                'icon' => 'bi-key',
                'color' => 'var(--warning)',
                'bg' => 'rgba(245,158,11,0.1)',
                'label' => __('notifications.password_changed'),
                'desc' => __('notifications.password_changed_desc'),
                'group' => 'security',
            ],
            'two_factor_enabled' => [
                'icon' => 'bi-shield-lock',
                'color' => 'var(--success)',
                'bg' => 'rgba(34,197,94,0.1)',
                'label' => __('notifications.two_factor_enabled'),
                'desc' => __('notifications.two_factor_enabled_desc'),
                'group' => 'security',
            ],
            'two_factor_disabled' => [
                'icon' => 'bi-shield-x',
                'color' => 'var(--danger)',
                'bg' => 'rgba(239,68,68,0.1)',
                'label' => __('notifications.two_factor_disabled'),
                'desc' => __('notifications.two_factor_disabled_desc'),
                'group' => 'security',
            ],
            'session_revoked' => [
                'icon' => 'bi-box-arrow-right',
                'color' => '#F97316',
                'bg' => 'rgba(249,115,22,0.1)',
                'label' => __('notifications.session_revoked'),
                'desc' => __('notifications.session_revoked_desc'),
                'group' => 'account',
            ],
            'email_changed' => [
                'icon' => 'bi-envelope-at',
                'color' => 'var(--info)',
                'bg' => 'rgba(59,130,246,0.1)',
                'label' => __('notifications.email_changed'),
                'desc' => __('notifications.email_changed_desc'),
                'group' => 'account',
            ],
            'workspace_member_login' => [
                'icon' => 'bi-person-check',
                'color' => 'var(--success)',
                'bg' => 'rgba(34,197,94,0.1)',
                'label' => __('notifications.workspace_member_login'),
                'desc' => __('notifications.workspace_member_login_desc'),
                'group' => 'workspace',
            ],
        ];

        $groups = [
            'financial' => ['label' => __('notifications.group_financial'), 'icon' => 'bi-wallet2'],
            'goals' => ['label' => __('notifications.group_goals'), 'icon' => 'bi-flag'],
            'zakat' => ['label' => __('notifications.group_zakat'), 'icon' => 'bi-heart'],
            'security' => ['label' => __('notifications.group_security'), 'icon' => 'bi-shield-lock'],
            'account' => ['label' => __('notifications.group_account'), 'icon' => 'bi-person-circle'],
            'workspace' => ['label' => __('notifications.group_workspace'), 'icon' => 'bi-building'],
        ];
    @endphp

    <div class="settings-card" style="max-width:760px">
        <form action="{{ route('notifications.settings.update') }}" method="POST" id="notif-prefs-form">
            @csrf
            @method('PUT')

            {{-- Master toggle --}}
            <div class="notif-prefs-master">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-toggle2-on" style="font-size:20px;color:var(--success)"></i>
                    <span style="font-size:13px;font-weight:600;color:var(--text)">{{ __('notifications.enable_all') }}</span>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <label style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:4px;cursor:pointer">
                        <input type="checkbox" class="form-check-input" id="toggle-all-inapp"
                            onclick="document.querySelectorAll('.pref-inapp').forEach(cb => cb.checked = this.checked)">
                        <i class="bi bi-phone" style="font-size:12px"></i> {{ __('notifications.in_app') }}
                    </label>
                    <label style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:4px;cursor:pointer">
                        <input type="checkbox" class="form-check-input" id="toggle-all-email"
                            onclick="document.querySelectorAll('.pref-email').forEach(cb => cb.checked = this.checked)">
                        <i class="bi bi-envelope" style="font-size:12px"></i> {{ __('notifications.email') }}
                    </label>
                </div>
            </div>

            {{-- Grouped preferences --}}
            @foreach($groups as $groupKey => $group)
                @php
                    $groupTypes = collect($types)->filter(fn($t) => ($typeMeta[$t]['group'] ?? '') === $groupKey)->values();
                @endphp
                @if($groupTypes->isEmpty()) @continue @endif

                <div class="notif-prefs-group">
                    <div class="notif-prefs-group-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi {{ $group['icon'] }}" style="font-size:14px;color:var(--accent)"></i>
                            <span style="font-size:13px;font-weight:600;color:var(--text)">{{ $group['label'] }}</span>
                        </div>
                    </div>

                    @foreach($groupTypes as $type)
                        @php
                            $meta = $typeMeta[$type];
                            $pref = $preferences->get($type);
                            $inApp = $pref?->in_app_enabled ?? true;
                            $email = $pref?->email_enabled ?? true;
                        @endphp
                        <div class="notif-prefs-row">
                            <div class="notif-prefs-info">
                                <div class="notif-prefs-icon" style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }}">
                                    <i class="bi {{ $meta['icon'] }}"></i>
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:500;color:var(--text)">{{ $meta['label'] }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:1px">{{ $meta['desc'] }}</div>
                                </div>
                            </div>
                            <div class="notif-prefs-toggles">
                                <div class="notif-prefs-toggle">
                                    <label style="font-size:11px;color:var(--text-muted)">{{ __('notifications.in_app') }}</label>
                                    <input type="hidden" name="preferences[{{ $type }}][in_app_enabled]" value="0">
                                    <input type="checkbox" name="preferences[{{ $type }}][in_app_enabled]" value="1"
                                        class="form-check-input pref-inapp"
                                        {{ $inApp ? 'checked' : '' }}>
                                </div>
                                <div class="notif-prefs-toggle">
                                    <label style="font-size:11px;color:var(--text-muted)">{{ __('notifications.email') }}</label>
                                    <input type="hidden" name="preferences[{{ $type }}][email_enabled]" value="0">
                                    <input type="checkbox" name="preferences[{{ $type }}][email_enabled]" value="1"
                                        class="form-check-input pref-email"
                                        {{ $email ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <div class="d-flex justify-content-end mt-4">
                <x-button submit variant="accent" icon="bi bi-check-lg">{{ __('general.save') }}</x-button>
            </div>
        </form>
    </div>
</x-app-layout>
