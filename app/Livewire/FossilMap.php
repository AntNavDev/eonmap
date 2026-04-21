<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\Api\Queries\OccurrenceQuery;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class FossilMap extends Component
{
    public ?string $loadError = null;

    /** Set to true once the user has submitted filters at least once. */
    public bool $filtersApplied = false;

    /** Number of occurrences returned in the last query (may be less than $resultTotal). */
    public int $resultCount = 0;

    /** Total occurrences matching the last query according to PBDB (records_found). */
    public int $resultTotal = 0;

    /**
     * On initial mount, fire an empty occurrences-loaded so the Leaflet map
     * initialises without a loading state.
     */
    public function mount(): void
    {
        $this->dispatch('occurrences-loaded', occurrences: []);
    }

    /**
     * Triggered when OccurrenceFilters dispatches apply-filters.
     * Calls the PBDB API and dispatches occurrences-loaded with the results.
     */
    #[On('apply-filters')]
    public function onFiltersApplied(
        ?string $baseName = null,
        ?string $interval = null,
        ?float $minMa = null,
        ?float $maxMa = null,
        ?string $envType = null,
        ?string $countryCodes = null,
        ?string $idQual = null,
        ?float $lngMin = null,
        ?float $lngMax = null,
        ?float $latMin = null,
        ?float $latMax = null,
    ): void {
        $this->filtersApplied = true;
        $this->loadError = null;

        try {
            /** @var FossilOccurrenceServiceInterface $service */
            $service = app(FossilOccurrenceServiceInterface::class);

            $query = new OccurrenceQuery(
                baseName: $baseName,
                interval: $interval,
                minMa: $minMa,
                maxMa: $maxMa,
                envType: $envType,
                countryCodes: $countryCodes,
                idQual: $idQual,
                lngMin: $lngMin,
                lngMax: $lngMax,
                latMin: $latMin,
                latMax: $latMax,
            );

            $collection = $service->getOccurrences($query);
            $this->resultCount = count($collection->items);
            $this->resultTotal = $collection->total;

            $occurrences = array_map(static fn ($dto) => [
                'occurrenceNo' => $dto->occurrenceNo,
                'acceptedName' => $dto->acceptedName,
                'acceptedRank' => $dto->acceptedRank,
                'lat' => $dto->lat,
                'lng' => $dto->lng,
                'earlyInterval' => $dto->earlyInterval,
                'lateInterval' => $dto->lateInterval,
                'maxMa' => $dto->maxMa,
                'minMa' => $dto->minMa,
                'country' => $dto->country,
                'formation' => $dto->formation,
                'environment' => $dto->environment,
                'paleolat' => $dto->paleolat,
                'paleolng' => $dto->paleolng,
            ], $collection->items);

            $this->dispatch('occurrences-loaded', occurrences: $occurrences);
        } catch (ApiException $e) {
            $this->loadError = $e->getMessage();
            $this->dispatch('occurrences-error', message: $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.fossil-map', [
            'hasFilters' => $this->filtersApplied,
            'resultCount' => $this->resultCount,
            'resultTotal' => $this->resultTotal,
        ]);
    }
}
