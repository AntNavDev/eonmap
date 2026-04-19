<div
    x-data="{ open: false }"
    class="relative"
    @click.outside="open = false"
>
    <button
        @click="open = !open"
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

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-64 rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] py-1 shadow-lg"
        role="menu"
    >
        @if (count($items) === 0)
            <p class="px-4 py-3 text-sm text-[var(--color-muted)]">Nothing viewed yet.</p>
        @else
            @foreach ($items as $item)
                <a
                    href="/occurrences/{{ $item['occurrence_no'] }}"
                    class="block truncate px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface-sunken)] transition-colors"
                    role="menuitem"
                >
                    {{ $item['name'] }}
                    <span class="text-xs text-[var(--color-muted)]">(#{{ $item['occurrence_no'] }})</span>
                </a>
            @endforeach
        @endif
    </div>
</div>