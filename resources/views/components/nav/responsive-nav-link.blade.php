@props(['href' => '/', 'active' => false])

{{-- Block-level nav link for mobile/hamburger menus --}}
<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'block px-4 py-3 text-sm font-medium transition-colors ' . ($active
            ? 'text-[var(--color-accent)] bg-[var(--color-accent-subtle)]'
            : 'text-[var(--color-muted)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]')
    ]) }}
>
    {{ $slot }}
</a>
