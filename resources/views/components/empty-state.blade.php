<div class="empty-state">
    <i class="{{ $icon }}"></i>
    <h4>{{ $title ?? __('general.no_data') }}</h4>
    <p>{{ $message ?? '' }}</p>
    @if(isset($action) && isset($actionText))
        <a href="{{ $action }}" class="btn btn-accent btn-custom">
            <i class="bi bi-plus-lg"></i>{{ $actionText }}
        </a>
    @endif
</div>
