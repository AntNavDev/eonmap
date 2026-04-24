<div class="flex flex-col flex-1 min-h-0" x-data="{ activePreset: null }">
<div class="flex-1 overflow-y-auto p-4 space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-muted">Filters</h2>
        <x-form.button
            variant="ghost"
            size="sm"
            wire:click="resetFilters"
            x-on:click="activePreset = null"
        >
            Reset
        </x-form.button>
    </div>
    <p class="text-xs text-muted -mt-3">Select an organism or time period to search. Use the filters below to refine.</p>

    {{-- Quick Start Presets --}}
    <div>
        <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-muted">Quick Start</p>
        <div class="grid grid-cols-2 gap-1.5">
            @foreach ($presets as $preset)
                <button
                    type="button"
                    wire:click="loadPreset('{{ $preset->id }}')"
                    wire:loading.attr="disabled"
                    x-on:click="activePreset = '{{ $preset->id }}'"
                    title="{{ $preset->description }}"
                    :class="activePreset === '{{ $preset->id }}'
                        ? 'border-accent bg-accent-subtle text-accent'
                        : 'border-border bg-surface text-text hover:bg-surface-hover'"
                    class="flex flex-col items-start gap-0.5 rounded-md border px-2.5 py-2 text-left text-xs transition-colors disabled:opacity-60"
                >
                    <span class="text-base leading-none">{{ $preset->emoji }}</span>
                    <span class="font-medium leading-tight">{{ $preset->name }}</span>
                </button>
            @endforeach
        </div>
        <p class="mt-1.5 text-xs text-muted">Hover a card for a description.</p>
    </div>

    <div class="border-t border-border"></div>

    {{-- Organism --}}
    <div>
        <x-form.input-label for="baseName" value="Organism" />
        @if (! $customTaxon)
            <x-form.select id="baseName" wire:model="baseName">
                @foreach ($taxonOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </x-form.select>
        @else
            <x-form.input
                id="baseName"
                wire:model.lazy="baseName"
                placeholder="e.g. Allosauridae, Pterosauria"
            />
        @endif
        <x-form.mode-toggle
            :is-custom="$customTaxon"
            default-label="Select from list"
            custom-label="Enter name"
            on-default="disableCustomTaxon"
            on-custom="enableCustomTaxon"
        />
    </div>

    {{-- Time Period --}}
    <div>
        <x-form.input-label for="interval" value="Time Period" />
        @if (! $customInterval)
            <x-form.select id="interval" wire:model="interval">
                @foreach ($intervalOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </x-form.select>
        @else
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <x-form.input-label for="minMa" value="Min (Mya)" :small="true" />
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
                    <x-form.input-label for="maxMa" value="Max (Mya)" :small="true" />
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
        @endif
        <x-form.mode-toggle
            :is-custom="$customInterval"
            default-label="Named period"
            custom-label="Custom range"
            on-default="disableCustomInterval"
            on-custom="enableCustomInterval"
        />
    </div>

    <div class="border-t border-border"></div>
    <p class="text-xs font-semibold uppercase tracking-widest text-muted -mt-1">Refine</p>

    {{-- Environment --}}
    <div>
        <x-form.label value="Environment" />
        <div class="mt-1 max-h-40 space-y-1.5 overflow-y-auto pr-1">
            @foreach ($envTypeOptions as $value => $label)
                <x-form.checkbox
                    wire:model="envTypes"
                    :value="$value"
                    :label="$label"
                />
            @endforeach
        </div>
    </div>

    {{-- Country --}}
    <div>
        <x-form.input-label for="countryCodes" value="Country" />
        <x-form.select id="countryCodes" wire:model="countryCodes">
            @foreach ($countryOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-form.select>
    </div>

    {{-- Identification Quality --}}
    <div>
        <x-form.input-label for="idQual" value="ID Quality" />
        <x-form.select id="idQual" wire:model="idQual">
            <option value="any">Any</option>
            <option value="certain">Certain only</option>
            <option value="uncertain">Uncertain only</option>
        </x-form.select>
    </div>


</div>

    {{-- Sticky Apply footer --}}
    <div class="shrink-0 border-t border-border p-4">
        @if ($validationError)
            <p class="mb-3 rounded-md border border-danger/30 bg-danger/10 px-3 py-2 text-xs text-danger">
                {{ $validationError }}
            </p>
        @endif
        <x-form.button
            variant="primary"
            class="w-full"
            wire:click="applyFilters"
            wire:loading.attr="disabled"
            wire:target="applyFilters"
        >
            <span wire:loading.remove wire:target="applyFilters">Apply Filters</span>
            <span wire:loading wire:target="applyFilters" class="flex items-center justify-center gap-2">
                <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Loading&hellip;
            </span>
        </x-form.button>
    </div>

</div>