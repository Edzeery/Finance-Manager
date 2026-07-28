<x-super-admin-layout>
    <x-slot:title>{{ __('notifications.preferences') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('notifications.preferences') }}</x-slot>
    <x-slot:page-description>{{ __('notifications.preferences_help') }}</x-slot>

    @php
        $types = \App\Models\AdminNotificationPreference::getAllTypes();

        $typeMeta = [
            'new_user' => [
                'icon' => 'bi-person-plus',
                'color' => 'var(--info)',
                'bg' => 'rgba(59,130,246,0.1)',
                'label' => __('admin.notif_new_user'),
                'desc' => __('admin.notif_new_user_desc'),
                'group' => 'users',
            ],
            'new_payment' => [
                'icon' => 'bi-cash-stack',
                'color' => 'var(--success)',
                'bg' => 'rgba(34,197,94,0.1)',
                'label' => __('admin.notif_new_payment'),
                'desc' => __('admin.notif_new_payment_desc'),
                'group' => 'billing',
            ],
            'subscription_activated' => [
                'icon' => 'bi-stars',
                'color' => '#6366F1',
                'bg' => 'rgba(99,102,241,0.1)',
                'label' => __('admin.notif_subscription_activated'),
                'desc' => __('admin.notif_subscription_activated_desc'),
                'group' => 'billing',
            ],
            'backup_completed' => [
                'icon' => 'bi-cloud-check',
                'color' => 'var(--sa-indigo)',
                'bg' => 'rgba(139,92,246,0.1)',
                'label' => __('admin.notif_backup_completed'),
                'desc' => __('admin.notif_backup_completed_desc'),
                'group' => 'system',
            ],
            'system_alert' => [
                'icon' => 'bi-exclamation-triangle',
                'color' => 'var(--danger)',
                'bg' => 'rgba(239,68,68,0.1)',
                'label' => __('admin.notif_system_alert'),
                'desc' => __('admin.notif_system_alert_desc'),
                'group' => 'system',
            ],
        ];

        $groups = [
            'users' => ['label' => __('super-admin.users'), 'icon' => 'bi-people'],
            'billing' => ['label' => __('super-admin.payments'), 'icon' => 'bi-credit-card'],
            'system' => ['label' => __('super-admin.system'), 'icon' => 'bi-cpu'],
        ];
    @endphp

    <div style="max-width:760px">
        <form action="{{ route('super.admin.notifications.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Master toggle --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:var(--bg-subtle);border:1px solid var(--border);border-radius:var(--radius) var(--radius) 0 0;border-bottom:none">
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

                <div style="border:1px solid var(--border);border-top:none;{{ $loop->last ? 'border-radius:0 0 var(--radius) var(--radius)' : '' }}">
                    <div style="padding:10px 16px;background:var(--bg-subtle);display:flex;align-items:center;gap:8px">
                        <i class="bi {{ $group['icon'] }}" style="font-size:14px;color:var(--accent)"></i>
                        <span style="font-size:13px;font-weight:600;color:var(--text)">{{ $group['label'] }}</span>
                    </div>

                    @foreach($groupTypes as $type)
                        @php
                            $meta = $typeMeta[$type];
                            $pref = $preferences->get($type);
                            $inApp = $pref?->in_app_enabled ?? true;
                            $email = $pref?->email_enabled ?? true;
                        @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--border)">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:{{ $meta['bg'] }};color:{{ $meta['color'] }}">
                                    <i class="bi {{ $meta['icon'] }}"></i>
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:500;color:var(--text)">{{ $meta['label'] }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:1px">{{ $meta['desc'] }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-4">
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <label style="font-size:10px;color:var(--text-muted)">{{ __('notifications.in_app') }}</label>
                                    <input type="hidden" name="preferences[{{ $type }}][in_app_enabled]" value="0">
                                    <input type="checkbox" name="preferences[{{ $type }}][in_app_enabled]" value="1"
                                        class="form-check-input pref-inapp"
                                        {{ $inApp ? 'checked' : '' }}>
                                </div>
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <label style="font-size:10px;color:var(--text-muted)">{{ __('notifications.email') }}</label>
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
                <button type="submit" class="btn btn-accent btn-custom"><i class="bi bi-check-lg ms-1"></i>{{ __('general.save') }}</button>
            </div>
        </form>
    </div>
</x-super-admin-layout>
