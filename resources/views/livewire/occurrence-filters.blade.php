<div class="p-4 space-y-5">

    <div class="flex items-center justify-between">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-[var(--color-muted)]">Filters</h2>
        <x-form.button variant="ghost" size="sm" wire:click="resetFilters">
            Reset
        </x-form.button>
    </div>

    {{-- Taxon --}}
    <div>
        <x-form.input-label for="baseName" value="Taxon" />
        <x-form.input
            id="baseName"
            wire:model.lazy="baseName"
            placeholder="e.g. Dinosauria"
        />
    </div>

    {{-- Geologic Interval --}}
    <div>
        <x-form.input-label for="interval" value="Geologic Interval" />
        <x-form.input
            id="interval"
            wire:model.lazy="interval"
            placeholder="e.g. Cretaceous"
        />
        <p class="mt-1 text-xs text-[var(--color-muted)]">Interval name as used by PBDB</p>
    </div>

    {{-- Age Range --}}
    <div>
        <x-form.label value="Age Range (Ma)" />
        <div class="grid grid-cols-2 gap-2">
            <div>
                <x-form.input-label for="minMa" value="Min" :small="true" />
                <x-form.input
                    id="minMa"
                    type="number"
                    wire:model.lazy="minMa"
                    min="0"
                    max="540"
                    step="0.1"
                    class="no-spin"
                />
            </div>
            <div>
                <x-form.input-label for="maxMa" value="Max" :small="true" />
                <x-form.input
                    id="maxMa"
                    type="number"
                    wire:model.lazy="maxMa"
                    min="0"
                    max="540"
                    step="0.1"
                    class="no-spin"
                />
            </div>
        </div>
    </div>

    {{-- Environments --}}
    <div>
        <x-form.label value="Environment" />
        <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1 mt-1">
            @foreach ($envTypeOptions as $value => $label)
                <x-form.checkbox
                    wire:model="envTypes"
                    :value="$value"
                    :label="$label"
                />
            @endforeach
        </div>
    </div>

    {{-- Country Codes --}}
    <div>
        <x-form.input-label for="countryCodes" value="Country Codes" />
        <x-form.input
            id="countryCodes"
            wire:model.lazy="countryCodes"
            placeholder="e.g. US,CA,GB"
        />
    </div>

    {{-- Identification Quality --}}
    <div>
        <x-form.input-label for="idQual" value="ID Quality" />
        <x-form.select id="idQual" wire:model="idQual">
            <option value="any">Any</option>
            <option value="certain">Certain</option>
            <option value="uncertain">Uncertain</option>
        </x-form.select>
    </div>

    {{-- Bounding Box --}}
    @if ($lngMin !== null || $lngMax !== null || $latMin !== null || $latMax !== null)
        <div class="rounded-md bg-[var(--color-accent-subtle)] border border-[var(--color-accent-muted)] p-3 text-xs space-y-1">
            <div class="flex items-center justify-between">
                <span class="font-semibold text-[var(--color-accent)]">Bounding Box</span>
                <x-form.button variant="ghost" size="sm" wire:click="clearBoundingBox">
                    &times; Clear
                </x-form.button>
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
    <x-form.button
        variant="primary"
        class="w-full"
        wire:click="applyFilters"
        wire:loading.attr="disabled"
        wire:target="applyFilters"
    >
        <span wire:loading.remove wire:target="applyFilters">Apply Filters</span>
        <span wire:loading wire:target="applyFilters" class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Loading&hellip;
        </span>
    </x-form.button>

</div>
