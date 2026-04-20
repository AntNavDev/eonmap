<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\OccurrenceFilters;
use App\Presets\SearchPreset;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OccurrenceFiltersPresetTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Preset loading — property state
    // -------------------------------------------------------------------------

    public function test_load_preset_populates_base_name(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('loadPreset', 'age-of-dinosaurs')
            ->assertSet('baseName', 'Dinosauria');
    }

    public function test_load_preset_populates_age_range(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('loadPreset', 'age-of-dinosaurs')
            ->assertSet('minMa', 66.0)
            ->assertSet('maxMa', 252.0);
    }

    public function test_load_preset_populates_interval(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('loadPreset', 't-rex-country')
            ->assertSet('interval', 'Cretaceous');
    }

    public function test_load_preset_populates_country_codes(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('loadPreset', 't-rex-country')
            ->assertSet('countryCodes', 'US');
    }

    public function test_load_preset_populates_env_types(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('loadPreset', 'cambrian-seas')
            ->assertSet('envTypes', ['marine']);
    }

    public function test_load_preset_resets_custom_taxon_mode(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('enableCustomTaxon')
            ->assertSet('customTaxon', true)
            ->call('loadPreset', 'age-of-dinosaurs')
            ->assertSet('customTaxon', false);
    }

    public function test_load_preset_with_unknown_id_does_not_change_state(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->set('baseName', 'Mammalia')
            ->call('loadPreset', 'does-not-exist')
            ->assertSet('baseName', 'Mammalia');
    }

    // -------------------------------------------------------------------------
    // Preset loading — event dispatch
    // -------------------------------------------------------------------------

    public function test_load_preset_dispatches_apply_filters_event(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('loadPreset', 'age-of-dinosaurs')
            ->assertDispatched('apply-filters');
    }

    public function test_load_preset_dispatches_filters_reset_before_applying(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('loadPreset', 'age-of-dinosaurs')
            ->assertDispatched('filters-reset');
    }

    // -------------------------------------------------------------------------
    // Custom taxon mode
    // -------------------------------------------------------------------------

    public function test_enable_custom_taxon_sets_flag_and_clears_base_name(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->set('baseName', 'Dinosauria')
            ->call('enableCustomTaxon')
            ->assertSet('customTaxon', true)
            ->assertSet('baseName', '');
    }

    public function test_disable_custom_taxon_clears_flag_and_base_name(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('enableCustomTaxon')
            ->set('baseName', 'Allosauridae')
            ->call('disableCustomTaxon')
            ->assertSet('customTaxon', false)
            ->assertSet('baseName', '');
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function test_reset_clears_preset_filter_values(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('loadPreset', 'age-of-dinosaurs')
            ->call('resetFilters')
            ->assertSet('baseName', '')
            ->assertSet('interval', '')
            ->assertSet('minMa', 0.0)
            ->assertSet('maxMa', 540.0)
            ->assertSet('envTypes', [])
            ->assertSet('countryCodes', '')
            ->assertSet('customTaxon', false);
    }

    // -------------------------------------------------------------------------
    // buildQuery() correctness per preset
    // Each test loads preset state manually and asserts the generated PBDB
    // query parameters so we know the full API call chain is correct.
    // -------------------------------------------------------------------------

    #[DataProvider('presetQueryExpectations')]
    public function test_preset_builds_correct_query_params(string $presetId, array $expectedParams): void
    {
        $component = new OccurrenceFilters;

        // Simulate what loadPreset() sets without triggering Livewire dispatch
        $preset = SearchPreset::find($presetId);
        $component->baseName = $preset->baseName ?? '';
        $component->interval = $preset->interval ?? '';
        $component->minMa = $preset->minMa ?? 0;
        $component->maxMa = $preset->maxMa ?? 540;
        $component->envTypes = $preset->envTypes;
        $component->countryCodes = $preset->countryCodes ?? '';

        $params = $component->buildQuery()->toQueryParams();

        foreach ($expectedParams as $key => $value) {
            $this->assertArrayHasKey($key, $params, "Expected param '{$key}' missing from query.");
            $this->assertSame($value, $params[$key], "Param '{$key}' has wrong value for preset '{$presetId}'.");
        }
    }

    /** @return array<string, array{string, array<string, mixed>}> */
    public static function presetQueryExpectations(): array
    {
        return [
            'age-of-dinosaurs' => [
                'age-of-dinosaurs',
                ['base_name' => 'Dinosauria', 'min_ma' => 66.0, 'max_ma' => 252.0],
            ],
            't-rex-country' => [
                't-rex-country',
                ['base_name' => 'Tyrannosauridae', 'interval' => 'Cretaceous', 'cc' => 'US'],
            ],
            'ice-age-giants' => [
                'ice-age-giants',
                ['base_name' => 'Mammalia', 'max_ma' => 2.6],
            ],
            'rise-of-mammals' => [
                'rise-of-mammals',
                ['base_name' => 'Mammalia', 'min_ma' => 23.0, 'max_ma' => 66.0],
            ],
            'trilobite-world' => [
                'trilobite-world',
                ['base_name' => 'Trilobita'],
            ],
            'ammonites' => [
                'ammonites',
                ['base_name' => 'Ammonoidea'],
            ],
            'cambrian-seas' => [
                'cambrian-seas',
                ['interval' => 'Cambrian', 'envtype' => 'marine'],
            ],
            'great-dying' => [
                'great-dying',
                ['min_ma' => 245.0, 'max_ma' => 260.0],
            ],
        ];
    }
}
