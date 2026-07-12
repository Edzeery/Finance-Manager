@php
    $languages = ['php', 'curl', 'python', 'javascript', 'ruby'];
    $langNames = [
        'php' => __('api-docs.php'),
        'curl' => __('api-docs.curl'),
        'python' => __('api-docs.python'),
        'javascript' => __('api-docs.javascript'),
        'ruby' => __('api-docs.ruby'),
    ];
@endphp

<div class="code-block">
    <div class="code-tabs" role="tablist">
        @foreach($languages as $lang)
            @if(isset($examples[$lang]))
                <button type="button" class="code-tab {{ $loop->first ? 'active' : '' }}" data-lang="{{ $lang }}">{{ $langNames[$lang] }}</button>
            @endif
        @endforeach
    </div>

    @foreach($languages as $lang)
        @if(isset($examples[$lang]))
            <div class="code-pre" data-lang="{{ $lang }}" dir="ltr" style="{{ $loop->first ? 'display:block' : 'display:none' }}">
                <div class="code-block-wrapper">
                    <button type="button" class="copy-btn">{{ __('api-docs.copy_code') }}</button>
                    <pre><code>{{ $examples[$lang] }}</code></pre>
                </div>
            </div>
        @endif
    @endforeach

    @if(isset($response))
        <details style="margin-top:0.5rem">
            <summary style="cursor:pointer;font-size:0.8125rem;color:var(--text-muted,#6b7280);padding:0.25rem 0">
                {{ __('api-docs.response_example') }}
            </summary>
            <div class="code-block-wrapper" style="margin-top:0.5rem" dir="ltr">
                <pre><code>{{ $response }}</code></pre>
            </div>
        </details>
    @endif
</div>
