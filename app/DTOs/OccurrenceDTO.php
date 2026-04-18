<?php

declare(strict_types=1);

namespace App\DTOs;

class OccurrenceDTO
{
    /**
     * @param  int  $occurrenceNo  oid — PBDB occurrence number
     * @param  string  $acceptedName  tna — accepted taxon name
     * @param  string  $acceptedRank  rnk — accepted taxonomic rank
     * @param  string|null  $phylum  phl — phylum
     * @param  string|null  $class  cll — class
     * @param  string|null  $order  odl — order
     * @param  string|null  $family  fml — family
     * @param  string|null  $genus  gnl — genus
     * @param  float|null  $lat  lat — latitude
     * @param  float|null  $lng  lng — longitude
     * @param  string|null  $earlyInterval  oei — early interval name
     * @param  string|null  $lateInterval  oli — late interval name
     * @param  float|null  $maxMa  eag — early age in Ma
     * @param  float|null  $minMa  lag — late age in Ma
     * @param  string|null  $country  cc2 — ISO country code
     * @param  string|null  $state  stp — state/province
     * @param  string|null  $formation  sfm — stratigraphic formation
     * @param  string|null  $environment  env — depositional environment
     * @param  int  $collectionNo  cid — PBDB collection number
     */
    public function __construct(
        public readonly int $occurrenceNo,
        public readonly string $acceptedName,
        public readonly string $acceptedRank,
        public readonly ?string $phylum,
        public readonly ?string $class,
        public readonly ?string $order,
        public readonly ?string $family,
        public readonly ?string $genus,
        public readonly ?float $lat,
        public readonly ?float $lng,
        public readonly ?string $earlyInterval,
        public readonly ?string $lateInterval,
        public readonly ?float $maxMa,
        public readonly ?float $minMa,
        public readonly ?string $country,
        public readonly ?string $state,
        public readonly ?string $formation,
        public readonly ?string $environment,
        public readonly int $collectionNo,
    ) {}

    /**
     * Create from a PBDB compact-vocabulary record array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        return new static(
            occurrenceNo: (int) $data['oid'],
            acceptedName: $data['tna'],
            acceptedRank: $data['rnk'],
            phylum: $data['phl'] ?? null,
            class: $data['cll'] ?? null,
            order: $data['odl'] ?? null,
            family: $data['fml'] ?? null,
            genus: $data['gnl'] ?? null,
            lat: isset($data['lat']) ? (float) $data['lat'] : null,
            lng: isset($data['lng']) ? (float) $data['lng'] : null,
            earlyInterval: $data['oei'] ?? null,
            lateInterval: $data['oli'] ?? null,
            maxMa: isset($data['eag']) ? (float) $data['eag'] : null,
            minMa: isset($data['lag']) ? (float) $data['lag'] : null,
            country: $data['cc2'] ?? null,
            state: $data['stp'] ?? null,
            formation: $data['sfm'] ?? null,
            environment: $data['env'] ?? null,
            collectionNo: (int) $data['cid'],
        );
    }
}
