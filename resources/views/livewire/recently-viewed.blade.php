{{--
    NOTE: The dropdown's Alpine `open` state will reset to false on Livewire re-renders
    (e.g., when new items are added). This is acceptable UX for a recently-viewed list.
    If this becomes a problem, wrap the dropdown in wire:ignore and refresh items via JS.
--}}
<x-ui.dropdown>
    <x-slot:trigger>
        <button
            class="flex items-center gap-1 text-sm font-medium text-[var(--color-muted)] hover:text-[var(--color-text)] transition-colors"
            aria-haspopup="true"
            :aria-expanded="open"
        >
            Recently Viewed
            <svg
                class="h-3 w-3 transition-transform"
                :class="{ 'rotate-180': open }"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </x-slot:trigger>

    @if (count($items) === 0)
        <p class="px-4 py-3 text-sm text-[var(--color-muted)]">Nothing viewed yet.</p>
    @else
        @foreach ($items as $item)
            <x-ui.dropdown-link href="/occurrences/{{ $item['occurrence_no'] }}">
                {{ $item['name'] }}
                <span class="text-xs text-[var(--color-muted)]">(#{{ $item['occurrence_no'] }})</span>
            </x-ui.dropdown-link>
        @endforeach
    @endif
</x-ui.dropdown>
