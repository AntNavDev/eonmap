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
- Query object: `app/Api/Queries/OccurrenceQuery` (immutable, `toQueryParams()`)
- DTOs: `app/DTOs/OccurrenceDTO`, `OccurrenceCollection` (compact PBDB vocabulary)
- Service binding in `AppServiceProvider::register()`
- Base layout: `resources/views/layouts/app.blade.php` (FART script, SEO, theme toggle)
- `pint.json` with Laravel preset
- Unit tests: 18 passing across `OccurrenceQueryTest`, `OccurrenceDTOTest`, `FossilOccurrenceServiceTest`

**Why:** PBDB is the data source; all env() calls are isolated to config/ files per the prompt rule.