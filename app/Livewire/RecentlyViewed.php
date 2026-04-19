<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\Api\Queries\OccurrenceQuery;
use App\Models\RecentlyViewed as RecentlyViewedModel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class RecentlyViewed extends Component
{
    /** @var array<int, array{occurrence_no: int, name: string}> */
    public array $items = [];

    public function mount(): void
    {
        $sessionId = session()->getId();

        $records = RecentlyViewedModel::where('session_id', $sessionId)
            ->orderByDesc('viewed_at')
            ->limit(5)
            ->get();

        /** @var FossilOccurrenceServiceInterface $service */
        $service = app(FossilOccurrenceServiceInterface::class);

        $this->items = $records->map(function (RecentlyViewedModel $record) use ($service): array {
            $cacheKey = 'occ_name_'.$record->occurrence_no;

            $name = Cache::remember($cacheKey, 86400, function () use ($service, $record): ?string {
                try {
                    $query = new OccurrenceQuery(
                        occId: $record->occurrence_no,
                        show: '',
                        limit: 1,
                    );

                    $collection = $service->getOccurrences($query);

                    return $collection->items[0]->acceptedName ?? null;
                } catch (ApiException) {
                    return null;
                }
            });

            return [
                'occurrence_no' => $record->occurrence_no,
                'name' => $name ?? 'Occurrence #'.$record->occurrence_no,
            ];
        })->toArray();
    }

    public function render(): View
    {
        return view('livewire.recently-viewed');
    }
}
