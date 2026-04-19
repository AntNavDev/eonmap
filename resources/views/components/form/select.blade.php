{{-- pr-10 (2.5rem) ensures the native arrow never overlaps the value --}}
{{-- text-base (1rem = 16px) prevents iOS from zooming in on focus   --}}
<select
    {{ $attributes->merge([
        'class' => 'w-full rounded-md border border-border bg-surface-sunken pl-3 pr-10 py-2 text-base text-text focus:outline-none focus:ring-2 focus:ring-accent-muted'
    ]) }}
>
    {{ $slot }}
</select>
