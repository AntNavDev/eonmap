<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\DTOs\OccurrenceCollection;
use App\DTOs\OccurrenceDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OccurrenceDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function makeDto(int $id = 1): OccurrenceDTO
    {
        return new OccurrenceDTO(
            occurrenceNo: $id,
            acceptedName: 'Tyrannosaurus rex',
            acceptedRank: 'species',
            phylum: 'Chordata',
            class: 'Reptilia',
            order: 'Saurischia',
            family: 'Tyrannosauridae',
            genus: 'Tyrannosaurus',
            lat: 46.123,
            lng: -104.456,
            earlyInterval: 'Maastrichtian',
            lateInterval: 'Maastrichtian',
            maxMa: 66.0,
            minMa: 65.5,
            country: 'US',
            state: 'Montana',
            formation: 'Hell Creek',
            environment: 'fluvial-channel',
            collectionNo: 456,
        );
    }

    private function mockCollection(int $id = 1): void
    {
        $collection = new OccurrenceCollection(
            items: [$this->makeDto($id)],
            total: 1,
            offset: 0,
        );

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->andReturn($collection);
    }

    public function test_show_returns_200_for_valid_occurrence(): void
    {
        $this->mockCollection();

        $this->get('/occurrences/1')->assertOk();
    }

    public function test_show_returns_404_when_collection_is_empty(): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->andReturn(new OccurrenceCollection(items: [], total: 0, offset: 0));

        $this->get('/occurrences/999')->assertNotFound();
    }

    public function test_detail_view_contains_taxon_name(): void
    {
        $this->mockCollection();

        $this->get('/occurrences/1')->assertSee('Tyrannosaurus rex');
    }

    public function test_detail_view_contains_link_back_to_map(): void
    {
        $this->mockCollection();

        $this->get('/occurrences/1')->assertSee('/map');
    }

    public function test_recently_viewed_row_is_created_on_page_load(): void
    {
        $this->mockCollection(42);

        $this->get('/occurrences/42');

        $this->assertDatabaseHas('recently_viewed', ['occurrence_no' => 42]);
    }
}
