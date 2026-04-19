<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\DTOs\OccurrenceCollection;
use App\DTOs\OccurrenceDTO;
use App\Livewire\RecentlyViewed;
use App\Models\RecentlyViewed as RecentlyViewedModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RecentlyViewedTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_mounts_without_error(): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->andReturn(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(RecentlyViewed::class)->assertOk();
    }

    public function test_renders_nothing_viewed_when_session_has_no_records(): void
    {
        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->andReturn(new OccurrenceCollection(items: [], total: 0, offset: 0));

        Livewire::test(RecentlyViewed::class)
            ->assertSee('Nothing viewed yet');
    }

    public function test_renders_occurrence_links_when_records_exist(): void
    {
        $session = session();

        RecentlyViewedModel::create([
            'session_id' => $session->getId(),
            'occurrence_no' => 42,
            'viewed_at' => now(),
        ]);

        $dto = new OccurrenceDTO(
            occurrenceNo: 42,
            acceptedName: 'Triceratops',
            acceptedRank: 'genus',
            phylum: null,
            class: null,
            order: null,
            family: null,
            genus: 'Triceratops',
            lat: null,
            lng: null,
            earlyInterval: null,
            lateInterval: null,
            maxMa: null,
            minMa: null,
            country: null,
            state: null,
            formation: null,
            environment: null,
            collectionNo: 99,
        );

        $this->mock(FossilOccurrenceServiceInterface::class)
            ->shouldReceive('getOccurrences')
            ->andReturn(new OccurrenceCollection(items: [$dto], total: 1, offset: 0));

        Livewire::test(RecentlyViewed::class)
            ->assertSee('Triceratops')
            ->assertSee('42');
    }
}
