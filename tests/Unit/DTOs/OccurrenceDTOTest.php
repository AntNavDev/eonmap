<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\OccurrenceDTO;
use PHPUnit\Framework\TestCase;

class OccurrenceDTOTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $fullRecord = [
        'oid' => '123',
        'tna' => 'Tyrannosaurus rex',
        'rnk' => 'species',
        'phl' => 'Chordata',
        'cll' => 'Reptilia',
        'odl' => 'Saurischia',
        'fml' => 'Tyrannosauridae',
        'gnl' => 'Tyrannosaurus',
        'lat' => '46.123',
        'lng' => '-104.456',
        'oei' => 'Maastrichtian',
        'oli' => 'Maastrichtian',
        'eag' => '66.0',
        'lag' => '65.5',
        'cc2' => 'US',
        'stp' => 'Montana',
        'sfm' => 'Hell Creek',
        'env' => 'fluvial-channel',
        'cid' => '456',
    ];

    public function test_from_array_maps_compact_vocabulary_keys_to_dto_properties(): void
    {
        $dto = OccurrenceDTO::fromArray($this->fullRecord);

        $this->assertSame(123, $dto->occurrenceNo);
        $this->assertSame('Tyrannosaurus rex', $dto->acceptedName);
        $this->assertSame('species', $dto->acceptedRank);
        $this->assertSame('Chordata', $dto->phylum);
        $this->assertSame('Reptilia', $dto->class);
        $this->assertSame('Saurischia', $dto->order);
        $this->assertSame('Tyrannosauridae', $dto->family);
        $this->assertSame('Tyrannosaurus', $dto->genus);
        $this->assertSame('Maastrichtian', $dto->earlyInterval);
        $this->assertSame('Maastrichtian', $dto->lateInterval);
        $this->assertSame('US', $dto->country);
        $this->assertSame('Montana', $dto->state);
        $this->assertSame('Hell Creek', $dto->formation);
        $this->assertSame('fluvial-channel', $dto->environment);
        $this->assertSame(456, $dto->collectionNo);
    }

    public function test_from_array_handles_nullable_fields_when_keys_are_absent(): void
    {
        $minimal = [
            'oid' => '1',
            'tna' => 'Trilobita',
            'rnk' => 'class',
            'cid' => '2',
        ];

        $dto = OccurrenceDTO::fromArray($minimal);

        $this->assertNull($dto->phylum);
        $this->assertNull($dto->class);
        $this->assertNull($dto->order);
        $this->assertNull($dto->family);
        $this->assertNull($dto->genus);
        $this->assertNull($dto->lat);
        $this->assertNull($dto->lng);
        $this->assertNull($dto->earlyInterval);
        $this->assertNull($dto->lateInterval);
        $this->assertNull($dto->maxMa);
        $this->assertNull($dto->minMa);
        $this->assertNull($dto->country);
        $this->assertNull($dto->state);
        $this->assertNull($dto->formation);
        $this->assertNull($dto->environment);
    }

    public function test_from_array_casts_oid_and_cid_to_int(): void
    {
        $dto = OccurrenceDTO::fromArray($this->fullRecord);

        $this->assertIsInt($dto->occurrenceNo);
        $this->assertIsInt($dto->collectionNo);
        $this->assertSame(123, $dto->occurrenceNo);
        $this->assertSame(456, $dto->collectionNo);
    }

    public function test_from_array_casts_lat_and_lng_to_float(): void
    {
        $dto = OccurrenceDTO::fromArray($this->fullRecord);

        $this->assertIsFloat($dto->lat);
        $this->assertIsFloat($dto->lng);
        $this->assertEqualsWithDelta(46.123, $dto->lat, 0.0001);
        $this->assertEqualsWithDelta(-104.456, $dto->lng, 0.0001);
    }
}
