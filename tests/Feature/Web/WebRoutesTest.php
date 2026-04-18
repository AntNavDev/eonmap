<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use Tests\TestCase;

class WebRoutesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_root_redirects_to_map(): void
    {
        $this->get('/')->assertRedirect('/map');
    }

    public function test_map_returns_200(): void
    {
        $this->get('/map')->assertOk();
    }

    public function test_browse_returns_200(): void
    {
        $this->get('/browse')->assertOk();
    }

    public function test_occurrence_show_returns_200(): void
    {
        $this->get('/occurrences/1')->assertOk();
    }

    public function test_taxon_show_returns_200(): void
    {
        $this->get('/taxa/Tyrannosaurus')->assertOk();
    }
}
