<div class="empty-state">
    <i class="{{ $icon }}"></i>
    <h4>{{ $title ?? __('general.no_data') }}</h4>
    <p>{{ $message ?? '' }}</p>
    @if(isset($action) && isset($actionText))
        <x-button href="{{ $action }}" icon="bi bi-plus-lg">{{ $actionText }}</x-button>
    @endif
</div>
