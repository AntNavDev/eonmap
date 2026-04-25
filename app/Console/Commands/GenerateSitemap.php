<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the public/sitemap.xml file';

    /**
     * Static URLs to include in the sitemap.
     *
     * @var array<int, array{path: string, changefreq: string, priority: string}>
     */
    private array $urls = [
        ['path' => '/',       'changefreq' => 'daily',  'priority' => '1.0'],
        ['path' => '/map',    'changefreq' => 'daily',  'priority' => '0.9'],
        ['path' => '/browse', 'changefreq' => 'weekly', 'priority' => '0.7'],
        ['path' => '/taxa',   'changefreq' => 'weekly', 'priority' => '0.6'],
        ['path' => '/guide',  'changefreq' => 'monthly', 'priority' => '0.5'],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $lastmod = now()->format('Y-m-d');

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $urlset = $dom->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
        $dom->appendChild($urlset);

        foreach ($this->urls as $entry) {
            $urlEl = $dom->createElement('url');

            $urlEl->appendChild($dom->createElement('loc', $baseUrl.$entry['path']));
            $urlEl->appendChild($dom->createElement('lastmod', $lastmod));
            $urlEl->appendChild($dom->createElement('changefreq', $entry['changefreq']));
            $urlEl->appendChild($dom->createElement('priority', $entry['priority']));

            $urlset->appendChild($urlEl);
        }

        $path = public_path('sitemap.xml');
        file_put_contents($path, $dom->saveXML());

        $count = count($this->urls);
        $this->info("Sitemap written to public/sitemap.xml ({$count} URLs)");

        return self::SUCCESS;
    }
}
