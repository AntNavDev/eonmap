<?php

declare(strict_types=1);

namespace Tests\Unit\Api;

use App\Api\Queries\OccurrenceQuery;
use PHPUnit\Framework\TestCase;

class OccurrenceQueryTest extends TestCase
{
    public function test_to_query_params_omits_null_properties(): void
    {
        $query = new OccurrenceQuery;
        $params = $query->toQueryParams();

        $this->assertArrayNotHasKey('base_name', $params);
        $this->assertArrayNotHasKey('taxon_name', $params);
        $this->assertArrayNotHasKey('base_id', $params);
        $this->assertArrayNotHasKey('lngmin', $params);
        $this->assertArrayNotHasKey('lngmax', $params);
        $this->assertArrayNotHasKey('latmin', $params);
        $this->assertArrayNotHasKey('latmax', $params);
        $this->assertArrayNotHasKey('cc', $params);
        $this->assertArrayNotHasKey('idqual', $params);
    }

    public function test_to_query_params_always_includes_show_limit_and_offset(): void
    {
        $query = new OccurrenceQuery;
        $params = $query->toQueryParams();

        $this->assertArrayHasKey('show', $params);
        $this->assertArrayHasKey('limit', $params);
        $this->assertArrayHasKey('offset', $params);
        $this->assertSame('coords,class,loc,time', $params['show']);
        $this->assertSame(500, $params['limit']);
        $this->assertSame(0, $params['offset']);
    }

    public function test_to_query_params_maps_base_name_to_pbdb_param(): void
    {
        $query = new OccurrenceQuery(baseName: 'Dinosauria');
        $params = $query->toQueryParams();

        $this->assertArrayHasKey('base_name', $params);
        $this->assertSame('Dinosauria', $params['base_name']);
    }

    public function test_to_query_params_maps_bounding_box_to_pbdb_params(): void
    {
        $query = new OccurrenceQuery(lngMin: -180.0, lngMax: 180.0, latMin: -90.0, latMax: 90.0);
        $params = $query->toQueryParams();

        $this->assertArrayHasKey('lngmin', $params);
        $this->assertSame(-180.0, $params['lngmin']);
        $this->assertArrayHasKey('lngmax', $params);
        $this->assertSame(180.0, $params['lngmax']);
        $this->assertArrayHasKey('latmin', $params);
        $this->assertSame(-90.0, $params['latmin']);
        $this->assertArrayHasKey('latmax', $params);
        $this->assertSame(90.0, $params['latmax']);
    }

    public function test_to_query_params_includes_idqual_when_set(): void
    {
        $query = new OccurrenceQuery(idQual: 'certain');
        $params = $query->toQueryParams();

        $this->assertArrayHasKey('idqual', $params);
        $this->assertSame('certain', $params['idqual']);
    }

    public function test_to_query_params_maps_all_nullable_fields_to_correct_pbdb_names(): void
    {
        $query = new OccurrenceQuery(
            baseName: 'Aves',
            taxonName: 'Gallus gallus',
            baseId: 42,
            countryCodes: 'US,CA',
            continent: 'North America',
            interval: 'Cretaceous',
            minMa: 66.0,
            maxMa: 145.0,
            envType: 'marine',
            lithology: 'limestone',
        );
        $params = $query->toQueryParams();

        $this->assertSame('Aves', $params['base_name']);
        $this->assertSame('Gallus gallus', $params['taxon_name']);
        $this->assertSame(42, $params['base_id']);
        $this->assertSame('US,CA', $params['cc']);
        $this->assertSame('North America', $params['continent']);
        $this->assertSame('Cretaceous', $params['interval']);
        $this->assertSame(66.0, $params['min_ma']);
        $this->assertSame(145.0, $params['max_ma']);
        $this->assertSame('marine', $params['envtype']);
        $this->assertSame('limestone', $params['lithology']);
    }
}
