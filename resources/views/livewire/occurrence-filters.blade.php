<div class="p-4 space-y-5">

    <div class="flex items-center justify-between">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-[var(--color-muted)]">Filters</h2>
        <button
            wire:click="resetFilters"
            class="text-xs text-[var(--color-muted)] hover:text-[var(--color-text)] transition-colors"
        >
            Reset
        </button>
    </div>

    {{-- Taxon --}}
    <div>
        <label for="baseName" class="block text-sm font-medium mb-1">Taxon</label>
        <input
            id="baseName"
            type="text"
            wire:model.lazy="baseName"
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
            wire:model.lazy="interval"
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
                    wire:model.lazy="minMa"
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
                    wire:model.lazy="maxMa"
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
            wire:model.lazy="countryCodes"
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

    {{-- Apply button --}}
    <button
        wire:click="applyFilters"
        wire:loading.attr="disabled"
        wire:target="applyFilters"
        class="w-full rounded-md bg-[var(--color-accent)] px-4 py-2 text-sm font-semibold text-[var(--color-text-on-accent)] hover:bg-[var(--color-accent-hover)] transition-colors disabled:opacity-60"
    >
        <span wire:loading.remove wire:target="applyFilters">Apply Filters</span>
        <span wire:loading wire:target="applyFilters" class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Loading&hellip;
        </span>
    </button>

</div>