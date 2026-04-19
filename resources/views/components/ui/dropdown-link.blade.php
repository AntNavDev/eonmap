<a
    {{ $attributes->merge([
        'class' => 'block truncate px-4 py-2 text-sm text-text hover:bg-surface-sunken transition-colors'
    ]) }}
    role="menuitem"
>
    {{ $slot }}
</a>
