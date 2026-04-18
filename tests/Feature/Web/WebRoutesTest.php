<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\DTOs\OccurrenceCollection;
use App\DTOs\OccurrenceDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_root_redirects_to_map(): void
    {
        $this->get('/')->assertRedirect('/map');
    }

    public function test_map_returns_200(): void
    {
        $this->get('/map')->assertOk();
    }

    public function test_browse_returns_200(): void
    {
        $this->get('/browse')->assertOk();
    }

    public function test_occurrence_show_returns_200(): void
    {
        $dto = new OccurrenceDTO(
            occurrenceNo: 1,
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

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->andReturn(new OccurrenceCollection(items: [$dto], total: 1, offset: 0));

        $this->get('/occurrences/1')->assertOk();
    }

    public function test_taxon_show_returns_200(): void
    {
        $this->get('/taxa/Tyrannosaurus')->assertOk();
    }
}
