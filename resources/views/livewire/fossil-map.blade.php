<div class="grid grid-cols-[18rem_1fr] h-[calc(100vh-4rem)]">

    {{-- ─── Filter Panel ──────────────────────────────────────────────── --}}
    <aside class="flex flex-col bg-[var(--color-surface)] border-r border-[var(--color-border)] overflow-y-auto">
        <div class="p-4 space-y-5">

            <h2 class="text-xs font-semibold uppercase tracking-widest text-[var(--color-muted)]">Filters</h2>

            {{-- Taxon --}}
            <div>
                <label for="baseName" class="block text-sm font-medium mb-1">Taxon</label>
                <input
                    id="baseName"
                    type="text"
                    wire:model="baseName"
                    placeholder="e.g. Dinosauria"
                    class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-surface-sunken)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-muted)]"
                />
            </div>

            {{-- Geologic Interval --}}
            <div>
                <label for="interval" class="block text-sm font-medium mb-1">Geologic Interval</label>
                <input
                    id="interval"
                    type="text"
                    wire:model="interval"
                    placeholder="e.g. Cretaceous"
                    class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-surface-sunken)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-muted)]"
                />
                <p class="mt-1 text-xs text-[var(--color-muted)]">Interval name as used by PBDB</p>
            </div>

            {{-- Age Range --}}
            <div>
                <span class="block text-sm font-medium mb-1">Age Range (Ma)</span>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="minMa" class="block text-xs text-[var(--color-muted)] mb-1">Min</label>
                        <input
                            id="minMa"
                            type="number"
                            wire:model="minMa"
                            min="0"
                            max="540"
                            step="0.1"
                            class="no-spin w-full rounded-md border border-[var(--color-border)] bg-[var(--color-surface-sunken)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-muted)]"
                        />
                    </div>
                    <div>
                        <label for="maxMa" class="block text-xs text-[var(--color-muted)] mb-1">Max</label>
                        <input
                            id="maxMa"
                            type="number"
                            wire:model="maxMa"
                            min="0"
                            max="540"
                            step="0.1"
                            class="no-spin w-full rounded-md border border-[var(--color-border)] bg-[var(--color-surface-sunken)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-muted)]"
                        />
                    </div>
                </div>
            </div>

            {{-- Environments --}}
            <div>
                <span class="block text-sm font-medium mb-2">Environment</span>
                <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                    @foreach ($envTypeOptions as $value => $label)
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input
                                type="checkbox"
                                wire:model="envTypes"
                                value="{{ $value }}"
                                class="rounded border-[var(--color-border)] text-[var(--color-accent)]"
                            />
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Country Codes --}}
            <div>
                <label for="countryCodes" class="block text-sm font-medium mb-1">Country Codes</label>
                <input
                    id="countryCodes"
                    type="text"
                    wire:model="countryCodes"
                    placeholder="e.g. US,CA,GB"
                    class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-surface-sunken)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-muted)]"
                />
            </div>

            {{-- Identification Quality --}}
            <div>
                <label for="idQual" class="block text-sm font-medium mb-1">ID Quality</label>
                <select
                    id="idQual"
                    wire:model="idQual"
                    class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-surface-sunken)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-muted)]"
                >
                    <option value="any">Any</option>
                    <option value="certain">Certain</option>
                    <option value="uncertain">Uncertain</option>
                </select>
            </div>

            {{-- Bounding Box --}}
            @if ($lngMin !== null || $lngMax !== null || $latMin !== null || $latMax !== null)
                <div class="rounded-md bg-[var(--color-accent-subtle)] border border-[var(--color-accent-muted)] p-3 text-xs space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-[var(--color-accent)]">Bounding Box</span>
                        <button
                            wire:click="clearBoundingBox"
                            class="text-[var(--color-muted)] hover:text-[var(--color-danger)] transition-colors"
                        >
                            &times; Clear
                        </button>
                    </div>
                    <p>Lng: {{ number_format($lngMin ?? 0, 3) }} &rarr; {{ number_format($lngMax ?? 0, 3) }}</p>
                    <p>Lat: {{ number_format($latMin ?? 0, 3) }} &rarr; {{ number_format($latMax ?? 0, 3) }}</p>
                </div>
            @else
                <p class="text-xs text-[var(--color-muted)]">
                    Use the rectangle tool on the map to set a bounding box.
                </p>
            @endif

            {{-- Error message --}}
            @if ($loadError)
                <div class="rounded-md border border-[var(--color-danger)]/30 bg-[var(--color-danger)]/10 p-3 text-sm text-[var(--color-danger)]">
                    {{ $loadError }}
                </div>
            @endif

            {{-- Apply Filters button --}}
            <button
                wire:click="loadOccurrences"
                wire:loading.attr="disabled"
                class="w-full rounded-md bg-[var(--color-accent)] px-4 py-2 text-sm font-semibold text-[var(--color-text-on-accent)] hover:bg-[var(--color-accent-hover)] transition-colors disabled:opacity-60"
            >
                <span wire:loading.remove>Apply Filters</span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Loading&hellip;
                </span>
            </button>

        </div>
    </aside>

    {{-- ─── Map Container ─────────────────────────────────────────────── --}}
    <div
        x-data="fossilMap"
        x-on:occurrences-loaded.window="updateMarkers($event.detail.occurrences)"
        class="relative bg-[var(--color-surface-sunken)]"
    >
        {{-- Leaflet map --}}
        <div id="eonmap-map" class="w-full h-full"></div>

        {{-- Empty state overlay — shown when no filters are set --}}
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