@props(['type' => 'text'])

{{-- text-base (1rem = 16px) prevents iOS from zooming in on focus --}}
<input
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'w-full rounded-md border border-border bg-surface-sunken px-3 py-2 text-base text-text placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-accent-muted'
    ]) }}
/>
