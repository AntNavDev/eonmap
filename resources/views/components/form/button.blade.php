@props([
    'variant' => 'primary',
    'type'    => 'button',
    'size'    => 'md',
])

@php
$variants = [
    'primary'   => 'bg-[var(--color-accent)] text-[var(--color-text-on-accent)] hover:bg-[var(--color-accent-hover)]',
    'secondary' => 'border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]',
    'danger'    => 'bg-[var(--color-danger)] text-white hover:bg-[var(--color-danger-hover)]',
    'success'   => 'bg-[var(--color-success)] text-white hover:bg-[var(--color-success-hover)]',
    'ghost'     => 'bg-transparent text-[var(--color-muted)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]',
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
