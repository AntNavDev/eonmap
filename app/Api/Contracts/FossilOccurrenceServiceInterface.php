<?php

declare(strict_types=1);

namespace App\Api\Contracts;

use App\Api\Queries\OccurrenceQuery;
use App\DTOs\OccurrenceCollection;

interface FossilOccurrenceServiceInterface
{
    /**
     * Retrieve fossil occurrences matching the given query.
     */
    public function getOccurrences(OccurrenceQuery $query): OccurrenceCollection;
}
