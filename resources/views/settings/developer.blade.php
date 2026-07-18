<x-app-layout>
    <x-slot:title>{{ __('developer.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('developer.api') }}</x-slot>
    <x-slot:page-description>{{ __('developer.page_description') }}</x-slot>

    @if(session('api_token'))
        @php $tokenVal = session('api_token'); @endphp
        <div id="new-token-value" data-token="{{ $tokenVal }}" style="display:none"></div>
    @endif

    <div class="row g-4">

        {{-- Main Content --}}
        <div class="col-lg-8">

            {{-- Stats Hero Card --}}
            <div class="settings-section">
                <div class="settings-card" style="overflow:hidden">
                    <div style="background:linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #000));margin:-1.25rem -1.25rem 1.25rem -1.25rem;padding:1.5rem 1.75rem;position:relative">
                        <div style="position:absolute;top:0;right:0;width:200px;height:200px;background:rgba(255,255,255,0.05);border-radius:50%;transform:translate(50%,-50%)"></div>
                        <div style="position:absolute;bottom:0;left:0;width:150px;height:150px;background:rgba(255,255,255,0.03);border-radius:50%;transform:translate(-30%,30%)"></div>
                        <div class="d-flex align-items-center justify-content-between gap-3" style="position:relative;z-index:1">
                            <div>
                                <div style="font-size:13px;color:rgba(255,255,255,0.7);margin-bottom:4px">{{ __('developer.api_tokens') }}</div>
                                <h3 style="font-weight:700;color:#fff;margin-bottom:0">{{ __('developer.api') }} <span style="font-weight:400;font-size:14px;color:rgba(255,255,255,0.6)">{{ __('developer.api_tokens_desc') }}</span></h3>
                            </div>
                            <button class="btn btn-custom flex-shrink-0" style="background:rgba(255,255,255,0.2);color:#fff;border:none" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                                <i class="bi bi-plus-lg me-1"></i>{{ __('developer.create_token') }}
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div style="text-align:center;padding:12px">
                                <div style="font-size:28px;font-weight:700;color:var(--text-color)">{{ $stats['total'] }}</div>
                                <div style="font-size:12px;color:var(--text-muted)">{{ __('general.total') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div style="text-align:center;padding:12px">
                                <div style="font-size:28px;font-weight:700;color:var(--success)">{{ $stats['active'] }}</div>
                                <div style="font-size:12px;color:var(--text-muted)">{{ __('general.active') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div style="text-align:center;padding:12px">
                                <div style="font-size:28px;font-weight:700;color:var(--danger)">{{ $stats['expired'] }}</div>
                                <div style="font-size:12px;color:var(--text-muted)">{{ __('general.expired') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div style="text-align:center;padding:12px">
                                <div style="font-size:28px;font-weight:700;color:var(--text-muted)">{{ $stats['never_used'] }}</div>
                                <div style="font-size:12px;color:var(--text-muted)">{{ __('developer.never_used') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Token Limit Bar --}}
                    <div style="margin-top:8px;padding:10px 16px;background:var(--bg-subtle);border-radius:8px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-bar-chart-fill" style="color:var(--accent);font-size:14px"></i>
                            <span style="font-size:13px;font-weight:500">{{ __('developer.token_usage') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width:300px">
                            <div class="progress flex-grow-1" style="height:6px;border-radius:3px;background:var(--border)">
                                @php $usagePercent = $stats['total'] > 0 ? min(100, round(($stats['total'] / max(1, $stats['token_limit'])) * 100)) : 0; @endphp
                                <div class="progress-bar" role="progressbar"
                                    style="width:{{ $usagePercent }}%;border-radius:3px;background:{{ $usagePercent >= 90 ? 'var(--danger)' : ($usagePercent >= 70 ? 'var(--warning)' : 'var(--accent))') }}"></div>
                            </div>
                            <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">{{ $stats['total'] }} / {{ $stats['token_limit'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- API Quota Dashboard --}}
            @if($quotaLimits['minute'] > 0 || $quotaLimits['hour'] > 0 || $quotaLimits['day'] > 0)
            <div class="settings-section">
                <div class="settings-card">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-speedometer2" style="color:var(--accent)"></i>
                        <span>{{ __('developer.api_quota') }}</span>
                    </h5>
                    <div class="row g-3">
                        @foreach(['minute' => __('developer.per_minute'), 'hour' => __('developer.per_hour'), 'day' => __('developer.per_day')] as $period => $label)
                            @php
                                $limit = $quotaLimits[$period];
                                $used = $quotaUsage[$period];
                                $remaining = max(0, $limit - $used);
                                $percent = $limit > 0 ? min(100, round(($used / $limit) * 100)) : 0;
                                $barColor = $percent >= 90 ? 'var(--danger)' : ($percent >= 70 ? 'var(--warning)' : 'var(--accent)');
                                $reset = \Carbon\Carbon::createFromTimestamp($quotaReset[$period]);
                            @endphp
                            <div class="col-md-4">
                                <div style="border:1px solid var(--border);border-radius:10px;padding:14px;height:100%">
                                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px">{{ $label }}</div>
                                    <div class="d-flex align-items-baseline gap-1 mb-2">
                                        <span style="font-size:24px;font-weight:700;color:var(--text-color)">{{ $used }}</span>
                                        <span style="font-size:13px;color:var(--text-muted)">/ {{ $limit }}</span>
                                    </div>
                                    <div class="progress" style="height:5px;border-radius:3px;background:var(--border);margin-bottom:6px">
                                        <div class="progress-bar" role="progressbar" style="width:{{ $percent }}%;border-radius:3px;background:{{ $barColor }}"></div>
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);display:flex;justify-content:space-between">
                                        <span>{{ $remaining }} {{ __('developer.remaining') }}</span>
                                        <span><i class="bi bi-arrow-clockwise me-1"></i>{{ $reset->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Tokens List --}}
            <div class="settings-section">
                <div class="settings-card">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-key-fill" style="color:var(--accent)"></i>
                                <span>{{ __('developer.api_tokens') }}</span>
                            </h5>
                        </div>
                        @if($tokens->isNotEmpty())
                            <div class="d-flex gap-2">
                                @if($tokens->count() > 1)
                                    <form action="{{ route('account.settings.developer.revoke-all') }}" method="POST" id="revoke-all-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="showConfirmModal('{{ __('developer.revoke_all') }}', '{{ __('developer.revoke_all_confirm') }}', (confirmed) => { if (confirmed) document.getElementById('revoke-all-form').submit(); }, '{{ __('developer.revoke_all') }}', 'btn-danger')">
                                            <i class="bi bi-trash me-1"></i>{{ __('developer.revoke_all') }}
                                        </button>
                                    </form>
                                @endif
                                <button class="btn btn-accent btn-custom btn-sm" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                                    <i class="bi bi-plus-lg me-1"></i>{{ __('developer.create_token') }}
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($tokens->isEmpty())
                        <div class="text-center py-5">
                            <div style="width:72px;height:72px;border-radius:50%;background:var(--bg-subtle);display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                                <i class="bi bi-key" style="font-size:2rem;color:var(--text-muted);opacity:0.4"></i>
                            </div>
                            <p class="fw-medium mb-1">{{ __('developer.no_tokens') }}</p>
                            <p class="text-muted small mb-3">{{ __('developer.no_tokens_desc') }}</p>
                            <button class="btn btn-accent btn-custom" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                                <i class="bi bi-plus-lg me-1"></i>{{ __('developer.create_token') }}
                            </button>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-2">
                            @foreach($tokens as $token)
                                @php
                                    $isExpired = $token['expires_at'] && now()->gt($token['expires_at']);
                                    $expiresSoon = $token['expires_at'] && !$isExpired && now()->diffInDays($token['expires_at']) <= 7;
                                    $isDeactivated = !$isExpired && $token['deactivated_at'];
                                    $isActive = !$isExpired && !$isDeactivated;
                                    $expiryPercent = $token['expires_at'] ? min(100, round((now()->diffInDays($token['expires_at'], false) / max(1, $token['created_at']->diffInDays($token['expires_at']))) * 100)) : null;
                                    $abilityCount = count($token['abilities']);
                                    $displayAbilities = $abilityCount <= 3 ? $token['abilities'] : array_slice($token['abilities'], 0, 3);
                                    $maskedToken = $token['plaintext_token'] ? (substr($token['plaintext_token'], 0, 8) . '****' . substr($token['plaintext_token'], -6)) : null;
                                    $statusColor = $isExpired ? 'var(--danger-border, #fecaca)' : ($isDeactivated ? 'var(--warning-border, #fde68a)' : 'var(--border)');
                                @endphp
                                <div class="token-card" style="border:1px solid {{ $statusColor }};border-radius:10px;padding:16px;transition:all 0.2s;background:var(--card-bg)">
                                    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                                        <div class="d-flex align-items-start gap-3 flex-grow-1">
                                            <div style="width:40px;height:40px;border-radius:8px;background:{{ $isExpired ? 'var(--danger-light, #fef2f2)' : ($isDeactivated ? 'var(--warning-light, #fef3c7)' : 'var(--accent-light, rgba(21,183,108,0.12))') }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                                <i class="bi bi-key-fill" style="color:{{ $isExpired ? 'var(--danger)' : ($isDeactivated ? 'var(--warning)' : 'var(--accent)') }};font-size:1.1rem"></i>
                                            </div>
                                            <div class="flex-grow-1" style="min-width:0">
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span id="token-name-{{ $token['id'] }}" class="fw-semibold" style="font-size:14px">{{ $token['name'] }}</span>
                                                    @if($isExpired)
                                                        <x-status-badge domain="general" status="expired" set="bi" size="xs" />
                                                    @elseif($isDeactivated)
                                                        <x-status-badge domain="general" status="inactive" set="bi" size="xs" />
                                                    @elseif($expiresSoon)
                                                        <x-status-badge domain="general" status="warning" set="bi" size="xs" />
                                                    @endif
                                                </div>
                                                <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size:12px;color:var(--text-muted);margin-top:4px">
                                                    <span><i class="bi bi-calendar3 me-1"></i>{{ $token['created_at']->format('M d, Y') }}</span>
                                                    <span><i class="bi bi-clock-history me-1"></i>{{ $token['last_used_at'] ? $token['last_used_at']->diffForHumans() : __('developer.never_used') }}</span>
                                                @if($token['expires_at'])
                                                    <span class="{{ $isExpired ? 'text-danger' : '' }}"><i class="bi bi-hourglass-split me-1"></i>{{ $token['expires_at']->format('M d, Y') }}</span>
                                                @else
                                                    <span><i class="bi bi-infinity me-1"></i>{{ __('developer.never_expires') }}</span>
                                                @endif
                                                    <span><i class="bi bi-graph-up me-1"></i>{{ $token['usage_7d'] }} {{ __('developer.requests_7d') }}</span>
                                            </div>
                                                @if($token['expires_at'] && !$isExpired && $expiryPercent !== null)
                                                    <div style="margin-top:8px;max-width:240px">
                                                        <div style="display:flex;justify-content:space-between;font-size:10px;color:var(--text-muted);margin-bottom:2px">
                                                            <span>{{ now()->diffInDays($token['expires_at']) }} {{ __('general.days_left') }}</span>
                                                        </div>
                                                        <div class="progress" style="height:3px;border-radius:2px;background:var(--border)">
                                                            <div class="progress-bar" role="progressbar" style="width:{{ $expiryPercent }}%;border-radius:2px;background:{{ $expiresSoon ? 'var(--warning)' : 'var(--accent)' }}"></div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($maskedToken)
                                                    <div style="margin-top:6px;display:flex;align-items:center;gap:6px">
                                                        <code style="font-size:11px;background:var(--bg-subtle);border:1px solid var(--border);border-radius:4px;padding:2px 8px;color:var(--text-muted);letter-spacing:0.5px;direction:ltr;display:inline-block">{{ $maskedToken }}</code>
                                                        <span style="font-size:10px;color:var(--text-muted)"><i class="bi bi-lock-fill me-1"></i>{{ __('developer.token_masked') }}</span>
                                                    </div>
                                                @endif
                                                <div style="margin-top:6px;display:flex;align-items:center;gap:4px;flex-wrap:wrap">
                                                    @foreach($displayAbilities as $a)
                                                        <span class="badge" style="background:var(--bg-subtle);color:var(--text-color);font-size:10px;padding:2px 8px;border-radius:20px;font-weight:500;border:1px solid var(--border)">{{ $a }}</span>
                                                    @endforeach
                                                    @if($abilityCount > 3)
                                                        <span class="badge" style="background:var(--bg-subtle);color:var(--text-muted);font-size:10px;padding:2px 8px;border-radius:20px;font-weight:500;border:1px solid var(--border)">+{{ $abilityCount - 3 }} {{ __('general.more') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-shrink-0">
                                            <button type="button" class="btn btn-sm btn-outline-accent" title="{{ __('developer.details') }}"
                                                @click="showTokenFullDetails({{ $token['id'] }}, '{{ $token['name'] }}', '{{ $token['created_at']->format('M d, Y H:i') }}', '{{ $token['last_used_at'] ? $token['last_used_at']->diffForHumans() : __('developer.never_used') }}', {{ json_encode($token['abilities']) }}, '{{ $token['expires_at'] ? $token['expires_at']->format('M d, Y') : __('developer.never_expires') }}', {{ $token['deactivated_at'] ? 'true' : 'false' }})">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                            @if($isActive)
                                                <button type="button" class="btn btn-sm btn-outline-accent" title="{{ __('developer.view_token') }}"
                                                    @click="showTokenFullDetails({{ $token['id'] }}, '{{ $token['name'] }}', '{{ $token['created_at']->format('M d, Y H:i') }}', '{{ $token['last_used_at'] ? $token['last_used_at']->diffForHumans() : __('developer.never_used') }}', {{ json_encode($token['abilities']) }}, '{{ $token['expires_at'] ? $token['expires_at']->format('M d, Y') : __('developer.never_expires') }}', {{ $token['deactivated_at'] ? 'true' : 'false' }})">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-accent" title="{{ __('developer.rename') }}"
                                                    @click="showRenameModal({{ $token['id'] }}, '{{ $token['name'] }}')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('account.settings.developer.regenerate', $token['id']) }}" method="POST" id="regenerate-form-{{ $token['id'] }}" style="display:inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-outline-warning" title="{{ __('developer.regenerate') }}"
                                                        @click="showConfirmModal('{{ __('developer.regenerate') }}', '{{ __('developer.regenerate_confirm') }}', (confirmed) => { if (confirmed) document.getElementById('regenerate-form-{{ $token['id'] }}').submit(); }, '{{ __('developer.regenerate') }}', 'btn-warning')">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('account.settings.developer.deactivate', $token['id']) }}" method="POST" id="deactivate-form-{{ $token['id'] }}" style="display:inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-outline-warning" title="{{ __('developer.deactivate') }}"
                                                        @click="showConfirmModal('{{ __('developer.deactivate') }}', '{{ __('developer.deactivate_confirm') }}', (confirmed) => { if (confirmed) document.getElementById('deactivate-form-{{ $token['id'] }}').submit(); }, '{{ __('developer.deactivate') }}', 'btn-warning')">
                                                        <i class="bi bi-pause-fill"></i>
                                                    </button>
                                                </form>
                                            @elseif($isDeactivated)
                                                <form action="{{ route('account.settings.developer.activate', $token['id']) }}" method="POST" id="activate-form-{{ $token['id'] }}" style="display:inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-outline-success" title="{{ __('developer.activate') }}"
                                                        @click="showConfirmModal('{{ __('developer.activate') }}', '{{ __('developer.activate_confirm') }}', (confirmed) => { if (confirmed) document.getElementById('activate-form-{{ $token['id'] }}').submit(); }, '{{ __('developer.activate') }}', 'btn-success')">
                                                        <i class="bi bi-play-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('account.settings.developer.revoke', $token['id']) }}" method="POST" id="revoke-form-{{ $token['id'] }}" style="display:inline">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('developer.revoke') }}"
                                                    @click="showConfirmModal('{{ __('developer.revoke') }}', '{{ __('developer.revoke_confirm') }}', (confirmed) => { if (confirmed) document.getElementById('revoke-form-{{ $token['id'] }}').submit(); }, '{{ __('developer.revoke') }}', 'btn-danger')">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="settings-section" style="position:sticky;top:24px">

                {{-- Quick Test --}}
                <div class="settings-card mb-3">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-terminal" style="color:var(--accent)"></i>
                        <span>{{ __('general.quick_test') }}</span>
                    </h5>
                    <p class="section-desc mb-2">{{ __('developer.quick_test_desc') }}</p>
                    <div style="background:var(--bg-subtle);border:1px solid var(--border);border-radius:8px;padding:12px;font-size:12px;font-family:monospace;direction:ltr;text-align:left;overflow-x:auto;white-space:nowrap">
                        <div style="color:var(--text-muted);margin-bottom:4px"># {{ __('general.curl_example') }}</div>
                        <div style="color:var(--accent)">curl -X GET "{{ url('/api/workspace') }}" \</div>
                        <div style="color:var(--accent);padding-inline-start:20px">-H "Authorization: Bearer <span style="color:var(--warning)">{{ __('general.your_token') }}</span>" \</div>
                        <div style="color:var(--accent);padding-inline-start:20px">-H "Accept: application/json"</div>
                    </div>
                    @if($tokens->isNotEmpty())
                        <div class="mt-2 d-flex gap-1">
                            <button class="btn btn-sm btn-outline-accent flex-fill" @click="testApiToken()">
                                <i class="bi bi-play-fill me-1"></i>{{ __('general.test_connection') }}
                            </button>
                        </div>
                        <div id="apiTestResult" class="mt-2 d-none"></div>
                    @endif
                </div>

                {{-- API Base URL --}}
                <div class="settings-card mb-3">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-link-45deg" style="color:var(--accent)"></i>
                        <span>{{ __('developer.api_base_url') }}</span>
                    </h5>
                    <div class="mt-2">
                        <div class="d-flex align-items-center gap-1" style="background:var(--bg-subtle);border:1px solid var(--border);border-radius:6px;padding:8px 12px">
                            <code style="font-size:13px;flex:1;background:none;border:none;padding:0" class="copy-target">{{ url('/api') }}</code>
                            <button class="btn btn-sm btn-link text-muted p-0" @click="copyToClipboard(this, '{{ url('/api') }}')" title="{{ __('general.copy') }}">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Auth Endpoints --}}
                <div class="settings-card mb-3">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-shield-check" style="color:var(--accent)"></i>
                        <span>{{ __('developer.auth_endpoints') }}</span>
                    </h5>
                    <div class="mt-2 d-flex flex-column gap-1">
                        <div class="endpoint-row" style="display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:6px;background:var(--bg-subtle);border:1px solid var(--border)">
                            <span class="badge" style="background:var(--success);color:#fff;font-size:9px;padding:2px 6px;border-radius:4px;font-weight:600;text-transform:uppercase;flex-shrink:0">POST</span>
                            <code style="font-size:11px;background:none;border:none;padding:0;flex:1">/api/auth/login</code>
                        </div>
                        <div class="endpoint-row" style="display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:6px;background:var(--bg-subtle);border:1px solid var(--border)">
                            <span class="badge" style="background:var(--accent);color:#fff;font-size:9px;padding:2px 6px;border-radius:4px;font-weight:600;text-transform:uppercase;flex-shrink:0">POST</span>
                            <code style="font-size:11px;background:none;border:none;padding:0;flex:1">/api/auth/register</code>
                        </div>
                        <div class="endpoint-row" style="display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:6px;background:var(--bg-subtle);border:1px solid var(--border)">
                            <span class="badge" style="background:var(--danger);color:#fff;font-size:9px;padding:2px 6px;border-radius:4px;font-weight:600;text-transform:uppercase;flex-shrink:0">POST</span>
                            <code style="font-size:11px;background:none;border:none;padding:0;flex:1">/api/auth/logout</code>
                        </div>
                    </div>
                </div>

                {{-- Usage History --}}
                @if($usageHistory->isNotEmpty())
                <div class="settings-card mb-3">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-bar-chart-steps" style="color:var(--accent)"></i>
                        <span>{{ __('developer.usage_history') }}</span>
                    </h5>
                    <p class="section-desc mb-2">{{ __('developer.usage_history_desc', ['total' => $totalRequests]) }}</p>
                    <div class="d-flex flex-column gap-1">
                        @foreach($usageHistory as $date => $count)
                            @php
                                $maxCount = $usageHistory->max();
                                $barWidth = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:11px;color:var(--text-muted);width:40px;flex-shrink:0">{{ \Carbon\Carbon::parse($date)->format('D') }}</span>
                                <div class="progress flex-grow-1" style="height:6px;border-radius:3px;background:var(--border)">
                                    <div class="progress-bar" role="progressbar" style="width:{{ $barWidth }}%;border-radius:3px;background:var(--accent)"></div>
                                </div>
                                <span style="font-size:11px;color:var(--text-color);font-weight:600;width:30px;text-align:right">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Documentation --}}
                <div class="settings-card">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-book" style="color:var(--accent)"></i>
                        <span>{{ __('developer.api_documentation_link') }}</span>
                    </h5>
                    <p class="section-desc mt-2 mb-3">{{ __('developer.api_documentation_desc') }}</p>
                    <a href="{{ route('api.documentation') }}" class="btn btn-accent btn-custom w-100" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('general.api_documentation') }}
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Create Token Modal --}}
    <div class="modal fade" id="createTokenModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="POST" action="{{ route('account.settings.developer.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('developer.create_token') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-custom">{{ __('developer.token_name') }}</label>
                        <input type="text" name="name" class="form-custom" placeholder="{{ __('developer.token_name_placeholder') }}" required maxlength="255" autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">{{ __('developer.expires_at') }}</label>
                        <p class="text-muted small mb-1">{{ __('developer.expires_at_optional') }}</p>
                        <input type="date" name="expires_at" class="form-custom" min="{{ now()->addDay()->format('Y-m-d') }}" style="max-width:260px">
                    </div>

                    <div class="mb-2">
                        <label class="form-label-custom">{{ __('developer.select_abilities') }}</label>
                        <p class="text-muted small mb-2">{{ __('developer.select_abilities_desc') }}</p>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-sm btn-outline-accent" @click="document.querySelectorAll('.ability-check').forEach(c=>c.checked=true)">{{ __('developer.select_all') }}</button>
                            <button type="button" class="btn btn-sm btn-outline-accent" @click="document.querySelectorAll('.ability-check').forEach(c=>c.checked=false)">{{ __('developer.deselect_all') }}</button>
                        </div>
                        <div style="max-height:360px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:8px">
                            @php
                                $grouped = [];
                                foreach ($abilities as $slug => $desc) {
                                    $group = explode(':', $slug)[0] ?? $slug;
                                    $grouped[$group][$slug] = $desc;
                                }
                            @endphp
                            @foreach($grouped as $group => $groupAbilities)
                                <div class="mb-2">
                                    <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);margin-bottom:4px;padding-bottom:4px;border-bottom:1px solid var(--border)">
                                        {{ ucfirst(str_replace('-', ' ', $group)) }}
                                    </h6>
                                    <div class="row g-1">
                                        @foreach($groupAbilities as $slug => $desc)
                                            <div class="col-md-6">
                                                <label class="d-flex align-items-center gap-2 p-2 rounded" style="border:1px solid var(--border);cursor:pointer;transition:all 0.15s" title="{{ $desc }}" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor=''">
                                                    <input type="checkbox" name="abilities[]" value="{{ $slug }}" class="ability-check" checked>
                                                    <span class="small fw-medium">{{ $slug }}</span>
                                                    <i class="bi bi-info-circle text-muted ms-auto flex-shrink-0" style="font-size:0.7rem" title="{{ $desc }}"></i>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-accent" data-bs-dismiss="modal">{{ __('developer.cancel') }}</button>
                    <button type="submit" class="btn btn-accent btn-custom">{{ __('developer.generate') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Token Created Modal --}}
    <div class="modal fade" id="tokenCreatedModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0" style="padding-bottom:0">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <x-status-icon domain="general" status="success" set="bi" class="text-success" />
                        <span>{{ __('developer.token_generated') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" @click="setTimeout(() => location.reload(), 100)"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="alert" style="background:var(--danger-light, #fef2f2);border:1px solid var(--danger-border, #fecaca);border-radius:8px;padding:12px;margin-bottom:16px">
                        <div class="d-flex align-items-start gap-2">
                            <x-status-icon domain="general" status="danger" set="bi" class="flex-shrink-0" style="margin-top:2px" />
                            <div>
                                <p class="fw-semibold mb-1" style="font-size:13px;color:var(--danger)">{{ __('developer.token_one_time') }}</p>
                                <p class="mb-0 small text-muted">{{ __('developer.token_generated_desc') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label-custom">{{ __('developer.new_token_created') }}</label>
                        <div class="d-flex align-items-center gap-1" style="background:var(--bg-subtle);border:2px dashed var(--accent);border-radius:8px;padding:10px 12px">
                            <code id="tokenValueDisplay" style="font-size:12px;word-break:break-all;flex:1;background:none;border:none;padding:0" class="copy-target"></code>
                            <button class="btn btn-accent btn-custom btn-sm flex-shrink-0" @click="copyTokenValue()" id="copyTokenBtn">
                                <i class="bi bi-clipboard me-1"></i>{{ __('developer.copy_token') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-2">
                    <button type="button" class="btn btn-accent btn-custom px-4" data-bs-dismiss="modal" @click="setTimeout(() => location.reload(), 100)">
                        <i class="bi bi-check-lg me-1"></i>{{ __('general.got_it') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Rename Token Modal --}}
    <div class="modal fade" id="renameTokenModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form method="POST" class="modal-content" id="renameTokenForm">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('developer.rename_token') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-0">
                        <label class="form-label-custom">{{ __('developer.token_name') }}</label>
                        <input type="text" name="name" id="renameTokenInput" class="form-custom" required maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-accent" data-bs-dismiss="modal">{{ __('developer.cancel') }}</button>
                    <button type="submit" class="btn btn-accent btn-custom">{{ __('general.save') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Combined Token Details Modal (shows info + view token with password) --}}
    <div class="modal fade" id="tokenDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width:520px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tokenDetailsModalTitle">{{ __('developer.token_name') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="tokenDetailsBody" style="display:flex;flex-direction:column;gap:10px"></div>

                    {{-- View Token Section (separated with border) --}}
                    <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-key-fill" style="color:var(--accent);font-size:14px"></i>
                            <span style="font-size:13px;font-weight:600">{{ __('developer.view_token_title') }}</span>
                        </div>
                        <p class="small text-muted mb-2" id="detailsViewTokenDesc">{{ __('developer.view_token_desc') }}</p>
                        <div>
                            <x-password-input id="detailsViewTokenPassword" placeholder="{{ __('developer.enter_password') }}" />
                        </div>
                        <div id="detailsViewTokenError" class="text-danger small mt-1 d-none"></div>
                        <button class="btn btn-accent btn-custom btn-sm w-100 mt-2" @click="confirmDetailsViewToken()" id="detailsViewTokenBtn">
                            <span id="detailsViewTokenBtnText">{{ __('developer.view_token') }}</span>
                            <div class="spinner-border spinner-border-sm d-none" id="detailsViewTokenSpinner"></div>
                        </button>
                        <div id="detailsTokenReveal" class="d-none mt-2">
                            <div class="d-flex align-items-center gap-1" style="background:var(--bg-subtle);border:2px dashed var(--accent);border-radius:8px;padding:10px 12px">
                                <code id="detailsTokenFullDisplay" style="font-size:12px;word-break:break-all;flex:1;background:none;border:none;padding:0" class="copy-target"></code>
                                <button class="btn btn-accent btn-custom btn-sm flex-shrink-0" @click="copyDetailsToken()" id="copyDetailsTokenBtn">
                                    <i class="bi bi-clipboard me-1"></i>{{ __('developer.copy_token') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-accent btn-custom" data-bs-dismiss="modal">{{ __('general.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    let currentTokenValue = '';
    let detailsTokenTargetId = null;

    function showTokenCreated(tokenVal) {
        currentTokenValue = tokenVal;
        document.getElementById('tokenValueDisplay').textContent = tokenVal;
        new bootstrap.Modal(document.getElementById('tokenCreatedModal')).show();
    }

    function copyTokenValue() {
        const btn = document.getElementById('copyTokenBtn');
        navigator.clipboard.writeText(currentTokenValue).then(() => {
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>{{ __('general.copied') }}';
            btn.className = 'btn btn-success btn-custom btn-sm flex-shrink-0';
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-clipboard me-1"></i>{{ __('developer.copy_token') }}';
                btn.className = 'btn btn-accent btn-custom btn-sm flex-shrink-0';
            }, 2500);
        });
    }

    function copyToClipboard(btn, text) {
        navigator.clipboard.writeText(text).then(() => {
            const icon = btn.querySelector('i');
            icon.className = 'bi bi-check-lg';
            setTimeout(() => { icon.className = 'bi bi-clipboard'; }, 2000);
        });
    }

    function copyDetailsToken() {
        const btn = document.getElementById('copyDetailsTokenBtn');
        navigator.clipboard.writeText(currentTokenValue).then(() => {
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>{{ __('general.copied') }}';
            btn.className = 'btn btn-success btn-custom btn-sm flex-shrink-0';
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-clipboard me-1"></i>{{ __('developer.copy_token') }}';
                btn.className = 'btn btn-accent btn-custom btn-sm flex-shrink-0';
            }, 2500);
        });
    }

    function showTokenFullDetails(id, name, createdAt, lastUsed, abilities, expiresAt, isDeactivated) {
        detailsTokenTargetId = id;
        const titleEl = document.getElementById('tokenDetailsModalTitle');
        const bodyEl = document.getElementById('tokenDetailsBody');
        const revealEl = document.getElementById('detailsTokenReveal');
        const passwordInput = document.getElementById('detailsViewTokenPassword');
        const errorEl = document.getElementById('detailsViewTokenError');

        revealEl.classList.add('d-none');
        passwordInput.value = '';
        errorEl.classList.add('d-none');
        document.getElementById('detailsViewTokenBtnText').classList.remove('d-none');
        document.getElementById('detailsViewTokenSpinner').classList.add('d-none');
        document.getElementById('detailsViewTokenBtn').disabled = false;

        if (titleEl) titleEl.textContent = name;
        if (bodyEl) {
            const abilitiesHtml = abilities.map(a =>
                '<span class="badge" style="background:var(--bg-subtle);color:var(--text-color);font-size:11px;padding:3px 10px;border-radius:20px;font-weight:500;border:1px solid var(--border)">' + a + '</span>'
            ).join(' ');
            const statusBadge = isDeactivated
                ? '<span class="badge" style="background:var(--warning-light, #fef3c7);color:var(--warning);font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600">{{ __('developer.deactivated') }}</span>'
                : '<span class="badge" style="background:var(--accent-light, rgba(21,183,108,0.12));color:var(--accent);font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600">{{ __('general.active') }}</span>';

            bodyEl.innerHTML = `
                <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <span style="font-size:12px;color:var(--text-muted)">{{ __('developer.token_name') }}</span>
                    <span style="font-size:13px;font-weight:600">${name}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <span style="font-size:12px;color:var(--text-muted)">{{ __('developer.status') }}</span>
                    <span style="font-size:13px">${statusBadge}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <span style="font-size:12px;color:var(--text-muted)">{{ __('developer.created') }}</span>
                    <span style="font-size:13px">${createdAt}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <span style="font-size:12px;color:var(--text-muted)">{{ __('developer.expires') }}</span>
                    <span style="font-size:13px">${expiresAt}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <span style="font-size:12px;color:var(--text-muted)">{{ __('developer.last_used') }}</span>
                    <span style="font-size:13px">${lastUsed}</span>
                </div>
                <div class="py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px">{{ __('developer.abilities') }} (${abilities.length})</div>
                    <div style="display:flex;flex-wrap:wrap;gap:4px">${abilitiesHtml}</div>
                </div>
            `;
        }

        document.getElementById('detailsViewTokenDesc').textContent = isDeactivated
            ? '{{ __('developer.token_deactivated_desc') }}'
            : '{{ __('developer.view_token_desc') }}';
        if (isDeactivated) {
            document.getElementById('detailsViewTokenPassword').disabled = true;
            document.getElementById('detailsViewTokenBtn').disabled = true;
        } else {
            document.getElementById('detailsViewTokenPassword').disabled = false;
            document.getElementById('detailsViewTokenBtn').disabled = false;
        }

        new bootstrap.Modal(document.getElementById('tokenDetailsModal')).show();
    }

    function confirmDetailsViewToken() {
        const password = document.getElementById('detailsViewTokenPassword').value;
        if (!password) {
            showDetailsError('{{ __('validation.required', ['attribute' => __('developer.password')]) }}');
            return;
        }

        const btn = document.getElementById('detailsViewTokenBtn');
        const btnText = document.getElementById('detailsViewTokenBtnText');
        const spinner = document.getElementById('detailsViewTokenSpinner');
        const errorEl = document.getElementById('detailsViewTokenError');
        const revealEl = document.getElementById('detailsTokenReveal');

        btn.disabled = true;
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');
        errorEl.classList.add('d-none');
        revealEl.classList.add('d-none');

        fetch('{{ route('account.settings.developer.show', '__ID__') }}'.replace('__ID__', detailsTokenTargetId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ password: password })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');

            if (data.token) {
                currentTokenValue = data.token;
                document.getElementById('detailsTokenFullDisplay').textContent = data.token;
                revealEl.classList.remove('d-none');
            } else {
                showDetailsError(data.message || '{{ __('developer.invalid_password') }}');
            }
        })
        .catch(e => {
            btn.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');
            showDetailsError('{{ __('general.connection_failed') }}');
        });
    }

    function showDetailsError(msg) {
        const errorEl = document.getElementById('detailsViewTokenError');
        errorEl.textContent = msg;
        errorEl.classList.remove('d-none');
    }

    function showRenameModal(id, currentName) {
        const form = document.getElementById('renameTokenForm');
        form.action = '{{ route('account.settings.developer.update', '__ID__') }}'.replace('__ID__', id);
        document.getElementById('renameTokenInput').value = currentName;
        new bootstrap.Modal(document.getElementById('renameTokenModal')).show();
    }

    function testApiToken() {
        const resultEl = document.getElementById('apiTestResult');
        resultEl.className = 'mt-2';
        resultEl.innerHTML = '<div class="d-flex align-items-center gap-2 text-muted small"><div class="spinner-border spinner-border-sm"></div> {{ __('general.testing') }}</div>';
        resultEl.classList.remove('d-none');

        fetch('{{ url('/api/workspace') }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const isOk = data && !data.message && data.data;
            resultEl.innerHTML = isOk
                ? '<div class="d-flex align-items-center gap-2 small" style="color:var(--success)"><i class="bi bi-check-circle-fill"></i> {{ __('general.connection_ok') }}</div>'
                : '<div class="d-flex align-items-center gap-2 small" style="color:var(--danger)"><i class="bi bi-x-circle-fill"></i> ' + (data.message || '{{ __('general.connection_failed') }}') + '</div>';
        })
        .catch(e => {
            resultEl.innerHTML = '<div class="d-flex align-items-center gap-2 small" style="color:var(--danger)"><i class="bi bi-x-circle-fill"></i> {{ __('general.connection_failed') }}</div>';
        });
    }

    function initDeveloperPage() {
        var el = document.getElementById('new-token-value');
        if (el && el.dataset.token) {
            setTimeout(function() { showTokenCreated(el.dataset.token); }, 300);
        }
    }
    if (!window._developerNavListener) {
        document.addEventListener('livewire:navigated', initDeveloperPage);
        window._developerNavListener = true;
    }
    </script>
    @endpush
</x-app-layout>
