<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class GenerateSitemapTest extends TestCase
{
    private string $sitemapPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sitemapPath = public_path('sitemap.xml');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->sitemapPath)) {
            unlink($this->sitemapPath);
        }

        parent::tearDown();
    }

    public function test_it_creates_sitemap_xml(): void
    {
        Artisan::call('sitemap:generate');

        $this->assertFileExists($this->sitemapPath);
    }

    public function test_it_contains_all_static_urls(): void
    {
        Artisan::call('sitemap:generate');

        $content = file_get_contents($this->sitemapPath);

        $appUrl = rtrim(config('app.url'), '/');

        $this->assertStringContainsString($appUrl.'/', $content);
        $this->assertStringContainsString($appUrl.'/map', $content);
        $this->assertStringContainsString($appUrl.'/browse', $content);
    }

    public function test_each_url_block_contains_required_elements(): void
    {
        Artisan::call('sitemap:generate');

        $xml = simplexml_load_file($this->sitemapPath);

        $this->assertNotFalse($xml, 'sitemap.xml is not valid XML');

        $this->assertGreaterThanOrEqual(3, count($xml->url));

        foreach ($xml->url as $url) {
            $this->assertNotEmpty((string) $url->loc, '<loc> is missing or empty');
            $this->assertNotEmpty((string) $url->lastmod, '<lastmod> is missing or empty');
            $this->assertNotEmpty((string) $url->changefreq, '<changefreq> is missing or empty');
            $this->assertNotEmpty((string) $url->priority, '<priority> is missing or empty');
        }
    }

    public function test_loc_values_use_app_url_not_hardcoded_domain(): void
    {
        config(['app.url' => 'https://testing.example.com']);

        Artisan::call('sitemap:generate');

        $content = file_get_contents($this->sitemapPath);

        $this->assertStringContainsString('https://testing.example.com', $content);
        $this->assertStringNotContainsString('eonmap.com', $content);
    }
}
