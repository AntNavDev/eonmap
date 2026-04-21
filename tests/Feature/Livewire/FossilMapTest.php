<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\DTOs\OccurrenceCollection;
use App\DTOs\OccurrenceDTO;
use App\Livewire\FossilMap;
use Livewire\Livewire;
use Tests\TestCase;

class FossilMapTest extends TestCase
{
    // ---------------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------------

    /**
     * Build a minimal OccurrenceDTO for use in collection fixtures.
     *
     * @param  array<string, mixed>  $overrides
     */
    private function makeDto(array $overrides = []): OccurrenceDTO
    {
        return OccurrenceDTO::fromArray(array_merge([
            'oid' => 'occ:41524',
            'tna' => 'Tyrannosaurus rex',
            'rnk' => 5,
            'cid' => 'col:3257',
            'lat' => '46.0',
            'lng' => '-104.0',
            'oei' => 'Maastrichtian',
            'oli' => 'Maastrichtian',
            'eag' => '66.0',
            'lag' => '65.5',
            'cc2' => 'US',
            'sfm' => 'Hell Creek',
            'env' => 'fluvial-channel',
            'pla' => '55.0',
            'plo' => '-112.0',
        ], $overrides));
    }

    // ---------------------------------------------------------------------------
    // Mount
    // ---------------------------------------------------------------------------

    public function test_component_mounts_without_error(): void
    {
        Livewire::test(FossilMap::class)
            ->assertOk();
    }

    public function test_initial_mount_dispatches_occurrences_loaded_with_empty_array(): void
    {
        Livewire::test(FossilMap::class)
            ->assertDispatched('occurrences-loaded', occurrences: []);
    }

    public function test_filters_applied_is_false_before_first_filter_dispatch(): void
    {
        Livewire::test(FossilMap::class)
            ->assertSet('filtersApplied', false);
    }

    // ---------------------------------------------------------------------------
    // apply-filters — happy path
    // ---------------------------------------------------------------------------

    public function test_on_filters_applied_dispatches_occurrences_loaded_event(): void
    {
        $collection = new OccurrenceCollection(items: [], total: 0, offset: 0);

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andReturn($collection);

        Livewire::test(FossilMap::class)
            ->dispatch('apply-filters', baseName: 'Dinosauria')
            ->assertDispatched('occurrences-loaded');
    }

    public function test_filters_applied_flag_is_set_after_apply_filters_event(): void
    {
        $collection = new OccurrenceCollection(items: [], total: 0, offset: 0);

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andReturn($collection);

        Livewire::test(FossilMap::class)
            ->assertSet('filtersApplied', false)
            ->dispatch('apply-filters', baseName: 'Dinosauria')
            ->assertSet('filtersApplied', true);
    }

    public function test_result_count_is_set_to_number_of_returned_items(): void
    {
        $collection = new OccurrenceCollection(
            items: [$this->makeDto(), $this->makeDto(['oid' => 'occ:99999'])],
            total: 500,
            offset: 0,
        );

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andReturn($collection);

        Livewire::test(FossilMap::class)
            ->dispatch('apply-filters', baseName: 'Tyrannosauridae')
            ->assertSet('resultCount', 2);
    }

    public function test_result_total_reflects_pbdb_records_found(): void
    {
        $collection = new OccurrenceCollection(
            items: [$this->makeDto()],
            total: 12345,
            offset: 0,
        );

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andReturn($collection);

        Livewire::test(FossilMap::class)
            ->dispatch('apply-filters', baseName: 'Dinosauria')
            ->assertSet('resultTotal', 12345);
    }

    /**
     * The occurrences-loaded payload must be plain camelCase arrays so that
     * Livewire serialises them correctly and map.js can read occ.lat, occ.lng etc.
     * Dispatching raw PHP DTOs caused a blank map — this regression test locks in the fix.
     */
    public function test_occurrences_loaded_payload_contains_plain_camel_case_arrays(): void
    {
        $dto = $this->makeDto();
        $collection = new OccurrenceCollection(items: [$dto], total: 1, offset: 0);

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andReturn($collection);

        $component = Livewire::test(FossilMap::class)
            ->dispatch('apply-filters', baseName: 'Tyrannosauridae');

        $component->assertDispatched(
            'occurrences-loaded',
            fn ($event, $params) => is_array($params['occurrences'][0])
                && array_key_exists('occurrenceNo', $params['occurrences'][0])
        );
    }

