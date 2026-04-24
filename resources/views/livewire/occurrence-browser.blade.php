<div
    x-data="occurrenceBrowser"
    x-on:browser-data-loaded.window="setTableData($event.detail.occurrences)"
    class="grid lg:grid-cols-[18rem_1fr] lg:h-[calc(100vh-4rem)]"
>

    {{-- ─── Table + Toolbar ───────────────────────────────────────────────── --}}
    {{-- order-first puts this above the filter panel on mobile               --}}
    <div class="order-first lg:order-last flex flex-col lg:min-h-0">

        {{-- Toolbar --}}
        <div class="flex items-center justify-between gap-4 px-4 py-3 border-b border-border bg-surface-raised shrink-0">
            <div class="flex flex-col gap-1">
                <x-pagination.pagination-bar
                    :from="$from"
                    :to="$to"
                    :total="$total"
                    :can-prev="$offset > 0"
                    :can-next="$hasMore"
                    :per-page="$perPage"
                    :show-total="false"
                />
                @if ($from > 0)
                    <p class="text-xs text-muted/70">The PBDB API does not provide a total count. Results load {{ $perPage }} at a time.</p>
                @endif
            </div>

            <a
                href="{{ $exportUrl }}"
                class="rounded-md border border-border bg-surface px-3 py-1.5 text-xs font-medium hover:bg-surface-hover transition-colors shrink-0"
                aria-label="Export current results as CSV"
            >
                Export CSV
            </a>
        </div>

        {{-- Error state --}}
        @if ($loadError)
            <div class="mx-4 mt-4 rounded-md border border-danger/30 bg-danger/10 p-3 text-sm text-danger shrink-0">
                {{ $loadError }}
            </div>
        @endif

        {{-- Tabulator table — flex-1 fills remaining column height on desktop --}}
        <div
            id="eonmap-browser-table"
            class="w-full lg:flex-1 lg:overflow-auto"
            wire:ignore
        ></div>

    </div>

    {{-- ─── Filter Panel ───────────────────────────────────────────────────── --}}
    {{-- order-last puts this below the table on mobile, left column on desktop --}}
    <aside
        x-data="{ filtersOpen: true }"
        class="order-last lg:order-first border-t lg:border-t-0 lg:border-r border-border bg-surface flex flex-col lg:overflow-hidden"
        aria-label="Filters"
    >
        {{-- Panel header --}}
        <div class="flex items-center justify-between px-4 py-2 border-b border-border shrink-0">
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

        <div
            id="browser-filters"
            x-show="filtersOpen"
            x-cloak
            class="flex flex-col flex-1 min-h-0 overflow-hidden"
        >
            <livewire:occurrence-filters />
        </div>
    </aside>

</div>