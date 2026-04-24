<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Api\Queries\OccurrenceQuery;
use App\Presets\SearchPreset;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class OccurrenceFilters extends Component
{
    public string $baseName = '';

    public string $interval = '';

    public float $minMa = 0;

    public float $maxMa = 540;

    /** @var string[] */
    public array $envTypes = [];

    public string $countryCodes = '';

    public string $idQual = 'any';

    public ?float $lngMin = null;

    public ?float $lngMax = null;

    public ?float $latMin = null;

    public ?float $latMax = null;

    /**
     * When true, shows a free-text input for baseName instead of the curated select.
     */
    public bool $customTaxon = false;

    /**
     * All PBDB envtype values available for filtering.
     *
     * @return array<string, string>
     */
    public static function envTypeOptions(): array
    {
        return [
            'terr' => 'Terrestrial',
            'marine' => 'Marine',
            'carbonate' => 'Carbonate',
            'siliciclastic' => 'Siliciclastic',
            'reef' => 'Reef / bioherm',
            'lacustrine' => 'Lacustrine',
            'fluvial' => 'Fluvial',
            'deltaic' => 'Deltaic',
            'estuary' => 'Estuary / bay',
            'paralic' => 'Paralic',
            'peritidal' => 'Peritidal',
            'offshore' => 'Offshore',
            'coastal' => 'Coastal',
        ];
    }

    /**
     * Curated organism groups shown in the taxon select.
     * Keys are PBDB base_name values; values are human-readable labels.
     *
     * @return array<string, string>
     */
    public static function taxonOptions(): array
    {
        return [
            '' => 'All organisms',
            'Dinosauria' => 'Dinosaurs',
            'Mammalia' => 'Mammals',
            'Reptilia' => 'Reptiles',
            'Aves' => 'Birds',
            'Amphibia' => 'Amphibians',
            'Actinopterygii' => 'Ray-finned Fish',
            'Chondrichthyes' => 'Sharks & Rays',
            'Trilobita' => 'Trilobites',
            'Ammonoidea' => 'Ammonites',
            'Tyrannosauridae' => 'Tyrannosaurids (T. rex family)',
            'Bivalvia' => 'Bivalves (Clams & Oysters)',
            'Echinoidea' => 'Sea Urchins',
            'Plantae' => 'Plants',
            'Insecta' => 'Insects',
        ];
    }

    /**
     * Major geological intervals shown in the interval select.
     * Keys are PBDB interval names; values include the age range for context.
     *
     * @return array<string, string>
     */
    public static function intervalOptions(): array
    {
        return [
            '' => 'All time',
            'Quaternary' => 'Quaternary (2.6 Mya – present)',
            'Neogene' => 'Neogene (23–2.6 Mya)',
            'Paleogene' => 'Paleogene (66–23 Mya)',
            'Cretaceous' => 'Cretaceous (145–66 Mya)',
            'Jurassic' => 'Jurassic (201–145 Mya)',
            'Triassic' => 'Triassic (252–201 Mya)',
            'Permian' => 'Permian (299–252 Mya)',
            'Carboniferous' => 'Carboniferous (359–299 Mya)',
            'Devonian' => 'Devonian (419–359 Mya)',
            'Silurian' => 'Silurian (444–419 Mya)',
            'Ordovician' => 'Ordovician (485–444 Mya)',
            'Cambrian' => 'Cambrian (541–485 Mya)',
        ];
    }

    /**
     * Major fossil-bearing countries shown in the country select.
     * Keys are ISO 3166-1 alpha-2 codes; values are country names.
     *
     * @return array<string, string>
     */
    public static function countryOptions(): array
    {
        return [
            '' => 'All countries',
            'US' => 'United States',
            'CA' => 'Canada',
            'CN' => 'China',
            'DE' => 'Germany',
            'FR' => 'France',
            'GB' => 'United Kingdom',
            'AR' => 'Argentina',
            'AU' => 'Australia',
            'MN' => 'Mongolia',
            'BR' => 'Brazil',
            'RU' => 'Russia',
            'ES' => 'Spain',
            'MA' => 'Morocco',
            'ZA' => 'South Africa',
            'MX' => 'Mexico',
            'IT' => 'Italy',
            'PL' => 'Poland',
        ];
    }

    /**
     * Fired after any property is updated server-side.
     * Notifies parent components that filter state has changed.
     */
    public function updated(string $name): void
    {
        $this->dispatch('filtersChanged');
    }

    /**
     * Populate all filter fields from a named preset and immediately apply.
     * Does nothing if the ID is not recognised.
     */
    public function loadPreset(string $id): void
    {
        $preset = SearchPreset::find($id);

        if ($preset === null) {
            return;
        }

        $this->resetFilters();

        $this->baseName = $preset->baseName ?? '';
        $this->interval = $preset->interval ?? '';
        $this->minMa = $preset->minMa ?? 0;
        $this->maxMa = $preset->maxMa ?? 540;
        $this->envTypes = $preset->envTypes;
        $this->countryCodes = $preset->countryCodes ?? '';
        $this->customTaxon = false;

        $this->applyFilters();
    }

    /**
     * Switch the taxon field to free-text mode and clear the current value.
     */
    public function enableCustomTaxon(): void
    {
        $this->customTaxon = true;
        $this->baseName = '';
    }

    /**
     * Switch the taxon field back to the curated select and clear the value.
     */
    public function disableCustomTaxon(): void
    {
        $this->customTaxon = false;
        $this->baseName = '';
    }

    /**
     * Construct an OccurrenceQuery from the current filter state.
     */
    public function buildQuery(): OccurrenceQuery
    {
        return new OccurrenceQuery(
            baseName: $this->baseName !== '' ? $this->baseName : null,
            interval: $this->interval !== '' ? $this->interval : null,
            minMa: $this->minMa > 0 ? $this->minMa : null,
            maxMa: $this->maxMa < 540 ? $this->maxMa : null,
            envType: $this->envTypes !== [] ? implode(',', $this->envTypes) : null,
            countryCodes: $this->countryCodes !== '' ? $this->countryCodes : null,
            idQual: $this->idQual !== 'any' ? $this->idQual : null,
            lngMin: $this->lngMin,
            lngMax: $this->lngMax,
            latMin: $this->latMin,
            latMax: $this->latMax,
        );
    }

    /**
     * Dispatch apply-filters with the current filter values so parent components
     * can call their respective API methods.
     */
    public function applyFilters(): void
    {
        $this->dispatch(
            'apply-filters',
            baseName: $this->baseName !== '' ? $this->baseName : null,
            interval: $this->interval !== '' ? $this->interval : null,
            minMa: $this->minMa > 0 ? $this->minMa : null,
            maxMa: $this->maxMa < 540 ? $this->maxMa : null,
            envType: $this->envTypes !== [] ? implode(',', $this->envTypes) : null,
            countryCodes: $this->countryCodes !== '' ? $this->countryCodes : null,
            idQual: $this->idQual !== 'any' ? $this->idQual : null,
            lngMin: $this->lngMin,
            lngMax: $this->lngMax,
            latMin: $this->latMin,
            latMax: $this->latMax,
        );
    }

    /**
     * Reset all filter properties to their defaults and notify listeners.
     */
    public function resetFilters(): void
    {
        $this->baseName = '';
        $this->interval = '';
        $this->minMa = 0;
        $this->maxMa = 540;
        $this->envTypes = [];
        $this->countryCodes = '';
        $this->idQual = 'any';
        $this->lngMin = null;
        $this->lngMax = null;
        $this->latMin = null;
        $this->latMax = null;
        $this->customTaxon = false;
        $this->dispatch('filters-reset');
    }

    /**
     * Clear the bounding box coordinates.
     */
    public function clearBoundingBox(): void
    {
        $this->lngMin = null;
        $this->lngMax = null;
        $this->latMin = null;
        $this->latMax = null;
    }

    /**
     * Receive bounding box coordinates from the Leaflet draw tool via JS.
     */
    #[On('bbox-set')]
    public function setBoundingBox(float $lngMin, float $lngMax, float $latMin, float $latMax): void
    {
        $this->lngMin = $lngMin;
        $this->lngMax = $lngMax;
        $this->latMin = $latMin;
        $this->latMax = $latMax;
    }

    /**
     * Returns true when at least one filter differs from its default.
     */
    public function hasFilters(): bool
    {
        return $this->baseName !== ''
            || $this->interval !== ''
            || $this->minMa > 0
            || $this->maxMa < 540
            || $this->envTypes !== []
            || $this->countryCodes !== ''
            || $this->idQual !== 'any'
            || $this->lngMin !== null
            || $this->lngMax !== null
            || $this->latMin !== null
            || $this->latMax !== null;
    }

    public function render(): View
    {
        return view('livewire.occurrence-filters', [
            'envTypeOptions' => self::envTypeOptions(),
            'taxonOptions' => self::taxonOptions(),
            'intervalOptions' => self::intervalOptions(),
            'countryOptions' => self::countryOptions(),
            'presets' => SearchPreset::all(),
        ]);
    }
}
