<?php

declare(strict_types=1);

namespace Tests\Unit\Presets;

use App\Presets\SearchPreset;
use Tests\TestCase;

class SearchPresetTest extends TestCase
{
    public function test_all_returns_non_empty_list(): void
    {
        $this->assertNotEmpty(SearchPreset::all());
    }

    public function test_all_preset_ids_are_unique(): void
    {
        $ids = array_map(fn (SearchPreset $p) => $p->id, SearchPreset::all());

        $this->assertSame($ids, array_unique($ids), 'Duplicate preset IDs found.');
    }

    public function test_all_presets_have_required_fields(): void
    {
        foreach (SearchPreset::all() as $preset) {
            $this->assertNotEmpty($preset->id, 'Preset is missing an id.');
            $this->assertNotEmpty($preset->name, "Preset '{$preset->id}' is missing a name.");
            $this->assertNotEmpty($preset->description, "Preset '{$preset->id}' is missing a description.");
            $this->assertNotEmpty($preset->emoji, "Preset '{$preset->id}' is missing an emoji.");
        }
    }

    public function test_all_presets_have_at_least_one_filter_set(): void
    {
        foreach (SearchPreset::all() as $preset) {
            $hasFilter = $preset->baseName !== null
                || $preset->interval !== null
                || $preset->minMa !== null
                || $preset->maxMa !== null
                || $preset->envTypes !== []
                || $preset->countryCodes !== null;

            $this->assertTrue($hasFilter, "Preset '{$preset->id}' has no filter criteria set.");
        }
    }

    public function test_find_returns_preset_by_id(): void
    {
        $preset = SearchPreset::find('age-of-dinosaurs');

        $this->assertInstanceOf(SearchPreset::class, $preset);
        $this->assertSame('age-of-dinosaurs', $preset->id);
    }

    public function test_find_returns_null_for_unknown_id(): void
    {
        $this->assertNull(SearchPreset::find('does-not-exist'));
    }

    public function test_age_of_dinosaurs_preset(): void
    {
        $preset = SearchPreset::find('age-of-dinosaurs');

        $this->assertSame('Dinosauria', $preset->baseName);
        $this->assertSame(66.0, $preset->minMa);
        $this->assertSame(252.0, $preset->maxMa);
        $this->assertNull($preset->interval);
        $this->assertNull($preset->countryCodes);
    }

    public function test_t_rex_country_preset(): void
    {
        $preset = SearchPreset::find('t-rex-country');

        $this->assertSame('Tyrannosauridae', $preset->baseName);
        $this->assertSame('Cretaceous', $preset->interval);
        $this->assertSame('US', $preset->countryCodes);
    }

    public function test_ice_age_giants_preset(): void
    {
        $preset = SearchPreset::find('ice-age-giants');

        $this->assertSame('Mammalia', $preset->baseName);
        $this->assertSame(0.0, $preset->minMa);
        $this->assertSame(2.6, $preset->maxMa);
    }

    public function test_cambrian_seas_preset_has_marine_environment(): void
    {
        $preset = SearchPreset::find('cambrian-seas');

        $this->assertSame('Cambrian', $preset->interval);
        $this->assertContains('marine', $preset->envTypes);
        $this->assertNull($preset->baseName);
    }

    public function test_great_dying_preset_spans_permo_triassic_boundary(): void
    {
        $preset = SearchPreset::find('great-dying');

        $this->assertSame(245.0, $preset->minMa);
        $this->assertSame(260.0, $preset->maxMa);
        $this->assertNull($preset->baseName);
        $this->assertNull($preset->interval);
    }

    public function test_rise_of_mammals_preset_is_paleogene(): void
    {
        $preset = SearchPreset::find('rise-of-mammals');

        $this->assertSame('Mammalia', $preset->baseName);
        $this->assertSame(23.0, $preset->minMa);
        $this->assertSame(66.0, $preset->maxMa);
    }

    public function test_ammonites_preset(): void
    {
        $preset = SearchPreset::find('ammonites');

        $this->assertSame('Ammonoidea', $preset->baseName);
        $this->assertNull($preset->interval);
        $this->assertNull($preset->minMa);
        $this->assertNull($preset->maxMa);
    }

    public function test_trilobite_world_preset(): void
    {
        $preset = SearchPreset::find('trilobite-world');

        $this->assertSame('Trilobita', $preset->baseName);
        $this->assertNull($preset->interval);
    }
}
