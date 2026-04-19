<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\Api\Queries\OccurrenceQuery;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class OccurrenceBrowser extends Component
{
    public int $perPage = 25;

    public int $offset = 0;

    public string $sortField = 'id';

    public string $sortDirection = 'asc';

    public int $total = 0;

    public ?string $loadError = null;

    // Filter values stored after the last apply-filters event
    public ?string $filterBaseName = null;

    public ?string $filterInterval = null;

    public ?float $filterMinMa = null;

    public ?float $filterMaxMa = null;

    public ?string $filterEnvType = null;

    public ?string $filterCountryCodes = null;

    public ?string $filterIdQual = null;

    public ?float $filterLngMin = null;

    public ?float $filterLngMax = null;

    public ?float $filterLatMin = null;

    public ?float $filterLatMax = null;

    /**
     * Receive filter values from the OccurrenceFilters component and load
     * the first page of results.
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
        $this->filterBaseName = $baseName;
        $this->filterInterval = $interval;
        $this->filterMinMa = $minMa;
        $this->filterMaxMa = $maxMa;
        $this->filterEnvType = $envType;
        $this->filterCountryCodes = $countryCodes;
        $this->filterIdQual = $idQual;
        $this->filterLngMin = $lngMin;
        $this->filterLngMax = $lngMax;
        $this->filterLatMin = $latMin;
        $this->filterLatMax = $latMax;
        $this->offset = 0;
        $this->loadOccurrences();
    }

    /**
     * Fetch a page of occurrences using the stored filter values and dispatch
     * browser-data-loaded with the serialised records.
     */
    public function loadOccurrences(): void
    {
        $this->loadError = null;

        $hasFilter = $this->filterBaseName !== null
            || $this->filterInterval !== null
            || $this->filterMinMa !== null
            || $this->filterMaxMa !== null
            || $this->filterEnvType !== null
            || $this->filterCountryCodes !== null
            || $this->filterIdQual !== null
            || $this->filterLngMin !== null
            || $this->filterLngMax !== null
            || $this->filterLatMin !== null
            || $this->filterLatMax !== null;

        if (! $hasFilter) {
            $this->dispatch('browser-data-loaded', occurrences: []);

            return;
        }

        try {
            /** @var FossilOccurrenceServiceInterface $service */
            $service = app(FossilOccurrenceServiceInterface::class);

            $query = new OccurrenceQuery(
                baseName: $this->filterBaseName,
                interval: $this->filterInterval,
                minMa: $this->filterMinMa,
                maxMa: $this->filterMaxMa,
                envType: $this->filterEnvType,
                countryCodes: $this->filterCountryCodes,
                idQual: $this->filterIdQual,
                lngMin: $this->filterLngMin,
                lngMax: $this->filterLngMax,
                latMin: $this->filterLatMin,
                latMax: $this->filterLatMax,
                limit: $this->perPage,
                offset: $this->offset,
            );

            $collection = $service->getOccurrences($query);
            $this->total = $collection->total;

            // Transform to snake_case arrays for Tabulator
            $rows = array_map(static fn ($dto) => [
                'occurrence_no' => $dto->occurrenceNo,
                'accepted_name' => $dto->acceptedName,
                'accepted_rank' => $dto->acceptedRank,
                'early_interval' => $dto->earlyInterval,
                'late_interval' => $dto->lateInterval,
                'max_ma' => $dto->maxMa,
                'min_ma' => $dto->minMa,
                'country' => $dto->country,
                'state' => $dto->state,
                'formation' => $dto->formation,
                'environment' => $dto->environment,
            ], $collection->items);

            $this->dispatch('browser-data-loaded', occurrences: $rows);
        } catch (ApiException $e) {
            $this->loadError = $e->getMessage();
        }
    }

    /**
     * Advance to the next page.
     */
    public function nextPage(): void
    {
        $this->offset += $this->perPage;
        $this->loadOccurrences();
    }

    /**
     * Return to the previous page (floor at zero).
     */
    public function prevPage(): void
    {
        $this->offset = max(0, $this->offset - $this->perPage);
        $this->loadOccurrences();
    }

    /**
     * Reset to page 1 and reload when the per-page value changes.
     * Rejects values outside the allowed set to prevent crafted requests.
     */
    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [25, 50, 100], strict: true)) {
            $this->perPage = 25;
        }

        $this->offset = 0;
        $this->loadOccurrences();
    }

    /**
     * Toggle sort direction when the same field is clicked; reset to asc for
     * a new field.
     */
    public function setSort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Build the CSV export URL from the current stored filter values.
     */
    public function exportUrl(): string
    {
        $params = array_filter([
            'base_name' => $this->filterBaseName,
            'interval' => $this->filterInterval,
            'min_ma' => $this->filterMinMa,
            'max_ma' => $this->filterMaxMa,
            'envtype' => $this->filterEnvType,
            'cc' => $this->filterCountryCodes,
            'idqual' => $this->filterIdQual,
            'lngmin' => $this->filterLngMin,
            'lngmax' => $this->filterLngMax,
            'latmin' => $this->filterLatMin,
            'latmax' => $this->filterLatMax,
        ], fn ($v) => $v !== null);

        return '/api/export/occurrences'.($params ? '?'.http_build_query($params) : '');
    }

    public function render(): View
    {
        return view('livewire.occurrence-browser', [
            'from' => $this->total > 0 ? $this->offset + 1 : 0,
            'to' => min($this->offset + $this->perPage, $this->total),
            'perPage' => $this->perPage,
            'exportUrl' => $this->exportUrl(),
        ]);
    }
}
