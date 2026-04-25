<?php

declare(strict_types=1);

namespace App\DTOs;

class OccurrenceDTO
{
    /**
     * @param  int  $occurrenceNo  oid — PBDB occurrence number
     * @param  string|null  $acceptedName  tna — accepted taxon name (absent for some PBDB records)
     * @param  string|null  $acceptedRank  rnk — accepted taxonomic rank (absent for some PBDB records)
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
     * @param  float|null  $paleolat  pla — reconstructed paleolatitude
     * @param  float|null  $paleolng  plo — reconstructed paleolongitude
     */
    public function __construct(
        public readonly int $occurrenceNo,
        public readonly ?string $acceptedName,
        public readonly ?string $acceptedRank,
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
        public readonly ?float $paleolat = null,
        public readonly ?float $paleolng = null,
    ) {}

    /**
     * Map PBDB compact-vocabulary numeric rank codes to human-readable labels.
     * Source: https://paleobiodb.org/data1.2/taxa/ranks.json
     *
     * @var array<int, string>
     */
    private const RANK_LABELS = [
        3 => 'subspecies',
        5 => 'species',
        9 => 'subgenus',
        10 => 'genus',
        13 => 'tribe',
        14 => 'subfamily',
        15 => 'family',
        16 => 'superfamily',
        17 => 'infraorder',
        18 => 'suborder',
        19 => 'order',
        20 => 'superorder',
        21 => 'infraclass',
        22 => 'subclass',
        23 => 'class',
        24 => 'superclass',
        25 => 'subphylum',
        26 => 'phylum',
        27 => 'superphylum',
        29 => 'kingdom',
    ];

    /**
     * Parse a PBDB compact-vocabulary ID field, which may be a bare integer
     * or a prefixed string like "occ:41524" or "col:3257".
     */
    private static function parseId(mixed $value): int
    {
        $str = (string) $value;

        if (str_contains($str, ':')) {
            return (int) explode(':', $str, 2)[1];
        }

        return (int) $str;
    }

    /**
     * Create from a PBDB compact-vocabulary record array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $rawRank = $data['rnk'] ?? null;
        $acceptedRank = match (true) {
            $rawRank === null => null,
            is_int($rawRank) => self::RANK_LABELS[$rawRank] ?? (string) $rawRank,
            default => (string) $rawRank,
        };

        return new static(
            occurrenceNo: self::parseId($data['oid']),
            acceptedName: $data['tna'] ?? null,
            acceptedRank: $acceptedRank,
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
            collectionNo: self::parseId($data['cid']),
            paleolat: isset($data['pla']) ? (float) $data['pla'] : null,
            paleolng: isset($data['plo']) ? (float) $data['plo'] : null,
        );
    }
}
