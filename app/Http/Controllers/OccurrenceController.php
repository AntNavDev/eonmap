<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\Api\Queries\OccurrenceQuery;
use App\Models\RecentlyViewed;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OccurrenceController extends Controller
{
    /**
     * Display the detail page for a single PBDB occurrence.
     *
     * Records the visit in recently_viewed and aborts with 404 if the
     * occurrence cannot be found.
     */
    public function show(Request $request, int $id): View
    {
        /** @var FossilOccurrenceServiceInterface $service */
        $service = app(FossilOccurrenceServiceInterface::class);

        try {
            $query = new OccurrenceQuery(
                occId: $id,
                show: 'coords,class,loc,time,paleoloc',
                limit: 1,
                offset: 0,
            );

            $collection = $service->getOccurrences($query);
        } catch (ApiException) {
            abort(404);
        }

        if (empty($collection->items)) {
            abort(404);
        }

        $occurrence = $collection->items[0];

        RecentlyViewed::create([
            'session_id' => $request->session()->getId(),
            'occurrence_no' => $id,
            'viewed_at' => now(),
        ]);

        return view('occurrences.show', compact('occurrence'));
    }
}
