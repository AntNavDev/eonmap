# PBDB API — Reference & Guide

---

## The Simple Version (ELI5)

Imagine there's a giant library on the internet that knows about every fossil ever found — where it was dug up, what creature it came from, how old it is, and what the world looked like back then. That library is the **Paleobiology Database (PBDB)**. It's free and public.

Eonmap asks that library questions like: *"Show me all Dinosauria fossils found in North America during the Cretaceous period."* The library sends back a list of records. Eonmap takes that list, turns it into dots on a map, and lets you explore it visually.

Because the library is on the internet and can be slow, Eonmap saves each answer for one hour so it doesn't have to ask the same question twice. That saved answer is called a **cache**.

The code that handles all this talking-to-the-library lives in `app/Api/`.

---

## What the API Does (and Why We Want It)

The PBDB exposes fossil occurrence data — each "occurrence" is a record of a specific organism found at a specific place. Eonmap uses a single endpoint:

### `GET /occs/list`

This returns a paginated list of fossil occurrence records matching a set of filters.

**Why we want each piece of data:**

| Data | PBDB field | Why we need it |
|---|---|---|
| Occurrence ID | `oid` | Unique identifier — used to link to the occurrence detail page |
| Accepted taxon name | `tna` | The authoritative name displayed on map popups and detail pages |
| Taxonomic rank | `rnk` | Lets us show "Genus", "Species", etc. in the UI |
| Phylum / Class / Order / Family / Genus | `phl`, `cll`, `odl`, `fml`, `gnl` | Full classification hierarchy for filtering and display |
| Latitude / Longitude | `lat`, `lng` | Modern coordinates — required to place a dot on the map |
| Paleolatitude / Paleolongitude | `pla`, `plo` | Reconstructed coordinates of where the fossil was *at the time the organism lived* — used in paleo mode to show continental drift |
| Early / Late interval | `oei`, `oli` | Geological time period names (e.g. "Maastrichtian", "Campanian") |
| Max / Min age (Ma) | `eag`, `lag` | Numerical age in millions of years — used for the age range display and timeline filtering |
| Country | `cc2` | ISO country code — shown in popups, used for geographic filtering |
| State/Province | `stp` | Sub-national location detail |
| Formation | `sfm` | The rock formation the fossil was found in — geological context |
| Environment | `env` | Depositional environment (marine, terrestrial, etc.) — used for environment filtering |
| Collection number | `cid` | Links to the PBDB collection record the occurrence belongs to |

**The `show` parameter** controls which of these data blocks PBDB includes in the response. Eonmap always requests: `coords,class,loc,time,paleoloc` — covering all the fields above.

---

## Detailed Technical Reference

### Architecture Overview

```
Livewire Component / Controller
         │
         │  injects interface
         ▼
FossilOccurrenceServiceInterface   ← the only public contract
         │
         │  implemented by
         ▼
FossilOccurrenceService            ← checks cache, delegates to connection
         │
         │  injects
         ▼
PbdbApiConnection                  ← extends AbstractApiConnection
         │
         │  calls
         ▼
AbstractApiConnection::get()       ← wraps Laravel Http facade
         │
         ▼
  PBDB API (paleobiodb.org)
```

The goal of this layering is **isolation**: a Livewire component never makes an HTTP call directly. It speaks to an interface. The interface can be swapped for a mock in tests without touching any HTTP code.

---

### `ApiConnectionInterface` — `app/Api/Contracts/ApiConnectionInterface.php`

The base contract for any external HTTP connection in the app.

```php
interface ApiConnectionInterface
{
    public function get(string $endpoint, array $params = []): array;
}
```

**`get(string $endpoint, array $params): array`**
Takes an endpoint path and a key/value array of query parameters. Returns the decoded JSON response as a PHP array. Throws `ApiException` on failure.

This interface exists so that if you ever needed to swap the HTTP client (or mock the entire connection layer in a test), you do it by binding a different implementation — nothing upstream changes.

---

### `AbstractApiConnection` — `app/Api/AbstractApiConnection.php`

The base class for all concrete API connections. Implements `ApiConnectionInterface`.

**Constructor:** takes `$baseUrl` as a string — subclasses pass their specific base URL up via `parent::__construct()`.

