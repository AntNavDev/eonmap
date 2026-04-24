<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\OccurrenceFilters;
use Livewire\Livewire;
use Tests\TestCase;

class OccurrenceFiltersValidationTest extends TestCase
{
    private const ERROR = 'Please select an organism or time period to search.';

    public function test_apply_filters_without_primary_filter_sets_validation_error(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('applyFilters')
            ->assertSet('validationError', self::ERROR);
    }

    public function test_apply_filters_without_primary_filter_does_not_dispatch_event(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('applyFilters')
            ->assertNotDispatched('apply-filters');
    }

    public function test_environment_only_is_not_sufficient_to_apply_filters(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->set('envTypes', ['terr'])
            ->call('applyFilters')
            ->assertSet('validationError', self::ERROR)
            ->assertNotDispatched('apply-filters');
    }

    public function test_country_only_is_not_sufficient_to_apply_filters(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->set('countryCodes', 'US')
            ->call('applyFilters')
            ->assertSet('validationError', self::ERROR)
            ->assertNotDispatched('apply-filters');
    }

    public function test_organism_alone_passes_validation(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->set('baseName', 'Dinosauria')
            ->call('applyFilters')
            ->assertSet('validationError', null)
            ->assertDispatched('apply-filters');
    }

    public function test_named_interval_alone_passes_validation(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->set('interval', 'Cretaceous')
            ->call('applyFilters')
            ->assertSet('validationError', null)
            ->assertDispatched('apply-filters');
    }

    public function test_age_range_alone_passes_validation(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->set('minMa', 66.0)
            ->call('applyFilters')
            ->assertSet('validationError', null)
            ->assertDispatched('apply-filters');
    }

    public function test_setting_organism_after_failed_validation_clears_error(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('applyFilters')
            ->assertSet('validationError', self::ERROR)
            ->set('baseName', 'Mammalia')
            ->assertSet('validationError', null);
    }

    public function test_reset_filters_clears_validation_error(): void
    {
        Livewire::test(OccurrenceFilters::class)
            ->call('applyFilters')
            ->assertSet('validationError', self::ERROR)
            ->call('resetFilters')
            ->assertSet('validationError', null);
    }
}