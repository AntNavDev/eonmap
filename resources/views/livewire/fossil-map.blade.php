<div class="grid grid-cols-[18rem_1fr] h-[calc(100vh-4rem)]">

    {{-- ─── Filter Panel ──────────────────────────────────────────────── --}}
    <aside class="relative flex flex-col bg-surface border-r border-border overflow-hidden" aria-label="Filters">
        <livewire:occurrence-filters />

        {{-- Error state (set by FossilMap when the API call fails) --}}
        @if ($loadError)
            <div class="mx-4 mb-4 rounded-md border border-danger/30 bg-danger/10 p-3 text-sm text-danger">
                {{ $loadError }}
            </div>
        @endif

        {{-- Loading overlay — covers the filter panel while the API call is in flight --}}
        <div
            wire:loading
            class="absolute inset-0 z-10 bg-surface/80 backdrop-blur-sm"
        >
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-3">
                <svg class="h-7 w-7 animate-spin text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p class="text-sm font-medium text-muted">Searching&hellip;</p>
            </div>
        </div>
    </aside>

    {{-- ─── Map Container ─────────────────────────────────────────────── --}}
    {{--
        wire:ignore prevents Livewire's DOM morphing from touching this subtree.
        Without it, each re-render (to update result counts) replaces #eonmap-map
        with a fresh empty div, leaving Leaflet attached to the old detached node
        and causing "can't access property offsetWidth, e is null" on any click.
        Overlays use $wire.* (reactive Alpine → Livewire bridge) instead of
        Blade conditionals so they still respond to server state without morphing.
    --}}
    <div
        x-data="fossilMap"
        x-on:occurrences-loaded.window="updateMarkers($event.detail.occurrences)"
        wire:ignore
        class="relative bg-surface-sunken"
    >
        {{-- Leaflet map --}}
        <div id="eonmap-map" class="w-full h-full" role="application" aria-label="Fossil occurrence map"></div>

        {{-- Empty state overlay — shown until the user applies filters --}}
        <div
            x-show="!$wire.filtersApplied"
            class="absolute inset-0 flex items-center justify-center pointer-events-none z-[500]"
        >
            <div class="bg-surface/90 backdrop-blur-sm rounded-xl border border-border px-8 py-6 text-center shadow-lg">
                <p class="text-lg font-semibold text-text">Add a filter to search</p>
                <p class="mt-1 text-sm text-muted">Use the panel on the left to filter fossil occurrences.</p>
            </div>
        </div>

        {{-- Zero results overlay — shown when a search completes with no matches --}}
        <div
            x-show="$wire.filtersApplied && $wire.resultCount === 0 && !$wire.loadError"
            class="absolute inset-0 flex items-center justify-center pointer-events-none z-[500]"
        >
            <div class="bg-surface/90 backdrop-blur-sm rounded-xl border border-border px-8 py-6 text-center shadow-lg">
                <p class="text-lg font-semibold text-text">No occurrences found</p>
                <p class="mt-1 text-sm text-muted">Try a different organism, time period, or remove some filters.</p>
            </div>
        </div>

        {{-- Result count indicator — shown after filters are applied --}}
        <div
            x-show="$wire.filtersApplied && $wire.resultCount > 0"
            class="absolute bottom-8 right-3 z-[1000]"
        >
            <div class="rounded-md border border-border bg-surface/90 backdrop-blur-sm px-3 py-1.5 text-xs text-muted shadow-sm">
                <span x-text="
                    $wire.resultTotal > $wire.resultCount
                        ? 'Showing ' + $wire.resultCount.toLocaleString() + ' of ' + $wire.resultTotal.toLocaleString() + ' occurrences'
                        : $wire.resultCount.toLocaleString() + ' ' + ($wire.resultCount === 1 ? 'occurrence' : 'occurrences')
                "></span>
                <p
                    x-show="$wire.resultCount >= 500"
                    class="mt-0.5 text-muted/70"
                >This application limits results to 500 occurrences at a time.</p>
            </div>
        </div>

        {{-- Floating map controls --}}
        <div class="absolute top-3 right-3 z-[1000] flex flex-col gap-2">

            {{-- Heatmap toggle --}}
            <button
                x-on:click="toggleHeatmap()"
                x-bind:class="heatmapMode ? 'bg-accent text-on-accent' : 'bg-surface text-text'"
                x-bind:aria-pressed="heatmapMode"
                class="rounded-md border border-border px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-surface-hover transition-colors"
                aria-label="Toggle heatmap"
            >
                Heatmap
            </button>

            {{-- Basemap switcher --}}
            <x-ui.dropdown align="right" width="sm">
                <x-slot:trigger>
                    <button
                        class="w-full rounded-md border border-border bg-surface px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-surface-hover transition-colors"
                        aria-label="Switch basemap"
                    >
                        Basemap
                    </button>
                </x-slot:trigger>

                <button x-on:click="switchBasemap('osm'); open = false"   class="block w-full px-3 py-2 text-left text-xs text-text hover:bg-surface-hover">OSM</button>
                <button x-on:click="switchBasemap('esri'); open = false"  class="block w-full px-3 py-2 text-left text-xs text-text hover:bg-surface-hover">Esri Imagery</button>
                <button x-on:click="switchBasemap('carto'); open = false" class="block w-full px-3 py-2 text-left text-xs text-text hover:bg-surface-hover">CartoDB Dark</button>
            </x-ui.dropdown>

        </div>
    </div>

</div>
