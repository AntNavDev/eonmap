@props([
    'for'   => null,
    'value' => null,
    'small' => false,
])

@php
$textClass = $small ? 'text-xs text-muted' : 'text-sm font-medium text-text';
@endphp

<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->merge(['class' => "block {$textClass} mb-1"]) }}
>
    {{ $value ?? $slot }}
</label>
