<div class="grid grid-cols-[18rem_1fr] h-[calc(100vh-4rem)]">

    {{-- ─── Filter Panel ──────────────────────────────────────────────── --}}
    <aside class="flex flex-col bg-[var(--color-surface)] border-r border-[var(--color-border)] overflow-y-auto">
        <livewire:occurrence-filters />

        {{-- Error state (set by FossilMap when the API call fails) --}}
        @if ($loadError)
            <div class="mx-4 mb-4 rounded-md border border-[var(--color-danger)]/30 bg-[var(--color-danger)]/10 p-3 text-sm text-[var(--color-danger)]">
                {{ $loadError }}
            </div>
        @endif
    </aside>

    {{-- ─── Map Container ─────────────────────────────────────────────── --}}
    <div
        x-data="fossilMap"
        x-on:occurrences-loaded.window="updateMarkers($event.detail.occurrences)"
        class="relative bg-[var(--color-surface-sunken)]"
    >
        {{-- Leaflet map --}}
        <div id="eonmap-map" class="w-full h-full"></div>

        {{-- Empty state overlay — shown until the user applies filters --}}
        @unless ($hasFilters)
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-[500]">
                <div class="bg-[var(--color-surface)]/90 backdrop-blur-sm rounded-xl border border-[var(--color-border)] px-8 py-6 text-center shadow-lg">
                    <p class="text-lg font-semibold text-[var(--color-text)]">Add a filter to search</p>
                    <p class="mt-1 text-sm text-[var(--color-muted)]">Use the panel on the left to filter fossil occurrences.</p>
                </div>
            </div>
        @endunless

        {{-- Floating map controls --}}
        <div class="absolute top-3 right-3 z-[1000] flex flex-col gap-2">

            {{-- Heatmap toggle --}}
            <button
                x-on:click="toggleHeatmap()"
                x-bind:class="heatmapMode
                    ? 'bg-[var(--color-accent)] text-[var(--color-text-on-accent)]'
                    : 'bg-[var(--color-surface)] text-[var(--color-text)]'"
                class="rounded-md border border-[var(--color-border)] px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-[var(--color-surface-hover)] transition-colors"
                title="Toggle heatmap"
            >
                Heatmap
            </button>

            {{-- Paleocoordinate toggle --}}
            <button
                x-on:click="togglePaleoMode()"
                x-bind:class="paleoMode
                    ? 'bg-[var(--color-accent)] text-[var(--color-text-on-accent)]'
                    : 'bg-[var(--color-surface)] text-[var(--color-text)]'"
                class="rounded-md border border-[var(--color-border)] px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-[var(--color-surface-hover)] transition-colors"
                title="Toggle paleocoordinates"
            >
                Paleo
            </button>

            {{-- Basemap switcher --}}
            <div class="relative" x-data="{ open: false }">
                <button
                    x-on:click="open = !open"
                    class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-[var(--color-surface-hover)] transition-colors"
                >
                    Basemap
                </button>
                <div
                    x-show="open"
                    x-on:click.outside="open = false"
                    class="absolute right-0 mt-1 w-36 rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] shadow-lg overflow-hidden"
                >
                    <button x-on:click="switchBasemap('osm'); open = false"   class="block w-full px-3 py-2 text-left text-xs hover:bg-[var(--color-surface-hover)]">OSM</button>
                    <button x-on:click="switchBasemap('esri'); open = false"  class="block w-full px-3 py-2 text-left text-xs hover:bg-[var(--color-surface-hover)]">Esri Imagery</button>
                    <button x-on:click="switchBasemap('carto'); open = false" class="block w-full px-3 py-2 text-left text-xs hover:bg-[var(--color-surface-hover)]">CartoDB Dark</button>
                </div>
            </div>

        </div>
    </div>

</div>