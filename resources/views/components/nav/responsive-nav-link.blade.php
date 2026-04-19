@props(['href' => '/', 'active' => false])

{{-- Block-level nav link for mobile/hamburger menus --}}
<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'block px-4 py-3 text-sm font-medium transition-colors ' . ($active
            ? 'text-accent bg-accent-subtle'
            : 'text-muted hover:text-text hover:bg-surface-hover')
    ]) }}
>
    {{ $slot }}
</a>
