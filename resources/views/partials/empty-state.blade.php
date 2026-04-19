@props(['message' => 'No results found.', 'detail' => null])

<div class="flex flex-col items-center justify-center py-16 text-center">
    <svg
        class="mb-4 h-12 w-12 text-muted"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="1.5"
        aria-hidden="true"
    >
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>

    <p class="text-sm font-medium text-text">{{ $message }}</p>

    @if ($detail)
        <p class="mt-2 text-sm text-muted">{{ $detail }}</p>
    @endif
</div>
