@props([
    'variant' => 'primary',
    'type'    => 'button',
    'size'    => 'md',
])

@php
$variants = [
    'primary'   => 'bg-accent text-on-accent hover:bg-accent-hover',
    'secondary' => 'border border-border bg-surface text-text hover:bg-surface-hover',
    'danger'    => 'bg-danger text-on-accent hover:bg-danger-hover',
    'success'   => 'bg-success text-on-accent hover:bg-success-hover',
    'ghost'     => 'bg-transparent text-muted hover:text-text hover:bg-surface-hover',
];

$sizes = [
    'sm' => 'px-2.5 py-1 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$variantClass = $variants[$variant] ?? $variants['primary'];
$sizeClass    = $sizes[$size] ?? $sizes['md'];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "inline-flex items-center justify-center gap-2 rounded-md font-semibold transition-colors disabled:opacity-60 disabled:cursor-not-allowed {$variantClass} {$sizeClass}"
    ]) }}
>
    {{ $slot }}
</button>
