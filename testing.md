# Testing

## Running Tests

```bash
./vendor/bin/sail php artisan test
./vendor/bin/sail php artisan test --filter=OccurrenceBrowserTest   # single class
./vendor/bin/sail php artisan test --filter=test_returns_occurrence  # single method
```

Tests run against a dedicated SQLite database (`:memory:`) by default. No connection to PBDB is made — all external API calls are stubbed.

## Test Structure

```
tests/
├── TestCase.php
├── Unit/
│   ├── Api/
│   │   └── OccurrenceQueryTest.php       # OccurrenceQuery::toQueryParams()
│   ├── DTOs/
│   │   └── OccurrenceDTOTest.php         # DTO mapping from raw PBDB records
│   ├── Services/
│   │   └── FossilOccurrenceServiceTest.php  # caching, error propagation
│   └── Console/
│       └── GenerateSitemapTest.php
└── Feature/
    ├── Api/
    │   └── OccurrenceApiTest.php          # JSON API endpoints (/api/occurrences)
    ├── Livewire/
    │   ├── FossilMapTest.php
    │   ├── OccurrenceBrowserTest.php
    │   └── RecentlyViewedTest.php
    └── Web/
        ├── WebRoutesTest.php              # basic route smoke tests
        ├── OccurrenceDetailTest.php
        └── TaxonPageTest.php
```

## Mocking the API Service

All Livewire and feature tests mock `FossilOccurrenceServiceInterface` so no HTTP calls are made:

```php
use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\DTOs\OccurrenceCollection;

private function mockService(OccurrenceCollection $collection): void
{
    $this->mock(FossilOccurrenceServiceInterface::class)
        ->shouldReceive('getOccurrences')
        ->andReturn($collection);
}
```

Construct an empty collection for state/pagination tests:

```php
new OccurrenceCollection(items: [], total: 0, offset: 0)
```

## Testing Livewire Components

Use `Livewire::test()` — no browser required:

```php
use App\Livewire\OccurrenceBrowser;
use Livewire\Livewire;

Livewire::test(OccurrenceBrowser::class)
    ->set('filterBaseName', 'Dinosauria')
    ->call('applyFilters')
    ->assertDispatched('browser-data-loaded');
```

Common assertions: `assertSet`, `assertDispatched`, `assertOk`, `assertSee`, `assertDontSee`.

## Testing the Service Layer (Unit)

Stub `PbdbApiConnection` directly — do not mock the service itself in unit tests:

```php
$connection = $this->createStub(PbdbApiConnection::class);
$connection->method('get')->willReturn(['records' => $this->sampleRecords]);

$service = new FossilOccurrenceService($connection);
$result  = $service->getOccurrences(new OccurrenceQuery);
```

Call `Cache::flush()` in `setUp()` so caching tests are isolated.

## What to Test

**Unit tests** — cover pure logic, no framework:
- `OccurrenceQuery::toQueryParams()` — null omission, param name mapping
- `OccurrenceDTO::fromRecord()` — field mapping, defaults for missing keys
- `FossilOccurrenceService` — cache hit (HTTP called once), cache miss, `ApiException` propagation

**Feature/Livewire tests** — cover component behaviour:
- Component mounts without error
- Filter changes trigger data load / event dispatch
- Pagination: next/prev offset math, floor at zero
- `perPage` validation rejects values outside `[25, 50, 100]`
- Sort toggle: same field flips direction, new field resets to `asc`

**Feature/Web tests** — cover HTTP layer:
- Routes return 200 for valid slugs / IDs
- Routes return 404 for missing resources
- No need to assert on HTML content — just status codes and presence of key text

## Conventions

- One assertion per test method where practical — prefer multiple focused tests over one omnibus test
- Test method names use `snake_case` and describe behaviour: `test_next_page_increments_offset_by_per_page`
- Always test the happy path and the key failure case (e.g. `ApiException` thrown)
- Do not test Blade rendering detail — test that the Livewire component sets the correct data
- `RefreshDatabase` is available but rarely needed — this app has no user-generated data
