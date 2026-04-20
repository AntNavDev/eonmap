# Eonmap

A public fossil occurrence explorer built on the [Paleobiology Database (PBDB)](https://paleobiodb.org) API. Browse hundreds of thousands of fossil records on an interactive map, filter by taxon, geologic period, environment, and location, and explore temporal and geographic distributions for any clade.

## Features

- **Interactive map** — clustered markers, heatmap mode, paleocoordinate toggle, rectangle bounding-box filter, and three basemap options (OSM, Esri Imagery, CartoDB Dark)
- **Browse table** — paginated, sortable table of occurrences with CSV export
- **Taxon pages** — occurrence count by geologic period, temporal range timeline, geographic distribution map, and classification breakdown
- **Occurrence detail** — full taxonomy, location, age bar, and mini-map for individual records
- **Light/dark theme** — system preference detection with manual toggle, no flash on load

## Stack

| Layer | Technology |
|---|---|
| Framework | Laravel / PHP |
| Frontend | Livewire, Alpine.js, Tailwind CSS |
| Maps | Leaflet, leaflet.markercluster, leaflet-draw, leaflet.heat |
| Charts | Chart.js, vis-timeline |
| Data source | Paleobiology Database public API |
| Local dev | Laravel Sail (Docker) |

## Local Development

Requires Docker Desktop.

```bash
# Install PHP dependencies
docker run --rm -v $(pwd):/app composer install

# Copy environment file and generate key
cp .env.example .env
./vendor/bin/sail artisan key:generate

# Start containers
./vendor/bin/sail up -d

# Install JS dependencies and build assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

The app is available at `http://localhost`.

### Useful commands

```bash
./vendor/bin/sail artisan ...   # Run Artisan commands
./vendor/bin/sail composer ...  # Run Composer
./vendor/bin/sail npm ...       # Run npm

./vendor/bin/sail php ./vendor/bin/pint          # Lint PHP
./vendor/bin/sail php artisan test               # Run test suite
```

## Routes

### Web

| Route | Description |
|---|---|
| `GET /map` | Interactive fossil map |
| `GET /browse` | Paginated occurrence browser |
| `GET /occurrences/{id}` | Occurrence detail page |
| `GET /taxa/{name}` | Taxon summary page |

### API

| Route | Description |
|---|---|
| `GET /api/occurrences` | Filtered occurrence list (JSON) |
| `GET /api/occurrences/{id}` | Single occurrence (JSON) |
| `GET /api/export/occurrences` | CSV export of current filter |

## Architecture

Data is fetched from PBDB through an isolated API layer (`app/Api/`) and cached for one hour. Livewire components handle filtering state server-side; Alpine.js handles map and chart rendering client-side.

See `app/Api/CLAUDE.md` for the full API layer documentation.

## Testing

```bash
./vendor/bin/sail php artisan test
```

Tests use an in-memory SQLite database. All PBDB HTTP calls are stubbed — no network access required. See `testing.md` for conventions.

## CI

GitHub Actions runs Pint and the test suite on every push. See `.github/workflows/ci.yml`.