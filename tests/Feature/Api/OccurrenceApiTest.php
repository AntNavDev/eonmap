<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\PbdbApiConnection;
use App\DTOs\OccurrenceCollection;
use Mockery\MockInterface;
use Tests\TestCase;

class OccurrenceApiTest extends TestCase
{
    public function test_index_returns_422_when_no_filter_parameters_provided(): void
    {
        $this->getJson('/api/occurrences')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['filters']);
    }

    public function test_index_returns_200_with_valid_filter(): void
    {
        $collection = new OccurrenceCollection(items: [], total: 0, offset: 0);

        $this->mock(FossilOccurrenceServiceInterface::class, function (MockInterface $mock) use ($collection) {
            $mock->shouldReceive('getOccurrences')->once()->andReturn($collection);
        });

        $this->getJson('/api/occurrences?base_name=Dinosauria')
            ->assertOk()
            ->assertJsonStructure(['items', 'total', 'offset']);
    }

    public function test_show_returns_404_json_when_occurrence_not_found(): void
    {
        $this->mock(PbdbApiConnection::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(['records' => []]);
        });

        $this->getJson('/api/occurrences/99999')
            ->assertNotFound()
            ->assertJson(['message' => 'Occurrence not found.']);
    }

    public function test_csv_export_returns_correct_headers(): void
    {
        $collection = new OccurrenceCollection(items: [], total: 0, offset: 0);

        $this->mock(FossilOccurrenceServiceInterface::class, function (MockInterface $mock) use ($collection) {
            $mock->shouldReceive('getOccurrences')->once()->andReturn($collection);
        });

        $response = $this->get('/api/export/occurrences?base_name=Dinosauria');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString(
            'eonmap-occurrences.csv',
            $response->headers->get('Content-Disposition')
        );
    }
}
