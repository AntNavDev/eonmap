<div x-data="occurrenceBrowser" x-on:browser-data-loaded.window="setTableData($event.detail.occurrences)">

    {{-- ─── Filter Panel (collapsible on mobile) ─────────────────────── --}}
    <div x-data="{ filtersOpen: true }">
        <div class="border-b border-border bg-surface">
            <div class="flex items-center justify-between px-4 py-2">
                <h1 class="text-sm font-semibold">Browse Occurrences</h1>
                <button
                    x-on:click="filtersOpen = !filtersOpen"
                    x-bind:aria-expanded="filtersOpen"
                    aria-controls="browser-filters"
                    class="text-xs text-muted hover:text-text transition-colors lg:hidden"
                >
                    <span x-text="filtersOpen ? 'Hide filters' : 'Show filters'"></span>
                </button>
            </div>

            <div id="browser-filters" x-show="filtersOpen" x-cloak class="border-t border-border">
                <livewire:occurrence-filters />
            </div>
        </div>
    </div>

    {{-- ─── Toolbar ────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between gap-4 px-4 py-3 border-b border-border bg-surface-raised">

        <x-pagination.pagination-bar
            :from="$from"
            :to="$to"
            :total="$total"
            :can-prev="$offset > 0"
            :can-next="$to < $total"
            :per-page="$perPage"
        />

        {{-- Export --}}
        <a
            href="{{ $exportUrl }}"
            class="rounded-md border border-border bg-surface px-3 py-1.5 text-xs font-medium hover:bg-surface-hover transition-colors"
            aria-label="Export current results as CSV"
        >
            Export CSV
        </a>

    </div>

    {{-- ─── Error state ────────────────────────────────────────────────── --}}
    @if ($loadError)
        <div class="mx-4 mt-4 rounded-md border border-danger/30 bg-danger/10 p-3 text-sm text-danger">
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
