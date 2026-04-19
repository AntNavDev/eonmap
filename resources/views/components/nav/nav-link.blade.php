@props(['href' => '/', 'active' => false])

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'text-sm font-medium transition-colors ' . ($active
            ? 'text-[var(--color-accent)]'
            : 'text-[var(--color-muted)] hover:text-[var(--color-text)]')
    ]) }}
>
    {{ $slot }}
</a>
