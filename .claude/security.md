# Security

Eonmap is a public read-only application with no authentication. There are no user accounts, no write operations, and no sensitive data stored. The threat model is therefore narrow: prevent injection into outbound API calls, prevent XSS in rendered output, and avoid exposing internal state.

## Blade XSS Hygiene

Always use `{{ }}` for output. Never use `{!! !!}` unless the value has been explicitly sanitised — there are currently no cases where raw HTML output is needed.

```blade
{{-- Correct --}}
{{ $occurrence->acceptedName }}

{{-- Never do this unless you own the HTML --}}
{!! $occurrence->rawHtml !!}
```

Blade's `{{ }}` automatically runs `htmlspecialchars`, so user-visible strings from PBDB (taxon names, formation names, etc.) are safe to render directly.

## PBDB Query Parameter Safety

All PBDB query parameters are built through `OccurrenceQuery` and passed as an array to `Http::get()`. Laravel's HTTP client handles URL encoding — string concatenation into query strings is never done manually.

Validated at the Livewire layer before the query is built:

- `perPage` — must be one of `[25, 50, 100]` (enforced in `updatedPerPage()`)
- `idQual` — constrained to `any|certain|uncertain` via a `<select>` with fixed options (server should also reject unknown values if this filter is added to `OccurrenceQuery` validation)
- Bounding box floats — cast to `float|null` by Livewire's type system; never interpolated as strings

Do not pass raw user strings directly to `OccurrenceQuery` without ensuring Livewire has already cast them to the correct type.

## CSV Export Safety

CSV export URLs are generated server-side from the current filter state. When writing CSV headers or values, ensure fields that begin with `=`, `+`, `-`, or `@` are prefixed with a tab or single-quote to prevent formula injection in spreadsheet applications (CSV injection).

If the export controller writes directly via `fputcsv`, this is handled automatically as long as values are passed as strings and not pre-formatted. Review any custom CSV writer if one is added.

## CSRF

CSRF protection is enforced by Laravel's default middleware stack for all POST/PATCH/DELETE routes. Livewire uses its own CSRF token mechanism for component updates — this requires no additional configuration.

The map and browser pages use Alpine.js for client-side state; no custom AJAX requests bypass CSRF.

## Input Casting and Livewire Properties

Livewire properties are typed — `public int $perPage`, `public float $latMin` — so Livewire will cast incoming values to the declared type. This prevents string injection into numeric fields. Still validate allowlists (like `perPage`) server-side because type casting alone does not constrain the range.

## Recommended HTTP Security Headers

These should be set at the nginx layer (in `docker/nginx/`) for production:

```nginx
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "geolocation=(), microphone=()" always;
```

A `Content-Security-Policy` header would need to allowlist the Leaflet CDN tile sources, PBDB links, and any inline scripts used by Alpine.js. Defer CSP until the full list of external origins is stable.

## No Secrets in Views

PBDB is a public API — no API keys are required. If a key-authenticated data source is added in the future, it must go in `.env` and be accessed via `config()` only. Never reference `env()` directly in application code (only in `config/*.php`).
