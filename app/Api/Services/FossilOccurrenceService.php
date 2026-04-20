<?php

declare(strict_types=1);

namespace App\Api\Services;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\Api\PbdbApiConnection;
use App\Api\Queries\OccurrenceQuery;
use App\DTOs\OccurrenceCollection;
use Illuminate\Support\Facades\Cache;

class FossilOccurrenceService implements FossilOccurrenceServiceInterface
{
    public function __construct(private readonly PbdbApiConnection $connection) {}

    /**
     * Retrieve fossil occurrences from PBDB, cached for one hour.
     *
     * @throws ApiException
     */
    public function getOccurrences(OccurrenceQuery $query): OccurrenceCollection
    {
        $params = $query->toQueryParams();
        $cacheKey = 'pbdb_occs_'.md5(serialize($params));

        return Cache::remember($cacheKey, 3600, function () use ($params) {
            $response = $this->connection->get('/occs/list', $params);
            $records = $response['records'] ?? [];
            $total = (int) ($response['records_found'] ?? count($records));
            $offset = (int) ($params['offset'] ?? 0);

            return OccurrenceCollection::fromArray($records, $total, $offset);
        });
    }
}
