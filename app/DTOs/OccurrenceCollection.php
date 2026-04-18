<?php

declare(strict_types=1);

namespace App\DTOs;

class OccurrenceCollection
{
    /**
     * @param  OccurrenceDTO[]  $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $total,
        public readonly int $offset,
    ) {}

    /**
     * Create from a PBDB records array.
     *
     * @param  array<int, array<string, mixed>>  $records
     */
    public static function fromArray(array $records, int $total = 0, int $offset = 0): static
    {
        return new static(
            items: array_map(
                static fn (array $record) => OccurrenceDTO::fromArray($record),
                $records
            ),
            total: $total,
            offset: $offset,
        );
    }
}
