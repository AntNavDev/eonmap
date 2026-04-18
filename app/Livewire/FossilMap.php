<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\Api\Queries\OccurrenceQuery;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class FossilMap extends Component
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

    public ?string $loadError = null;

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
     * On mount, dispatch an empty occurrences-loaded so the map initialises
     * without a loading state. Only call the API if filters are already set
     * (e.g. when the component is re-mounted with pre-filled properties).
     */
    public function mount(): void
    {
        if ($this->hasFilters()) {
            $this->loadOccurrences();
        } else {
            $this->dispatch('occurrences-loaded', occurrences: []);
        }
    }

    /**
     * Fetch occurrences from the PBDB API using the current filter state and
     * dispatch a browser event with the results. Dispatches occurrences-error
     * if the API call fails.
     */
    public function loadOccurrences(): void
    {
        $this->loadError = null;

        if (! $this->hasFilters()) {
            $this->dispatch('occurrences-loaded', occurrences: []);

            return;
        }

        try {
            /** @var FossilOccurrenceServiceInterface $service */
            $service = app(FossilOccurrenceServiceInterface::class);

            $query = new OccurrenceQuery(
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

            $collection = $service->getOccurrences($query);

            $this->dispatch('occurrences-loaded', occurrences: $collection->items);
        } catch (ApiException $e) {
            $this->loadError = $e->getMessage();
            $this->dispatch('occurrences-error', message: $e->getMessage());
        }
    }

    /**
     * Clear all bounding box coordinates.
     */
    public function clearBoundingBox(): void
    {
        $this->lngMin = null;
        $this->lngMax = null;
        $this->latMin = null;
        $this->latMax = null;
    }

    public function render(): View
    {
        return view('livewire.fossil-map', [
            'envTypeOptions' => self::envTypeOptions(),
            'hasFilters' => $this->hasFilters(),
        ]);
    }

    /**
     * Returns true if at least one filter differs from its default value.
     */
    private function hasFilters(): bool
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
}