**`get()` behaviour:**

1. If the endpoint doesn't already end with `.json`, it appends it — PBDB requires `.json` on all endpoints.
2. Issues `Http::get($baseUrl . $endpoint, $params)` via Laravel's HTTP facade.
3. If Laravel throws `ConnectionException` (network failure, DNS error, timeout), it re-throws as `ApiException` with the original message.
4. If the response status is not 2xx, it throws `ApiException` with the HTTP status code and response body.
5. Returns `$response->json()` — a decoded PHP array.

**The `.json` auto-append** means callers use clean paths like `/occs/list` rather than `/occs/list.json`. The class handles the detail.

---

### `PbdbApiConnection` — `app/Api/PbdbApiConnection.php`

The PBDB-specific concrete connection. Extends `AbstractApiConnection`.

```php
class PbdbApiConnection extends AbstractApiConnection
{
    public function __construct()
    {
        parent::__construct(config('api.pbdb.base_url'));
    }
}
```

It reads the base URL from `config/api.php`:

```php
// config/api.php
'pbdb' => [
    'base_url' => env('PBDB_API_URL', 'https://paleobiodb.org/data1.2'),
],
```

The default is `https://paleobiodb.org/data1.2`. You can override it in `.env` with `PBDB_API_URL` — useful if you ever point to a staging or local mirror of PBDB.

`PbdbApiConnection` has no methods of its own. Its only job is to know the base URL.

---

### `ApiException` — `app/Api/Exceptions/ApiException.php`

```php
class ApiException extends RuntimeException {}
```

A thin wrapper around `RuntimeException`. It carries the error message and HTTP status code (as the exception code). Two scenarios produce it:

- **Connection failure** — `$code` is `0`, `$previous` is the original `ConnectionException`.
- **Non-2xx response** — `$code` is the HTTP status (e.g. `429`, `500`), message includes the response body.

Callers that care about the distinction can check `$e->getCode()`. Most callers just let it bubble up to the global error handler.

---

### `OccurrenceQuery` — `app/Api/Queries/OccurrenceQuery.php`

A **readonly constructor DTO** that represents all the filter parameters you can send to the PBDB `/occs/list` endpoint. Every property is nullable except `show`, `limit`, and `offset` which always have defaults.

**All properties:**

| Property | Type | PBDB param | Default | Description |
|---|---|---|---|---|
| `$occId` | `?int` | `occ_id` | `null` | Fetch a single specific occurrence by PBDB ID |
| `$baseName` | `?string` | `base_name` | `null` | Clade or taxon root — returns all descendants (e.g. `Dinosauria`) |
| `$taxonName` | `?string` | `taxon_name` | `null` | Exact taxon match — no descendants |
| `$baseId` | `?int` | `base_id` | `null` | PBDB numeric taxon ID — alternative to `baseName` |
| `$lngMin` | `?float` | `lngmin` | `null` | West bounding box coordinate |
| `$lngMax` | `?float` | `lngmax` | `null` | East bounding box coordinate |
| `$latMin` | `?float` | `latmin` | `null` | South bounding box coordinate |
| `$latMax` | `?float` | `latmax` | `null` | North bounding box coordinate |
| `$countryCodes` | `?string` | `cc` | `null` | Comma-separated ISO 3166-1 alpha-2 codes (e.g. `US,CA,MX`) |
| `$continent` | `?string` | `continent` | `null` | Continent name (e.g. `North America`) |
| `$interval` | `?string` | `interval` | `null` | Geological interval name (e.g. `Cretaceous`, `Maastrichtian`) |
| `$minMa` | `?float` | `min_ma` | `null` | Minimum age in millions of years |
| `$maxMa` | `?float` | `max_ma` | `null` | Maximum age in millions of years |
| `$envType` | `?string` | `envtype` | `null` | Depositional environment type (e.g. `marine`, `terrestrial`) |
| `$lithology` | `?string` | `lithology` | `null` | Lithology description |
| `$idQual` | `?string` | `idqual` | `null` | Identification quality: `any`, `certain`, or `uncertain` |
| `$show` | `string` | `show` | `'coords,class,loc,time,paleoloc'` | Response data blocks to include |
| `$limit` | `int` | `limit` | `500` | Max records per page |
| `$offset` | `int` | `offset` | `0` | Record offset for pagination |

