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

    /**
     * PBDB returns oid/cid as prefixed strings like "occ:41524" and "col:3257".
     * Casting these directly with (int) yields 0, breaking all occurrence links.
     */
    public function test_from_array_parses_prefixed_oid_format(): void
    {
        $record = array_merge($this->fullRecord, ['oid' => 'occ:41524']);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertSame(41524, $dto->occurrenceNo);
    }

    public function test_from_array_parses_prefixed_cid_format(): void
    {
        $record = array_merge($this->fullRecord, ['cid' => 'col:3257']);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertSame(3257, $dto->collectionNo);
    }

    public function test_from_array_parses_both_prefixed_ids_in_same_record(): void
    {
        $record = array_merge($this->fullRecord, [
            'oid' => 'occ:41524',
            'cid' => 'col:3257',
        ]);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertSame(41524, $dto->occurrenceNo);
        $this->assertSame(3257, $dto->collectionNo);
    }

    /**
     * PBDB returns rnk as a numeric code (e.g. 5 = species, 10 = genus).
     * Passing the int directly to a string-typed property causes a TypeError.
     */
    public function test_from_array_maps_integer_rank_5_to_species(): void
    {
        $record = array_merge($this->fullRecord, ['rnk' => 5]);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertSame('species', $dto->acceptedRank);
    }

    public function test_from_array_maps_integer_rank_10_to_genus(): void
    {
        $record = array_merge($this->fullRecord, ['rnk' => 10]);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertSame('genus', $dto->acceptedRank);
    }

    public function test_from_array_maps_integer_rank_15_to_family(): void
    {
        $record = array_merge($this->fullRecord, ['rnk' => 15]);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertSame('family', $dto->acceptedRank);
    }

    public function test_from_array_maps_integer_rank_19_to_order(): void
    {
        $record = array_merge($this->fullRecord, ['rnk' => 19]);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertSame('order', $dto->acceptedRank);
    }

    public function test_from_array_maps_integer_rank_23_to_class(): void
    {
        $record = array_merge($this->fullRecord, ['rnk' => 23]);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertSame('class', $dto->acceptedRank);
    }

    public function test_from_array_maps_integer_rank_26_to_phylum(): void
    {
        $record = array_merge($this->fullRecord, ['rnk' => 26]);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertSame('phylum', $dto->acceptedRank);
    }

    public function test_from_array_falls_back_to_string_for_unknown_integer_rank(): void
    {
        $record = array_merge($this->fullRecord, ['rnk' => 99]);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertSame('99', $dto->acceptedRank);
    }

    public function test_from_array_preserves_string_rank_unchanged(): void
    {
        $record = array_merge($this->fullRecord, ['rnk' => 'clade']);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertSame('clade', $dto->acceptedRank);
    }

    public function test_from_array_sets_null_rank_when_rnk_key_absent(): void
    {
        $record = $this->fullRecord;
        unset($record['rnk']);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertNull($dto->acceptedRank);
    }

    public function test_from_array_sets_null_accepted_name_when_tna_key_absent(): void
    {
        $record = $this->fullRecord;
        unset($record['tna']);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertNull($dto->acceptedName);
    }

    public function test_from_array_maps_paleolat_and_paleolng(): void
    {
        $record = array_merge($this->fullRecord, [
            'pla' => '55.3',
            'plo' => '-112.7',
        ]);

        $dto = OccurrenceDTO::fromArray($record);

        $this->assertEqualsWithDelta(55.3, $dto->paleolat, 0.0001);
        $this->assertEqualsWithDelta(-112.7, $dto->paleolng, 0.0001);
    }

    public function test_paleolat_and_paleolng_are_null_when_absent(): void
    {
        $dto = OccurrenceDTO::fromArray($this->fullRecord);

        $this->assertNull($dto->paleolat);
        $this->assertNull($dto->paleolng);
    }
}
