<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\Api\PbdbApiConnection;
use App\Api\Queries\OccurrenceQuery;
use App\DTOs\OccurrenceDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\OccurrenceIndexRequest;
use App\Models\RecentlyViewed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OccurrenceApiController extends Controller
{
    public function __construct(
        private readonly FossilOccurrenceServiceInterface $service,
        private readonly PbdbApiConnection $connection,
    ) {}

    /**
     * List fossil occurrences matching the given filters.
     */
    public function index(OccurrenceIndexRequest $request): JsonResponse
    {
        $query = new OccurrenceQuery(
            baseName: $request->input('base_name'),
            taxonName: $request->input('taxon_name'),
            baseId: $request->filled('base_id') ? (int) $request->input('base_id') : null,
            lngMin: $request->filled('lngmin') ? (float) $request->input('lngmin') : null,
            lngMax: $request->filled('lngmax') ? (float) $request->input('lngmax') : null,
            latMin: $request->filled('latmin') ? (float) $request->input('latmin') : null,
            latMax: $request->filled('latmax') ? (float) $request->input('latmax') : null,
            countryCodes: $request->input('cc'),
            continent: $request->input('continent'),
            interval: $request->input('interval'),
            minMa: $request->filled('min_ma') ? (float) $request->input('min_ma') : null,
            maxMa: $request->filled('max_ma') ? (float) $request->input('max_ma') : null,
            envType: $request->input('envtype'),
            lithology: $request->input('lithology'),
            idQual: $request->input('idqual'),
            limit: $request->filled('limit') ? (int) $request->input('limit') : 500,
            offset: $request->filled('offset') ? (int) $request->input('offset') : 0,
        );

        $collection = $this->service->getOccurrences($query);

        return response()->json($collection);
    }

    /**
     * Return a single occurrence by PBDB occurrence number.
     *
     * Records the occurrence in `recently_viewed` for the current session.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $response = $this->connection->get('/occs/list', ['occ_id' => $id, 'show' => 'full']);
        } catch (ApiException) {
            return response()->json(['message' => 'Failed to fetch occurrence.'], 500);
        }

        $records = $response['records'] ?? [];

        if (empty($records)) {
            return response()->json(['message' => 'Occurrence not found.'], 404);
        }

        $dto = OccurrenceDTO::fromArray($records[0]);

        RecentlyViewed::create([
            'session_id' => $request->session()->getId(),
            'occurrence_no' => $id,
            'viewed_at' => now(),
        ]);

        return response()->json($dto);
    }
}