**`toQueryParams(): array`**

Converts the DTO to a flat array suitable for passing to `Http::get()`. Null properties are omitted — PBDB treats a missing parameter as "no filter", which is the correct behaviour. The three non-nullable fields (`show`, `limit`, `offset`) are always included.

The internal property-to-PBDB-key mapping lives in a `$map` array inside this method. If PBDB renames a parameter, this is the only place to update it.

---

### `FossilOccurrenceServiceInterface` — `app/Api/Contracts/FossilOccurrenceServiceInterface.php`

```php
interface FossilOccurrenceServiceInterface
{
    public function getOccurrences(OccurrenceQuery $query): OccurrenceCollection;
}
```

The **only public contract** that Livewire components and controllers depend on. They type-hint this interface, not the concrete `FossilOccurrenceService`. This is what makes the service testable without hitting the network:

```php
// In a test
$this->mock(FossilOccurrenceServiceInterface::class)
    ->shouldReceive('getOccurrences')
    ->andReturn($fakeCollection);
```

---

### `FossilOccurrenceService` — `app/Api/Services/FossilOccurrenceService.php`

The concrete implementation of `FossilOccurrenceServiceInterface`. Injected with `PbdbApiConnection`.

**`getOccurrences(OccurrenceQuery $query): OccurrenceCollection`**

1. Calls `$query->toQueryParams()` to get the flat parameter array.
2. Computes a cache key: `pbdb_occs_{md5(serialize($params))}` — identical queries from different users share the same cache entry.
3. Wraps everything in `Cache::remember($key, 3600, $callback)` — if a cached entry exists, it's returned immediately without hitting PBDB. If not, the callback fires.
4. Inside the callback: calls `$this->connection->get('/occs/list', $params)`.
5. Pulls `$response['records']` (an empty array if the key is missing).
6. Returns `OccurrenceCollection::fromArray($records)`.

**TTL is 3600 seconds (1 hour).** Do not lower this in production — PBDB rate-limits aggressively. If you need to bust the cache in development:

```bash
./vendor/bin/sail artisan cache:clear
```

---

### `OccurrenceDTO` — `app/DTOs/OccurrenceDTO.php`

A **readonly value object** representing a single fossil occurrence record. Created by `OccurrenceDTO::fromArray()` which maps PBDB's terse field codes (e.g. `oid`, `tna`, `phl`) to readable property names.

All fields except `occurrenceNo` and `collectionNo` are nullable — PBDB does not guarantee every field is present in every record. `acceptedName` and `acceptedRank` are both nullable; when a taxon has not been entered into PBDB taxonomy (`tdf: 'taxon not entered'`), both `tna` and `rnk` are omitted. This is common in deep-time queries (e.g. Cambrian Seas and Great Dying presets).

The `paleolat` / `paleolng` properties are only populated when `paleoloc` is in the `show` parameter.

---

### `OccurrenceCollection` — `app/DTOs/OccurrenceCollection.php`

A thin wrapper around an array of `OccurrenceDTO` objects.

```php
public readonly array $items;   // OccurrenceDTO[]
public readonly int $total;     // total matching records (from PBDB metadata)
public readonly int $offset;    // current offset
```

Created via `OccurrenceCollection::fromArray($records)` which calls `OccurrenceDTO::fromArray()` on each record via `array_map`.

---

### Service Binding — `app/Providers/AppServiceProvider.php`

```php
$this->app->bind(
    FossilOccurrenceServiceInterface::class,
    FossilOccurrenceService::class,
);
```

Laravel's container resolves `FossilOccurrenceServiceInterface` to `FossilOccurrenceService` — and auto-resolves `FossilOccurrenceService`'s constructor dependency on `PbdbApiConnection`. No factories or manual wiring needed.

---

## Using the PBDB API with Postman

PBDB is a public API — no API key, no authentication, no account needed.

### Base URL

```
https://paleobiodb.org/data1.2
```

### The One Endpoint Eonmap Uses

```
GET https://paleobiodb.org/data1.2/occs/list.json
```

---

### Setting Up in Postman

