<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\Api\Queries\OccurrenceQuery;
use App\DTOs\OccurrenceCollection;
use Illuminate\Contracts\View\View;

class TaxonController extends Controller
{
    /**
     * Display the taxon page for the given taxon name.
     */
    public function show(string $name): View
    {
        /** @var FossilOccurrenceServiceInterface $service */
        $service = app(FossilOccurrenceServiceInterface::class);

        try {
            $query = new OccurrenceQuery(
                baseName: $name,
                show: 'coords,class,loc,time',
                limit: 1000,
            );

            $occurrences = $service->getOccurrences($query);
        } catch (ApiException) {
            $occurrences = new OccurrenceCollection(items: [], total: 0, offset: 0);
        }

        $totalCount = count($occurrences->items);

        // Compute classification breakdown from the occurrence set.
        $byPhylum = [];
        $byClass = [];
        $byEnvironment = [];

        foreach ($occurrences->items as $occ) {
            if ($occ->phylum !== null) {
                $byPhylum[$occ->phylum] = ($byPhylum[$occ->phylum] ?? 0) + 1;
            }
            if ($occ->class !== null) {
                $byClass[$occ->class] = ($byClass[$occ->class] ?? 0) + 1;
            }
            if ($occ->environment !== null) {
                $byEnvironment[$occ->environment] = ($byEnvironment[$occ->environment] ?? 0) + 1;
            }
        }

        arsort($byPhylum);
        arsort($byClass);
        arsort($byEnvironment);

        return view('taxa.show', compact(
            'name',
            'occurrences',
            'totalCount',
            'byPhylum',
            'byClass',
            'byEnvironment',
        ))->with('title', $name);
    }
}
