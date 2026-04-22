@props(['placeholder' => 'Search...'])

<div class="relative">
    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
        <svg class="h-4 w-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>

    {{-- text-base (1rem = 16px) prevents iOS from zooming in on focus --}}
    <input
        type="text"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-md border border-border bg-surface-sunken py-2 pl-9 text-base text-text placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-accent-muted ' . (isset($right) ? 'pr-10' : 'pr-3'),
        ]) }}
    />

    {{-- Optional right-side controls (spinner, clear button, etc.) --}}
    @isset($right)
        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
            {{ $right }}
        </div>
    @endisset

    {{-- Optional dropdown anchored below the input --}}
    @isset($dropdown)
        {{ $dropdown }}
    @endisset
</div>