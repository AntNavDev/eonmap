<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Api\Exceptions\ApiException;
use App\Api\PbdbApiConnection;
use App\Models\Taxon;
use Illuminate\Console\Command;

class SeedTaxaFromPbdb extends Command
{
    protected $signature = 'taxa:seed
        {--ranks=genus,subfamily,family,superfamily,infraorder,suborder,order,superorder,subclass,class,subphylum,phylum,kingdom : Comma-separated PBDB rank names to include}
        {--chunk=500 : Records per API request}
        {--fresh : Truncate the taxa table before seeding}';

    protected $description = 'Populate the local taxa table from the PBDB /taxa/list endpoint (genus and above by default).';

    public function handle(PbdbApiConnection $connection): int
    {
        if ($this->option('fresh')) {
            Taxon::truncate();
            $this->info('Taxa table truncated.');
        }

        $ranks = (string) $this->option('ranks');
        $chunk = (int) $this->option('chunk');
        $offset = 0;
        $inserted = 0;
        $records = [];

        $this->info("Seeding taxa from PBDB (ranks: {$ranks}) …");

        // The taxa endpoint does not return a total count, so we paginate until
        // a page returns fewer records than the chunk size.
        do {
            try {
                $response = $connection->get('/taxa/list', [
                    'vocab' => 'pbdb',
                    'rank' => $ranks,
                    'show' => 'parent',
                    'all_records' => 'true',
                    'limit' => $chunk,
                    'offset' => $offset,
                ]);
            } catch (ApiException $e) {
                $this->error('PBDB request failed: '.$e->getMessage());

                return self::FAILURE;
            }

            $records = $response['records'] ?? [];

            if (empty($records)) {
                break;
            }

            $rows = array_map(static fn (array $r): array => [
                'name' => $r['taxon_name'] ?? '',
                'rank' => $r['taxon_rank'] ?? null,
                'parent_name' => $r['parent_name'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ], $records);

            // Filter out rows with empty names.
            $rows = array_filter($rows, static fn (array $r): bool => $r['name'] !== '');

            Taxon::upsert(
                array_values($rows),
                uniqueBy: ['name'],
                update: ['rank', 'parent_name', 'updated_at'],
            );

            $inserted += count($rows);
            $offset += $chunk;

            $this->line("  {$inserted} processed …");

        } while (count($records) === $chunk);

        $this->info("Done. {$inserted} taxa upserted.");

        return self::SUCCESS;
    }
}
