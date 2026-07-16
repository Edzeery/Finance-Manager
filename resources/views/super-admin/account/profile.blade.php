<x-super-admin-layout>
    <x-slot:title>{{ __('profile.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('general.profile') }}</x-slot>
    <x-slot:page-description>{{ __('profile.page_description') }}</x-slot>

    @php
        $user = Auth::user();
        $initials = implode('', array_map(fn($n) => $n[0] ?? '', array_filter(explode(' ', $user->name))));
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
                <div class="mt-3 text-center">
                    <span class="badge" style="background:var(--accent);color:#0F172A;font-size:12px;padding:4px 12px;border-radius:20px">
                        <i class="bi bi-shield-fill-check me-1"></i>{{ __('super-admin.super_admin') }}
                    </span>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-card-header">
                    <i class="bi bi-link-45deg"></i>
                    <span>{{ __('profile.quick_links') }}</span>
                </div>
                <nav class="profile-nav">
                    <a href="{{ route('super.admin.dashboard') }}" wire:navigate class="profile-nav-item">
                        <i class="bi bi-shield-shaded"></i>
                        <span>{{ __('super-admin.dashboard') }}</span>
                    </a>
                    <a href="{{ route('super.admin.settings.index') }}" wire:navigate class="profile-nav-item">
                        <i class="bi bi-gear-fill"></i>
                        <span>{{ __('super-admin.settings') }}</span>
                    </a>
                    <a href="{{ route('super.admin.activity-log') }}" wire:navigate class="profile-nav-item">
                        <i class="bi bi-clock-history"></i>
                        <span>{{ __('super-admin.activity_log') }}</span>
                    </a>
                </nav>
            </div>
        </div>

        <div class="profile-main">
            <div class="profile-card">
                <div class="profile-card-header">
                    <i class="bi bi-person-badge"></i>
                    <span>{{ __('general.account_details') }}</span>
                </div>
                <div class="profile-info-grid">
                    <div class="info-item">
                        <span class="info-label">{{ __('general.name') }}</span>
                        <span class="info-value">{{ $user->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('general.email') }}</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('general.roles') }}</span>
                        <span class="info-value">
                            @foreach($user->roles as $role)
                                <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                            @endforeach
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('profile.member_since') }}</span>
                        <span class="info-value">{{ $user->created_at->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
