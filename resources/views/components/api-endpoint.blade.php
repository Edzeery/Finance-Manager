<div class="endpoint-card">
    <div class="endpoint-card-header" role="button">
        <span class="http-method {{ strtolower($method) }}">{{ $method }}</span>
        <span class="endpoint-url">{{ $endpoint }}</span>
        <span class="endpoint-desc" style="margin-left:auto;font-size:0.8rem;color:var(--text-muted,#6b7280);display:block;text-align:right;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:250px">{{ $desc ?? '' }}</span>
        <i class="bi bi-chevron-down" style="flex-shrink:0;color:var(--text-muted,#6b7280);transition:transform 0.2s"></i>
    </div>
    <div class="endpoint-card-body">
        @if(!empty($ability) && $ability !== 'none')
            <div style="margin-bottom:0.75rem;font-size:0.8125rem;color:var(--text-muted,#6b7280)">
                <strong>{{ __('api-docs.with_abilities') }}</strong>
                <span class="ability-tag">{{ $ability }}</span>
            </div>
        @endif
        {{ $slot }}
    </div>
</div>
