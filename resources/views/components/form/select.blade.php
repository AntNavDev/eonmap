{{-- pr-10 (2.5rem) ensures the native arrow never overlaps the value --}}
{{-- text-base (1rem = 16px) prevents iOS from zooming in on focus   --}}
<select
    {{ $attributes->merge([
        'class' => 'w-full rounded-md border border-[var(--color-border)] bg-[var(--color-surface-sunken)] pl-3 pr-10 py-2 text-base text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-muted)]'
    ]) }}
>
    {{ $slot }}
</select>