1. Open Postman and create a new **Collection** called `PBDB`.
2. Add a new **GET** request.
3. Set the URL to: `https://paleobiodb.org/data1.2/occs/list.json`
4. Go to the **Params** tab — add query parameters there (don't put them in the URL manually).

---

### The `show` Parameter (Required)

The `show` parameter tells PBDB which data blocks to include. Without it you get almost nothing useful. Eonmap always sends:

```
show = coords,class,loc,time,paleoloc
```

| Block | What it adds to the response |
|---|---|
| `coords` | `lat`, `lng` — modern decimal coordinates |
| `class` | `phl`, `cll`, `odl`, `fml`, `gnl` — full taxonomy |
| `loc` | `cc2`, `stp`, `sfm`, `env` — location and formation data |
| `time` | `oei`, `oli`, `eag`, `lag` — geological interval and age |
| `paleoloc` | `pla`, `plo` — reconstructed paleocoordinates |

**Always include `show` or your results will be missing most fields.**

---

### Example Requests

**All Dinosauria occurrences (first 50)**

| Param | Value |
|---|---|
| `show` | `coords,class,loc,time,paleoloc` |
| `base_name` | `Dinosauria` |
| `limit` | `50` |
| `offset` | `0` |

---

**T. rex specifically (exact match)**

| Param | Value |
|---|---|
| `show` | `coords,class,loc,time,paleoloc` |
| `taxon_name` | `Tyrannosaurus rex` |
| `limit` | `100` |

---

**All Cretaceous fossils in the USA**

| Param | Value |
|---|---|
| `show` | `coords,class,loc,time,paleoloc` |
| `interval` | `Cretaceous` |
| `cc` | `US` |
| `limit` | `500` |

---

**Fossils by age range (65–100 Ma) in a bounding box**

| Param | Value |
|---|---|
| `show` | `coords,class,loc,time,paleoloc` |
| `min_ma` | `65` |
| `max_ma` | `100` |
| `lngmin` | `-120` |
| `lngmax` | `-70` |
| `latmin` | `25` |
| `latmax` | `50` |
| `limit` | `500` |

---

**Single occurrence by ID**

| Param | Value |
|---|---|
| `show` | `coords,class,loc,time,paleoloc` |
| `occ_id` | `100` |

---

**Marine fossils in a specific formation**

| Param | Value |
|---|---|
| `show` | `coords,class,loc,time,paleoloc` |
| `envtype` | `marine` |
| `interval` | `Jurassic` |
| `limit` | `500` |

---

### Understanding the Response

A successful response looks like this:

```json
{
  "records": [
    {
      "oid": "occ:1234",
      "tna": "Tyrannosaurus rex",
      "rnk": "species",
      "phl": "Chordata",
      "cll": "Reptilia",
      "odl": "Saurischia",
      "fml": "Tyrannosauridae",
      "gnl": "Tyrannosaurus",
      "lat": "46.8",
      "lng": "-104.0",
      "oei": "Maastrichtian",
      "oli": "Maastrichtian",
      "eag": "66.0",
      "lag": "66.0",
      "cc2": "US",
      "stp": "Montana",
      "sfm": "Hell Creek",
      "env": "terrestrial indet.",
      "cid": "col:5678",
      "pla": "50.2",
      "plo": "-68.1"
    }
  ]
}
```

**Note:** `oid` comes back as `"occ:1234"` — the `OccurrenceDTO::fromArray()` casts it with `(int) $data['oid']` which strips the `occ:` prefix and gives you the integer. Keep this in mind if you're comparing IDs between Postman responses and what's stored in the DTO.

---

### Pagination

PBDB does not return a total count in the `records` response by default. Use `limit` and `offset` to page through results:

| Page | `limit` | `offset` |
|---|---|---|
| 1 | `500` | `0` |
| 2 | `500` | `500` |
| 3 | `500` | `1000` |

When `records` returns fewer items than your `limit`, you've hit the last page.

---

### Common Error Responses

| Status | Meaning |
|---|---|
| `400` | Bad parameter name or value — check your param spellings |
| `404` | Endpoint path is wrong |
| `429` | Rate limited — wait before retrying; use caching in production |
| `500` | PBDB server error — transient, retry after a delay |

Eonmap converts all non-2xx responses into an `ApiException` with the status code and body.