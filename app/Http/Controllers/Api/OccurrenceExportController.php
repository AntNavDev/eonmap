<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Queries\OccurrenceQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\OccurrenceIndexRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OccurrenceExportController extends Controller
{
    public function __construct(
        private readonly FossilOccurrenceServiceInterface $service,
    ) {}

    /**
     * Stream a CSV export of fossil occurrences matching the given filters.
     */
    public function csv(OccurrenceIndexRequest $request): StreamedResponse
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
            limit: 5000,
            offset: 0,
        );

        $collection = $this->service->getOccurrences($query);

        return response()->streamDownload(function () use ($collection) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'occurrence_no', 'accepted_name', 'accepted_rank',
                'phylum', 'class', 'order', 'family', 'genus',
                'lat', 'lng', 'country', 'state', 'formation', 'environment',
                'early_interval', 'late_interval', 'max_ma', 'min_ma', 'collection_no',
            ]);

            foreach ($collection->items as $dto) {
                fputcsv($handle, [
                    $dto->occurrenceNo,
                    $dto->acceptedName,
                    $dto->acceptedRank,
                    $dto->phylum,
                    $dto->class,
                    $dto->order,
                    $dto->family,
                    $dto->genus,
                    $dto->lat,
                    $dto->lng,
                    $dto->country,
                    $dto->state,
                    $dto->formation,
                    $dto->environment,
                    $dto->earlyInterval,
                    $dto->lateInterval,
                    $dto->maxMa,
                    $dto->minMa,
                    $dto->collectionNo,
                ]);
            }

            fclose($handle);
        }, 'eonmap-occurrences.csv', ['Content-Type' => 'text/csv']);
    }
}
