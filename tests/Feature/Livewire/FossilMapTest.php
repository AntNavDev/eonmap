<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\DTOs\OccurrenceCollection;
use App\Livewire\FossilMap;
use Livewire\Livewire;
use Tests\TestCase;

class FossilMapTest extends TestCase
{
    public function test_component_mounts_without_error(): void
    {
        Livewire::test(FossilMap::class)
            ->assertOk();
    }

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
}