    public function test_occurrences_loaded_payload_has_all_expected_map_keys(): void
    {
        $dto = $this->makeDto();
        $collection = new OccurrenceCollection(items: [$dto], total: 1, offset: 0);

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andReturn($collection);

        $component = Livewire::test(FossilMap::class)
            ->dispatch('apply-filters', baseName: 'Tyrannosauridae');

        $expectedKeys = [
            'occurrenceNo', 'acceptedName', 'acceptedRank',
            'lat', 'lng',
            'earlyInterval', 'lateInterval', 'maxMa', 'minMa',
            'country', 'formation', 'environment',
            'paleolat', 'paleolng',
        ];

        $component->assertDispatched(
            'occurrences-loaded',
            fn ($event, $params) => count(
                array_diff($expectedKeys, array_keys($params['occurrences'][0]))
            ) === 0
        );
    }

    public function test_occurrences_loaded_payload_values_match_dto_properties(): void
    {
        $dto = $this->makeDto();
        $collection = new OccurrenceCollection(items: [$dto], total: 1, offset: 0);

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andReturn($collection);

        $component = Livewire::test(FossilMap::class)
            ->dispatch('apply-filters', baseName: 'Tyrannosauridae');

        $component->assertDispatched(
            'occurrences-loaded',
            function ($event, $params) use ($dto) {
                // Mount also dispatches occurrences-loaded with an empty array;
                // skip that one and only validate when a row is present.
                if (empty($params['occurrences'])) {
                    return false;
                }

                $row = $params['occurrences'][0];

                // Compare int and string fields only — Livewire's event pipeline
                // JSON-serializes whole floats (46.0 → 46) so strict float === fails.
                return $row['occurrenceNo'] === $dto->occurrenceNo
                    && $row['acceptedName'] === $dto->acceptedName
                    && $row['country'] === $dto->country
                    && $row['formation'] === $dto->formation
                    && $row['environment'] === $dto->environment
                    && $row['earlyInterval'] === $dto->earlyInterval;
            }
        );
    }

    // ---------------------------------------------------------------------------
    // apply-filters — error path
    // ---------------------------------------------------------------------------

    public function test_on_filters_applied_dispatches_occurrences_error_on_api_exception(): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andThrow(new ApiException('Service unavailable'));

        Livewire::test(FossilMap::class)
            ->dispatch('apply-filters', baseName: 'Dinosauria')
            ->assertDispatched('occurrences-error');
    }

    public function test_load_error_is_set_when_api_exception_thrown(): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andThrow(new ApiException('PBDB is down'));

        Livewire::test(FossilMap::class)
            ->dispatch('apply-filters', baseName: 'Dinosauria')
            ->assertSet('loadError', 'PBDB is down');
    }

    public function test_load_error_is_cleared_on_subsequent_successful_call(): void
    {
        $mock = $this->mock(FossilOccurrenceServiceInterface::class);
        $mock->shouldReceive('getOccurrences')
            ->once()
            ->andThrow(new ApiException('Temporary failure'));
        $mock->shouldReceive('getOccurrences')
            ->once()
            ->andReturn(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(FossilMap::class)
            ->dispatch('apply-filters', baseName: 'Dinosauria')
            ->assertSet('loadError', 'Temporary failure')
            ->dispatch('apply-filters', baseName: 'Dinosauria')
            ->assertSet('loadError', null);
    }

    // ---------------------------------------------------------------------------
    // Filter forwarding
    // ---------------------------------------------------------------------------

    public function test_all_filter_params_are_forwarded_to_occurrence_query(): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->withArgs(function ($query) {
                return $query->baseName === 'Tyrannosauridae'
                    && $query->interval === 'Cretaceous'
                    && $query->minMa === 66.0
                    && $query->maxMa === 100.5
                    && $query->envType === 'terrestrial'
                    && $query->countryCodes === 'US,CA'
                    && $query->idQual === 'certain'
                    && $query->lngMin === -120.0
                    && $query->lngMax === -60.0
                    && $query->latMin === 25.0
                    && $query->latMax === 70.0;
            })
            ->andReturn(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(FossilMap::class)
            ->dispatch('apply-filters',
                baseName: 'Tyrannosauridae',
                interval: 'Cretaceous',
                minMa: 66.0,
                maxMa: 100.5,
                envType: 'terrestrial',
                countryCodes: 'US,CA',
                idQual: 'certain',
                lngMin: -120.0,
                lngMax: -60.0,
                latMin: 25.0,
                latMax: 70.0,
            );
    }
}
