<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Api\Exceptions\ApiException;
use App\Api\PbdbApiConnection;
use App\Api\Queries\OccurrenceQuery;
use App\Api\Services\FossilOccurrenceService;
use App\DTOs\OccurrenceCollection;
use App\DTOs\OccurrenceDTO;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FossilOccurrenceServiceTest extends TestCase
{
    /** @var array<int, array<string, mixed>> */
    private array $sampleRecords = [
        [
            'oid' => '1',
            'tna' => 'Dinosauria',
            'rnk' => 'clade',
            'cid' => '10',
            'lat' => '40.0',
            'lng' => '-100.0',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_returns_occurrence_collection_with_mapped_dto_instances(): void
    {
        $connection = $this->createStub(PbdbApiConnection::class);
        $connection->method('get')->willReturn(['records' => $this->sampleRecords]);

        $service = new FossilOccurrenceService($connection);
        $result = $service->getOccurrences(new OccurrenceQuery);

        $this->assertInstanceOf(OccurrenceCollection::class, $result);
        $this->assertCount(1, $result->items);
        $this->assertInstanceOf(OccurrenceDTO::class, $result->items[0]);
        $this->assertSame(1, $result->items[0]->occurrenceNo);
    }

    public function test_result_served_from_cache_on_second_call(): void
    {
        $connection = $this->createMock(PbdbApiConnection::class);
        // Connection must only be called once despite two getOccurrences calls.
        $connection->expects($this->once())
            ->method('get')
            ->willReturn(['records' => $this->sampleRecords]);

        $service = new FossilOccurrenceService($connection);
        $query = new OccurrenceQuery;

        $first = $service->getOccurrences($query);
        $second = $service->getOccurrences($query);

        $this->assertInstanceOf(OccurrenceCollection::class, $first);
        $this->assertInstanceOf(OccurrenceCollection::class, $second);
    }

    public function test_throws_api_exception_when_connection_throws(): void
    {
        $connection = $this->createStub(PbdbApiConnection::class);
        $connection->method('get')->willThrowException(new ApiException('Connection failed', 503));

        $service = new FossilOccurrenceService($connection);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Connection failed');

        $service->getOccurrences(new OccurrenceQuery);
    }
}
