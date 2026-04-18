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

    public function test_load_occurrences_dispatches_occurrences_loaded_event(): void
    {
        $collection = new OccurrenceCollection(items: [], total: 0, offset: 0);

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andReturn($collection);

        Livewire::test(FossilMap::class)
            ->set('baseName', 'Dinosauria')
            ->call('loadOccurrences')
            ->assertDispatched('occurrences-loaded');
    }

    public function test_load_occurrences_dispatches_occurrences_error_on_api_exception(): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->once()
            ->andThrow(new ApiException('Service unavailable'));

        Livewire::test(FossilMap::class)
            ->set('baseName', 'Dinosauria')
            ->call('loadOccurrences')
            ->assertDispatched('occurrences-error');
    }

    public function test_clear_bounding_box_nulls_all_lat_lng_properties(): void
    {
        Livewire::test(FossilMap::class)
            ->set('lngMin', -100.0)
            ->set('lngMax', 100.0)
            ->set('latMin', -50.0)
            ->set('latMax', 50.0)
            ->call('clearBoundingBox')
            ->assertSet('lngMin', null)
            ->assertSet('lngMax', null)
            ->assertSet('latMin', null)
            ->assertSet('latMax', null);
    }

    public function test_filter_panel_renders_all_environment_type_checkboxes(): void
    {
        Livewire::test(FossilMap::class)
            ->assertSee('Terrestrial')
            ->assertSee('Marine')
            ->assertSee('Carbonate')
            ->assertSee('Siliciclastic')
            ->assertSee('Reef / bioherm')
            ->assertSee('Lacustrine')
            ->assertSee('Fluvial');
    }
}
