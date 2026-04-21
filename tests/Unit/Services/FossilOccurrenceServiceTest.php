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
            'oid' => 'occ:1',
            'tna' => 'Dinosauria',
            'rnk' => 10,
            'cid' => 'col:10',
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

    /**
     * PBDB returns total matches in `records_found`, which may be much larger
     * than the number of records actually returned (capped by `limit`).
     */
    public function test_records_found_is_mapped_to_collection_total(): void
    {
        $connection = $this->createStub(PbdbApiConnection::class);
        $connection->method('get')->willReturn([
            'records' => $this->sampleRecords,
            'records_found' => 12345,
        ]);

        $service = new FossilOccurrenceService($connection);
        $result = $service->getOccurrences(new OccurrenceQuery);

        $this->assertSame(12345, $result->total);
    }

    public function test_total_falls_back_to_record_count_when_records_found_absent(): void
    {
        $connection = $this->createStub(PbdbApiConnection::class);
        // Response has no records_found key — happens on empty result sets
        $connection->method('get')->willReturn([
            'records' => $this->sampleRecords,
        ]);

        $service = new FossilOccurrenceService($connection);
        $result = $service->getOccurrences(new OccurrenceQuery);

        $this->assertSame(1, $result->total);
    }

    public function test_empty_records_key_returns_empty_collection(): void
    {
        $connection = $this->createStub(PbdbApiConnection::class);
        $connection->method('get')->willReturn(['records_found' => 0]);

        $service = new FossilOccurrenceService($connection);
        $result = $service->getOccurrences(new OccurrenceQuery);

        $this->assertCount(0, $result->items);
        $this->assertSame(0, $result->total);
    }

    public function test_offset_is_passed_to_collection_from_query(): void
    {
        $connection = $this->createStub(PbdbApiConnection::class);
        $connection->method('get')->willReturn([
            'records' => $this->sampleRecords,
            'records_found' => 100,
        ]);

        $service = new FossilOccurrenceService($connection);
        $query = new OccurrenceQuery(offset: 50);
        $result = $service->getOccurrences($query);

        $this->assertSame(50, $result->offset);
    }

    public function test_different_queries_produce_separate_cache_entries(): void
    {
        $connection = $this->createMock(PbdbApiConnection::class);
        // Both queries should hit the API since their cache keys differ.
        $connection->expects($this->exactly(2))
            ->method('get')
            ->willReturn(['records' => $this->sampleRecords]);

        $service = new FossilOccurrenceService($connection);
        $service->getOccurrences(new OccurrenceQuery(baseName: 'Dinosauria'));
        $service->getOccurrences(new OccurrenceQuery(baseName: 'Mammalia'));
    }
}
