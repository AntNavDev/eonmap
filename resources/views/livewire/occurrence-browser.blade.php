<div x-data="occurrenceBrowser" x-on:browser-data-loaded.window="setTableData($event.detail.occurrences)">

    {{-- ─── Filter Panel (collapsible on mobile) ─────────────────────── --}}
    <div x-data="{ filtersOpen: true }">
        <div class="border-b border-[var(--color-border)] bg-[var(--color-surface)]">
            <div class="flex items-center justify-between px-4 py-2">
                <h1 class="text-sm font-semibold">Browse Occurrences</h1>
                <button
                    x-on:click="filtersOpen = !filtersOpen"
                    class="text-xs text-[var(--color-muted)] hover:text-[var(--color-text)] transition-colors lg:hidden"
                >
                    <span x-text="filtersOpen ? 'Hide filters' : 'Show filters'"></span>
                </button>
            </div>

            <div x-show="filtersOpen" x-cloak class="border-t border-[var(--color-border)]">
                <livewire:occurrence-filters />
            </div>
        </div>
    </div>

    {{-- ─── Toolbar ────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between gap-4 px-4 py-3 border-b border-[var(--color-border)] bg-[var(--color-surface-raised)]">
        {{-- Pagination --}}
        <div class="flex items-center gap-3">
            <button
                wire:click="prevPage"
                @disabled($offset <= 0)
                class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] px-3 py-1.5 text-xs font-medium hover:bg-[var(--color-surface-hover)] disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
            >
                &larr; Prev
            </button>

            <span class="text-xs text-[var(--color-muted)]">
                @if ($total > 0)
                    Showing {{ $from }}&ndash;{{ $to }} of {{ number_format($total) }}
                @else
                    No results
                @endif
            </span>

            <button
                wire:click="nextPage"
                @disabled($to >= $total)
                class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] px-3 py-1.5 text-xs font-medium hover:bg-[var(--color-surface-hover)] disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
            >
                Next &rarr;
            </button>
        </div>

        {{-- Export --}}
        <a
            href="{{ $exportUrl }}"
            class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] px-3 py-1.5 text-xs font-medium hover:bg-[var(--color-surface-hover)] transition-colors"
        >
            Export CSV
        </a>
    </div>

    {{-- ─── Error state ────────────────────────────────────────────────── --}}
    @if ($loadError)
        <div class="mx-4 mt-4 rounded-md border border-[var(--color-danger)]/30 bg-[var(--color-danger)]/10 p-3 text-sm text-[var(--color-danger)]">
            {{ $loadError }}
        </div>
    @endif

    {{-- ─── Tabulator table ────────────────────────────────────────────── --}}
    <div
        id="eonmap-browser-table"
        class="w-full"
        wire:ignore
    ></div>

</div>