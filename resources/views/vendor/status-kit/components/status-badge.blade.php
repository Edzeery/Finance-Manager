@props([
    'domain',
    'status',
    'set'   => null,
    'class' => '',
])

@php
    $result = \Edzeery\MyStatusKit\Facades\Status::for($domain, $status);
@endphp

<span {{ $attributes->merge(['class' => $result->badgeClasses($class)]) }}>
    {!! $result->icon($set) !!}
    <span>{{ $result->label() }}</span>
</span>
