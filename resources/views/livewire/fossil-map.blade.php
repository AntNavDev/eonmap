<div class="grid grid-cols-[18rem_1fr] h-[calc(100vh-4rem)]">

    {{-- ─── Filter Panel ──────────────────────────────────────────────── --}}
    <aside class="flex flex-col bg-surface border-r border-border overflow-y-auto" aria-label="Filters">
        <livewire:occurrence-filters />

        {{-- Error state (set by FossilMap when the API call fails) --}}
        @if ($loadError)
            <div class="mx-4 mb-4 rounded-md border border-danger/30 bg-danger/10 p-3 text-sm text-danger">
                {{ $loadError }}
            </div>
        @endif
    </aside>

    {{-- ─── Map Container ─────────────────────────────────────────────── --}}
    <div
        x-data="fossilMap"
        x-on:occurrences-loaded.window="updateMarkers($event.detail.occurrences)"
        class="relative bg-surface-sunken"
    >
        {{-- Leaflet map --}}
        <div id="eonmap-map" class="w-full h-full" role="application" aria-label="Fossil occurrence map"></div>

        {{-- Empty state overlay — shown until the user applies filters --}}
        @unless ($hasFilters)
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-[500]">
                <div class="bg-surface/90 backdrop-blur-sm rounded-xl border border-border px-8 py-6 text-center shadow-lg">
                    <p class="text-lg font-semibold text-text">Add a filter to search</p>
                    <p class="mt-1 text-sm text-muted">Use the panel on the left to filter fossil occurrences.</p>
                </div>
            </div>
        @endunless

        {{-- Result count indicator — shown after filters are applied --}}
        @if ($hasFilters && $resultCount > 0)
            <div class="absolute bottom-8 right-3 z-[1000]">
                <div class="rounded-md border border-border bg-surface/90 backdrop-blur-sm px-3 py-1.5 text-xs text-muted shadow-sm">
                    @if ($resultTotal > $resultCount)
                        Showing {{ number_format($resultCount) }} of {{ number_format($resultTotal) }} occurrences
                    @else
                        {{ number_format($resultCount) }} {{ Str::plural('occurrence', $resultCount) }}
                    @endif
                </div>
            </div>
        @endif

        {{-- Floating map controls --}}
        <div class="absolute top-3 right-3 z-[1000] flex flex-col gap-2">

            {{-- Heatmap toggle --}}
            <button
                x-on:click="toggleHeatmap()"
                x-bind:class="heatmapMode ? 'bg-accent text-white' : 'bg-surface text-text'"
                x-bind:aria-pressed="heatmapMode"
                class="rounded-md border border-border px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-surface-hover transition-colors"
                aria-label="Toggle heatmap"
            >
                Heatmap
            </button>

            {{-- Paleocoordinate toggle --}}
            <button
                x-on:click="togglePaleoMode()"
                x-bind:class="paleoMode ? 'bg-accent text-white' : 'bg-surface text-text'"
                x-bind:aria-pressed="paleoMode"
                class="rounded-md border border-border px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-surface-hover transition-colors"
                aria-label="Toggle paleocoordinates"
            >
                Paleo
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
