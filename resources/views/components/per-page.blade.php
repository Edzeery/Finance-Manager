@props([
    'current' => 15,
    'route' => null,
    'options' => [5, 10, 15, 20, 35, 50, 100],
    'preserve' => [],
    'name' => 'per_page',
    'pageName' => 'page',
])

<form method="GET" action="{{ $route ?? url()->current() }}" class="d-flex align-items-center gap-2" style="font-size:13px">
    @foreach(request()->except([$name, $pageName, ...$preserve]) as $key => $value)
        @if(is_array($value))
            @foreach($value as $v)
                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
    <label class="mb-0" style="color:var(--text-muted);white-space:nowrap">{{ __('general.per_page') }}:</label>
    <select name="{{ $name }}" class="form-custom" style="width:auto;padding:6px 10px;font-size:13px" onchange="this.form.submit()">
        @foreach($options as $val)
            <option value="{{ $val }}" {{ $current == $val ? 'selected' : '' }}>{{ $val }}</option>
        @endforeach
    </select>
</form>
