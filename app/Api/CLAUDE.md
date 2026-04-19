# app/Api — API Layer

This directory contains everything needed to fetch data from external APIs. It is deliberately isolated from the rest of the application: controllers and Livewire components never touch HTTP directly — they go through a service interface.

## Structure

```
app/Api/
├── AbstractApiConnection.php          # Base HTTP client (wraps Laravel Http facade)
├── PbdbApiConnection.php              # PBDB-specific connection (sets base URL from config)
├── Contracts/
│   ├── ApiConnectionInterface.php     # get(endpoint, params): array
│   └── FossilOccurrenceServiceInterface.php  # getOccurrences(OccurrenceQuery): OccurrenceCollection
├── Exceptions/
│   └── ApiException.php              # Thrown on non-2xx or connection failure
├── Queries/
│   └── OccurrenceQuery.php           # Readonly DTO — filter params → PBDB query string
└── Services/
    └── FossilOccurrenceService.php    # Calls connection, caches result 1 hour
```

## Request Flow

```
Livewire component
  → builds OccurrenceQuery (readonly DTO)
  → calls FossilOccurrenceServiceInterface::getOccurrences()
    → FossilOccurrenceService checks Cache::remember (1 hour, keyed by md5 of params)
      → on miss: PbdbApiConnection::get('/occs/list', $params)
        → AbstractApiConnection::get() → Http::get() → PBDB
      → maps response['records'] → OccurrenceCollection
```

## OccurrenceQuery

A readonly constructor DTO. All fields are nullable except `show`, `limit`, and `offset`. `toQueryParams()` omits null values so PBDB only receives params that were explicitly set.

```php
$query = new OccurrenceQuery(
    baseName: 'Dinosauria',
    interval: 'Cretaceous',
    limit: 500,
);

$params = $query->toQueryParams();
// ['show' => 'coords,class,loc,time,paleoloc', 'limit' => 500, 'offset' => 0, 'base_name' => 'Dinosauria', 'interval' => 'Cretaceous']
```

The property-to-PBDB-param mapping lives in `toQueryParams()` — update it there if PBDB renames a parameter.

## AbstractApiConnection

- Appends `.json` if the endpoint doesn't already end with it
- Throws `ApiException` on connection failure (`ConnectionException`) or non-2xx response
- Returns `response->json()` as `array<mixed>`

## Caching

Cache keys are `pbdb_occs_{md5(serialize($params))}`. TTL is 3600 seconds. To bust the cache during development:

```bash
./vendor/bin/sail artisan cache:clear
```

Do not lower the TTL in production — PBDB rate-limits aggressively. Identical filter combinations from different users will share a cached response, which is intentional.

## Service Binding

`FossilOccurrenceServiceInterface` is bound to `FossilOccurrenceService` in a service provider. Livewire components and controllers type-hint the interface, not the concrete class. This is what makes mocking in tests possible:

```php
$this->mock(FossilOccurrenceServiceInterface::class)
    ->shouldReceive('getOccurrences')
    ->andReturn($collection);
```

## Adding a New API Source

1. Create `App\Api\YourApiConnection extends AbstractApiConnection` — set `$baseUrl` via a config key
2. Register the base URL in `config/api.php`
3. Add a `YourDataServiceInterface` to `Contracts/`
4. Create `Services/YourDataService` — inject `YourApiConnection`, wrap with `Cache::remember`
5. Bind the interface in a service provider
6. Add a Query DTO if the endpoint has multiple filter params
7. Write unit tests: happy path, cache hit (HTTP called once), `ApiException` propagation

Do not make HTTP calls anywhere outside `AbstractApiConnection::get()`. Do not call `Cache` anywhere outside the service layer.
