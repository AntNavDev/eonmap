<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Api\Queries\OccurrenceQuery;
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
     * Fired after any property is updated server-side.
     * Notifies parent components that filter state has changed.
     */
    public function updated(string $name): void
    {
        $this->dispatch('filtersChanged');
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
        ]);
    }
}
