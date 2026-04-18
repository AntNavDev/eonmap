<?php

declare(strict_types=1);

namespace App\Api\Queries;

class OccurrenceQuery
{
    /**
     * @param  string|null  $baseName  base_name  — clade or taxon root
     * @param  string|null  $taxonName  taxon_name — exact taxon name
     * @param  int|null  $baseId  base_id    — PBDB taxon ID
     * @param  float|null  $lngMin  lngmin     — west bound
     * @param  float|null  $lngMax  lngmax     — east bound
     * @param  float|null  $latMin  latmin     — south bound
     * @param  float|null  $latMax  latmax     — north bound
     * @param  string|null  $countryCodes  cc         — comma-separated ISO country codes
     * @param  string|null  $continent  continent  — continent name
     * @param  string|null  $interval  interval   — geological time interval name
     * @param  float|null  $minMa  min_ma     — minimum age in Ma
     * @param  float|null  $maxMa  max_ma     — maximum age in Ma
     * @param  string|null  $envType  envtype    — depositional environment type
     * @param  string|null  $lithology  lithology  — lithology description
     * @param  string|null  $idQual  idqual     — identification quality (any|certain|uncertain)
     * @param  int|null  $occId  occ_id     — single PBDB occurrence number
     * @param  string  $show  show       — comma-separated response blocks
     * @param  int  $limit  limit      — max records per page
     * @param  int  $offset  offset     — record offset for pagination
     */
    public function __construct(
        public readonly ?int $occId = null,
        public readonly ?string $baseName = null,
        public readonly ?string $taxonName = null,
        public readonly ?int $baseId = null,
        public readonly ?float $lngMin = null,
        public readonly ?float $lngMax = null,
        public readonly ?float $latMin = null,
        public readonly ?float $latMax = null,
        public readonly ?string $countryCodes = null,
        public readonly ?string $continent = null,
        public readonly ?string $interval = null,
        public readonly ?float $minMa = null,
        public readonly ?float $maxMa = null,
        public readonly ?string $envType = null,
        public readonly ?string $lithology = null,
        public readonly ?string $idQual = null,
        public readonly string $show = 'coords,class,loc,time,paleoloc',
        public readonly int $limit = 500,
        public readonly int $offset = 0,
    ) {}

    /**
     * Convert to PBDB API query parameters, omitting null values.
     *
     * @return array<string, mixed>
     */
    public function toQueryParams(): array
    {
        $params = [
            'show' => $this->show,
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];

        $map = [
            'occId' => 'occ_id',
            'baseName' => 'base_name',
            'taxonName' => 'taxon_name',
            'baseId' => 'base_id',
            'lngMin' => 'lngmin',
            'lngMax' => 'lngmax',
            'latMin' => 'latmin',
            'latMax' => 'latmax',
            'countryCodes' => 'cc',
            'continent' => 'continent',
            'interval' => 'interval',
            'minMa' => 'min_ma',
            'maxMa' => 'max_ma',
            'envType' => 'envtype',
            'lithology' => 'lithology',
            'idQual' => 'idqual',
        ];

        foreach ($map as $property => $pbdbParam) {
            if ($this->$property !== null) {
                $params[$pbdbParam] = $this->$property;
            }
        }

        return $params;
    }
}
