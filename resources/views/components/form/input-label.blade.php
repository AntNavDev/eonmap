@props([
    'for'   => null,
    'value' => null,
    'small' => false,
])

@php
$textClass = $small
    ? 'text-xs text-[var(--color-muted)]'
    : 'text-sm font-medium text-[var(--color-text)]';
@endphp

<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->merge(['class' => "block {$textClass} mb-1"]) }}
>
    {{ $value ?? $slot }}
</label>
