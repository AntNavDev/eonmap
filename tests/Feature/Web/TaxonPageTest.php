<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\DTOs\OccurrenceCollection;
use App\DTOs\OccurrenceDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /**
     * Build a small collection of Tyrannosaurus occurrences for use in tests.
     */
    private function mockCollection(): void
    {
        $dto = new OccurrenceDTO(
            occurrenceNo: 1001,
            acceptedName: 'Tyrannosaurus',
            acceptedRank: 'genus',
            phylum: 'Chordata',
            class: 'Reptilia',
            order: 'Saurischia',
            family: 'Tyrannosauridae',
            genus: 'Tyrannosaurus',
            lat: 46.0,
            lng: -104.0,
            earlyInterval: 'Maastrichtian',
            lateInterval: 'Maastrichtian',
            maxMa: 66.0,
            minMa: 65.5,
            country: 'US',
            state: 'Montana',
            formation: 'Hell Creek',
            environment: 'fluvial-channel',
            collectionNo: 500,
        );

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->andReturn(new OccurrenceCollection(items: [$dto], total: 1, offset: 0));
    }

    public function test_taxon_page_returns_200_for_valid_name(): void
    {
        $this->mockCollection();

        $this->get('/taxa/Tyrannosaurus')->assertOk();
    }

    public function test_taxon_page_contains_taxon_name_in_header(): void
    {
        $this->mockCollection();

        $this->get('/taxa/Tyrannosaurus')->assertSee('Tyrannosaurus');
    }

    public function test_taxon_page_contains_period_chart_canvas(): void
    {
        $this->mockCollection();

        $this->get('/taxa/Tyrannosaurus')->assertSee('period-chart');
    }

    public function test_taxon_page_contains_timeline_div(): void
    {
        $this->mockCollection();

        $this->get('/taxa/Tyrannosaurus')->assertSee('taxon-timeline');
    }

    public function test_taxon_page_contains_map_div(): void
    {
        $this->mockCollection();

        $this->get('/taxa/Tyrannosaurus')->assertSee('taxon-map');
    }

    public function test_taxon_page_contains_classification_summary_table(): void
    {
        $this->mockCollection();

        $response = $this->get('/taxa/Tyrannosaurus');
        $response->assertSee('Classification Summary');
        $response->assertSee('Chordata');
        $response->assertSee('Reptilia');
    }
}
