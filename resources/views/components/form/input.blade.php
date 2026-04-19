@props(['type' => 'text'])

{{-- text-base (1rem = 16px) prevents iOS from zooming in on focus --}}
<input
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'w-full rounded-md border border-[var(--color-border)] bg-[var(--color-surface-sunken)] px-3 py-2 text-base text-[var(--color-text)] placeholder:text-[var(--color-muted)] focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-muted)]'
    ]) }}
/>
