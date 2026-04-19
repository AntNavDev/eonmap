<a
    {{ $attributes->merge([
        'class' => 'block truncate px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface-sunken)] transition-colors'
    ]) }}
    role="menuitem"
>
    {{ $slot }}
</a>
