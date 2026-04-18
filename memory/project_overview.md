---
name: Project Overview
description: Eonmap app — purpose, stack, prompt-based build sequence, and completed milestones
type: project
---

Eonmap is a fossil occurrence map application that visualises data from the Paleobiology Database (PBDB) API.

**Stack:** Laravel 13, PHP 8.5, MySQL 8.4, Redis, Tailwind CSS 4, Vite, Alpine.js, PHPUnit 12

**Build sequence:** Prompt-based — each prompt builds on the last without revisiting completed work.

## Prompt 0 — Infrastructure, SEO & Theme (done)
- `deploy.sh`, `public/robots.txt`, `public/sitemap.xml`
- `resources/css/app.css` — Deep Ocean theme (light + dark), CSS variables, DM Sans/Mono fonts
- `resources/js/theme.js` + `registerThemeStore` in `app.js` (Alpine store, key: `eonmap-theme`)
- `resources/views/components/seo.blade.php`
- `resources/views/components/theme-toggle.blade.php`
- `app/Console/Commands/GenerateSitemap.php`

## Prompt 1 — Foundation (done)
- Docker: `docker/php/Dockerfile`, `docker/nginx/eonmap.conf` + `rate-limit.conf`, `docker/mysql/my.cnf`, `docker-compose.prod.yml`
- Config: `config/api.php` (PBDB base URL); env vars added for PBDB, Redis, DB
- API layer: `app/Api/` — `AbstractApiConnection`, `PbdbApiConnection`, contracts, `FossilOccurrenceService`
- Query object: `app/Api/Queries/OccurrenceQuery` (immutable, `toQueryParams()` maps to PBDB param names)
- DTOs: `app/DTOs/OccurrenceDTO`, `OccurrenceCollection` (compact PBDB vocabulary: oid, tna, cll, etc.)
- Service binding in `AppServiceProvider::register()`
- Base layout: `resources/views/layouts/app.blade.php` (FART script, SEO, theme toggle, `@yield('content')`)
- `pint.json` with Laravel preset
- Unit tests: 18 passing

## Prompt 2 — Backend Routes, Controllers & Migrations (done)
- Migrations: `saved_searches` (user_id nullable, name, filters json) + `recently_viewed` (session_id, occurrence_no, viewed_at, no timestamps)
- Models: `SavedSearch` (filters cast to array), `RecentlyViewed` ($timestamps = false)
- Routes: `routes/api.php` registered in `bootstrap/app.php`; web routes in `routes/web.php`
- API controllers: `OccurrenceApiController` (index uses service, show uses connection directly with occ_id), `OccurrenceExportController` (5000-row CSV stream)
- Web controllers: `MapController`, `BrowseController`, `OccurrenceController`, `TaxonController` (stub views)
- Stub views: `map/index`, `browse/index`, `occurrences/show`, `taxa/show` — extend layouts.app
- Form request: `OccurrenceIndexRequest` (withValidator enforces at least one filter)
- Feature tests: 10 passing (WebRoutesTest uses withoutVite())

**Key decisions:**
- `show()` in `OccurrenceApiController` calls `PbdbApiConnection::get()` directly (bypassing OccurrenceQuery/service) to avoid modifying app/Api/ — passes `occ_id` + `show=full` as raw params
- Layout uses `@yield('content')` (not $slot) for @extends compatibility with stub views
- `withoutVite()` used in WebRoutesTest to avoid manifest.json failures in test environment
