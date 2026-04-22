<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Taxon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaxaSearchController extends Controller
{
    /**
     * Return up to 10 taxa whose names begin with the given query string.
     *
     * Used by the taxa index page autocomplete. Returns an empty array if the
     * query is fewer than 2 characters or the taxa table has not been seeded.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $results = Taxon::where('name', 'like', addcslashes($q, '%_').'%')
            ->orderBy('name')
            ->limit(10)
            ->get(['name', 'rank', 'parent_name']);

        return response()->json($results);
    }
}
