@props([
    'domain',
    'status',
    'set'   => null,
    'class' => '',
])

@php
    $result = \Edzeery\MyStatusKit\Facades\Status::for($domain, $status);
@endphp

{!! $result->icon($set, $class ? $class : null) !!}
