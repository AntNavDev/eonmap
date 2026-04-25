<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\DTOs\OccurrenceCollection;
use App\DTOs\OccurrenceDTO;
use App\Livewire\OccurrenceBrowser;
use Livewire\Livewire;
use Tests\TestCase;

class OccurrenceBrowserTest extends TestCase
{
    // ---------------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------------

    private function mockService(OccurrenceCollection $collection): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->andReturn($collection);
    }

    /**
     * Build a minimal OccurrenceDTO for table row fixtures.
     *
     * @param  array<string, mixed>  $overrides
     */
    private function makeDto(array $overrides = []): OccurrenceDTO
    {
        return OccurrenceDTO::fromArray(array_merge([
            'oid' => 'occ:41524',
            'tna' => 'Triceratops horridus',
            'rnk' => 5,
            'cid' => 'col:3257',
            'lat' => '40.0',
            'lng' => '-105.0',
            'oei' => 'Maastrichtian',
            'oli' => 'Maastrichtian',
            'eag' => '66.0',
            'lag' => '65.5',
            'cc2' => 'US',
            'stp' => 'Colorado',
            'sfm' => 'Lance',
            'env' => 'terrestrial',
        ], $overrides));
    }

    // ---------------------------------------------------------------------------
    // Mount
    // ---------------------------------------------------------------------------

    public function test_component_mounts_without_error(): void
    {
        Livewire::test(OccurrenceBrowser::class)
            ->assertOk();
    }

    // ---------------------------------------------------------------------------
    // loadOccurrences — no filter guard
    // ---------------------------------------------------------------------------

    public function test_load_occurrences_without_any_filter_dispatches_empty_array(): void
    {
        // No service mock needed — the method should short-circuit before calling the service.
        Livewire::test(OccurrenceBrowser::class)
            ->call('loadOccurrences')
            ->assertDispatched('browser-data-loaded', occurrences: []);
    }

    // ---------------------------------------------------------------------------
    // loadOccurrences — happy path
    // ---------------------------------------------------------------------------

    public function test_load_occurrences_dispatches_browser_data_loaded_event(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->call('loadOccurrences')
            ->assertDispatched('browser-data-loaded');
    }

    public function test_total_reflects_count_of_returned_items_not_pbdb_records_found(): void
    {
        $dto = $this->makeDto();
        // Even if OccurrenceCollection carries a large total from records_found,
        // $this->total is set to count($collection->items) because PBDB does not
        // actually return records_found in its API responses.
        $this->mockService(new OccurrenceCollection(items: [$dto], total: 9876, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->call('loadOccurrences')
            ->assertSet('total', 1);
    }

    public function test_has_more_is_true_when_page_is_full(): void
    {
        // Build exactly perPage (25) DTOs so the page is considered full.
        $items = array_fill(0, 25, $this->makeDto());
        $this->mockService(new OccurrenceCollection(items: $items, total: 25, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->call('loadOccurrences')
            ->assertSet('hasMore', true);
    }

    public function test_has_more_is_false_when_page_is_partial(): void
    {
        // Fewer than perPage (25) items — we are on the last page.
        $items = array_fill(0, 10, $this->makeDto());
        $this->mockService(new OccurrenceCollection(items: $items, total: 10, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->call('loadOccurrences')
            ->assertSet('hasMore', false);
    }

    public function test_has_more_is_false_when_no_filter_applied(): void
    {
        Livewire::test(OccurrenceBrowser::class)
            ->call('loadOccurrences')
            ->assertSet('hasMore', false);
    }

    public function test_has_more_is_false_after_api_exception(): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->andThrow(new ApiException('Failure'));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->call('loadOccurrences')
            ->assertSet('hasMore', false);
    }

    /**
     * The browser-data-loaded payload must use snake_case keys because Tabulator
     * column definitions reference them as occurrence_no, accepted_name, etc.
     */
    public function test_browser_data_loaded_payload_has_snake_case_keys(): void
    {
        $dto = $this->makeDto();
        $this->mockService(new OccurrenceCollection(items: [$dto], total: 1, offset: 0));

        $component = Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Ceratopsidae')
            ->call('loadOccurrences');

        $component->assertDispatched(
            'browser-data-loaded',
            fn ($event, $params) => is_array($params['occurrences'][0])
                && array_key_exists('occurrence_no', $params['occurrences'][0])
                && ! array_key_exists('occurrenceNo', $params['occurrences'][0])
        );
    }

    public function test_browser_data_loaded_payload_has_all_expected_table_keys(): void
    {
        $dto = $this->makeDto();
        $this->mockService(new OccurrenceCollection(items: [$dto], total: 1, offset: 0));

        $component = Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Ceratopsidae')
            ->call('loadOccurrences');

        $expectedKeys = [
            'occurrence_no', 'accepted_name', 'accepted_rank',
            'early_interval', 'late_interval', 'max_ma', 'min_ma',
            'country', 'state', 'formation', 'environment',
        ];

        $component->assertDispatched(
            'browser-data-loaded',
            fn ($event, $params) => count(
                array_diff($expectedKeys, array_keys($params['occurrences'][0]))
            ) === 0
        );
    }

    public function test_browser_data_loaded_payload_values_match_dto_properties(): void
    {
        $dto = $this->makeDto();
        $this->mockService(new OccurrenceCollection(items: [$dto], total: 1, offset: 0));

        $component = Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Ceratopsidae')
            ->call('loadOccurrences');

        $component->assertDispatched(
            'browser-data-loaded',
            function ($event, $params) use ($dto) {
                $row = $params['occurrences'][0];

                return $row['occurrence_no'] === $dto->occurrenceNo
                    && $row['accepted_name'] === $dto->acceptedName
                    && $row['accepted_rank'] === $dto->acceptedRank
                    && $row['country'] === $dto->country
                    && $row['state'] === $dto->state
                    && $row['formation'] === $dto->formation;
            }
        );
    }

    // ---------------------------------------------------------------------------
    // loadOccurrences — error path
    // ---------------------------------------------------------------------------

    public function test_load_error_is_set_when_api_exception_thrown(): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andThrow(new ApiException('Rate limit exceeded'));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->call('loadOccurrences')
            ->assertSet('loadError', 'Rate limit exceeded');
    }

    public function test_load_error_is_cleared_on_subsequent_successful_load(): void
    {
        $mock = $this->mock(FossilOccurrenceServiceInterface::class);
        $mock->shouldReceive('getOccurrences')
            ->once()
            ->andThrow(new ApiException('Temporary failure'));
        $mock->shouldReceive('getOccurrences')
            ->once()
            ->andReturn(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->call('loadOccurrences')
            ->assertSet('loadError', 'Temporary failure')
            ->call('loadOccurrences')
            ->assertSet('loadError', null);
    }

    // ---------------------------------------------------------------------------
    // onFiltersApplied
    // ---------------------------------------------------------------------------

    public function test_on_filters_applied_stores_all_filter_values(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
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
            )
            ->assertSet('filterBaseName', 'Tyrannosauridae')
            ->assertSet('filterInterval', 'Cretaceous')
            ->assertSet('filterMinMa', 66.0)
            ->assertSet('filterMaxMa', 100.5)
            ->assertSet('filterEnvType', 'terrestrial')
            ->assertSet('filterCountryCodes', 'US,CA')
            ->assertSet('filterIdQual', 'certain')
            ->assertSet('filterLngMin', -120.0)
            ->assertSet('filterLngMax', -60.0)
            ->assertSet('filterLatMin', 25.0)
            ->assertSet('filterLatMax', 70.0);
    }

    public function test_on_filters_applied_resets_offset_to_zero(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 100, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('offset', 50)
            ->dispatch('apply-filters', baseName: 'Dinosauria')
            ->assertSet('offset', 0);
    }

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

        Livewire::test(OccurrenceBrowser::class)
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

    // ---------------------------------------------------------------------------
    // Pagination
    // ---------------------------------------------------------------------------

    public function test_next_page_increments_offset_by_per_page(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->assertSet('offset', 0)
            ->call('nextPage')
            ->assertSet('offset', 25);
    }

    public function test_prev_page_decrements_offset_and_floors_at_zero(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->set('offset', 25)
            ->call('prevPage')
            ->assertSet('offset', 0)
            ->call('prevPage')
            ->assertSet('offset', 0); // cannot go below zero
    }

    public function test_updated_per_page_resets_offset_and_reloads(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->set('offset', 50)
            ->set('perPage', 50)
            ->assertSet('offset', 0)
            ->assertSet('perPage', 50);
    }

    public function test_updated_per_page_rejects_values_outside_allowed_set(): void
    {
        Livewire::test(OccurrenceBrowser::class)
            ->set('perPage', 9999)
            ->assertSet('perPage', 25);
    }

    public function test_per_page_sent_to_query_as_limit(): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->withArgs(fn ($query) => $query->limit === 50)
            ->andReturn(new OccurrenceCollection(items: [], total: 0, offset: 0));

        // Setting perPage triggers updatedPerPage() → loadOccurrences(); no extra call needed.
        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->set('perPage', 50);
    }

    public function test_current_offset_sent_to_query(): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->withArgs(fn ($query) => $query->offset === 25)
            ->andReturn(new OccurrenceCollection(items: [], total: 0, offset: 25));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->set('offset', 25)
            ->call('loadOccurrences');
    }

    // ---------------------------------------------------------------------------
    // exportUrl
    // ---------------------------------------------------------------------------

    public function test_export_url_without_filters_returns_base_path(): void
    {
        $component = Livewire::test(OccurrenceBrowser::class);

        $this->assertSame('/api/export/occurrences', $component->instance()->exportUrl());
    }

    public function test_export_url_with_base_name_filter(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 0, offset: 0));

        $component = Livewire::test(OccurrenceBrowser::class)
            ->dispatch('apply-filters', baseName: 'Dinosauria');

        $url = $component->instance()->exportUrl();
        $this->assertStringContainsString('base_name=Dinosauria', $url);
        $this->assertStringStartsWith('/api/export/occurrences?', $url);
    }

    public function test_export_url_includes_all_active_filters(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 0, offset: 0));

        $component = Livewire::test(OccurrenceBrowser::class)
            ->dispatch('apply-filters',
                baseName: 'Tyrannosauridae',
                interval: 'Cretaceous',
                countryCodes: 'US',
                envType: 'terrestrial',
            );

        $url = $component->instance()->exportUrl();
        $this->assertStringContainsString('base_name=Tyrannosauridae', $url);
        $this->assertStringContainsString('interval=Cretaceous', $url);
        $this->assertStringContainsString('cc=US', $url);
        $this->assertStringContainsString('envtype=terrestrial', $url);
    }

    public function test_export_url_omits_null_filter_values(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 0, offset: 0));

        $component = Livewire::test(OccurrenceBrowser::class)
            ->dispatch('apply-filters', baseName: 'Dinosauria');

        $url = $component->instance()->exportUrl();
        // null filters like interval, envType must not appear in the URL
        $this->assertStringNotContainsString('interval=', $url);
        $this->assertStringNotContainsString('envtype=', $url);
    }
}
