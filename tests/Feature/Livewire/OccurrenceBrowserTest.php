<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\DTOs\OccurrenceCollection;
use App\Livewire\OccurrenceBrowser;
use Livewire\Livewire;
use Tests\TestCase;

class OccurrenceBrowserTest extends TestCase
{
    private function mockService(OccurrenceCollection $collection): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->andReturn($collection);
    }

    public function test_component_mounts_without_error(): void
    {
        Livewire::test(OccurrenceBrowser::class)
            ->assertOk();
    }

    public function test_load_occurrences_dispatches_browser_data_loaded_event(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->call('loadOccurrences')
            ->assertDispatched('browser-data-loaded');
    }

    public function test_next_page_increments_offset_by_limit(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->assertSet('offset', 0)
            ->call('nextPage')
            ->assertSet('offset', 100);
    }

    public function test_prev_page_decrements_offset_and_floors_at_zero(): void
    {
        $this->mockService(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(OccurrenceBrowser::class)
            ->set('filterBaseName', 'Dinosauria')
            ->set('offset', 100)
            ->call('prevPage')
            ->assertSet('offset', 0)
            ->call('prevPage')
            ->assertSet('offset', 0); // cannot go below zero
    }

    public function test_set_sort_toggles_direction_on_repeated_calls_with_same_field(): void
    {
        Livewire::test(OccurrenceBrowser::class)
            ->set('sortField', 'accepted_name')
            ->set('sortDirection', 'asc')
            ->call('setSort', 'accepted_name')
            ->assertSet('sortDirection', 'desc')
            ->call('setSort', 'accepted_name')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_set_sort_resets_direction_to_asc_for_new_field(): void
    {
        Livewire::test(OccurrenceBrowser::class)
            ->set('sortField', 'accepted_name')
            ->set('sortDirection', 'desc')
            ->call('setSort', 'country')
            ->assertSet('sortField', 'country')
            ->assertSet('sortDirection', 'asc');
    }
}
