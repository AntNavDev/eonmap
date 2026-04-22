# Eonmap — CLAUDE.md

## Tech Stack

| Layer | Technology |
|---|---|
| Runtime | PHP 8.5 (Laravel Sail / Ubuntu 24.04 container) |
| Framework | Laravel 13 |
| UI | Livewire 3 + Alpine.js |
| CSS | Tailwind CSS v4 (`@tailwindcss/vite`, no PostCSS) |
| Build | Vite |
| Maps | Leaflet.js |
| Tables | Tabulator |
| Charts | Chart.js |
| Database | MySQL 8.4 |
| Cache | Redis (for PBDB API response caching) |
| Testing | PHPUnit 12 |
| Linting | Laravel Pint (PSR-12, `pint.json` at project root) |

## Code Style — Pint

All PHP code must conform to PSR-12. **Run Pint immediately after writing or editing any PHP file:**

```bash
./vendor/bin/sail php ./vendor/bin/pint
```

Do not wait until commit time — run it after each file so formatting issues are caught before moving on.

Key enforced rules: `declare_strict_types`, `no_unused_imports`, `ordered_imports`, single quotes, trailing commas in multi-line arrays.

## Folder Structure

| Path | Purpose |
|---|---|
| `app/Api/` | HTTP client layer — see `app/Api/CLAUDE.md` |
| `app/Api/Contracts/` | Interfaces: `ApiConnectionInterface`, `FossilOccurrenceServiceInterface` |
| `app/Api/Queries/` | Typed query builder classes (one per API operation) |
| `app/Api/Services/` | Service classes that call the API, parse responses, and cache results |
| `app/DTOs/` | Readonly DTO / value-object classes returned by services |
| `app/Livewire/` | All Livewire component classes |
| `app/Http/Controllers/` | Thin controllers for non-Livewire routes (API, export) |
| `resources/views/components/form/` | Form components (`<x-form.*>`) |
| `resources/views/components/nav/` | Nav link components (`<x-nav.*>`) |
| `resources/views/components/pagination/` | Pagination components (`<x-pagination.*>`) |
| `resources/views/components/ui/` | UI components: modal, dropdown, etc. (`<x-ui.*>`) |
| `resources/views/livewire/` | Blade views for Livewire components |
| `resources/js/app.js` | Alpine.js component registrations and JS entry point |
| `resources/css/app.css` | Tailwind entry point — all theme tokens and CSS variables |
| `config/api.php` | Base URLs for external APIs — reads from `.env` |

## Documentation Update Rule

Whenever architecture changes — new layers, new patterns, new config files, new directory purposes — update the relevant `CLAUDE.md` immediately. If no `CLAUDE.md` exists for that directory, create one. This root table must always reflect the current project state.

---

## Theme System

All colours use CSS custom properties. **Always use Tailwind utility aliases** — never `[var(--color-*)]` arbitrary values in templates. The `@theme inline` block in `app.css` exposes all tokens as Tailwind utilities.

### Available utility aliases

| Utility | Maps to |
|---|---|
| `bg-bg` / `text-bg` | `--color-bg` |
| `bg-surface` | `--color-surface` |
| `bg-surface-raised` | `--color-surface-raised` |
| `bg-surface-sunken` | `--color-surface-sunken` |
| `bg-surface-hover` | `--color-surface-hover` |
| `bg-surface-active` | `--color-surface-active` |
| `border-border` | `--color-border` |
| `text-text` | `--color-text` |
| `text-muted` | `--color-text-muted` |
| `bg-accent` / `text-accent` / `border-accent` | `--color-accent` |
| `bg-accent-hover` | `--color-accent-hover` |
| `bg-accent-subtle` | `--color-accent-subtle` |
| `border-accent-muted` / `ring-accent-muted` | `--color-accent-muted` |
| `bg-secondary` / `text-secondary` | `--color-accent-secondary` |
| `bg-success` / `text-success` | `--color-success` |
| `bg-success-hover` | `--color-success-hover` |
| `bg-danger` / `text-danger` / `border-danger` | `--color-danger` |
| `bg-danger-hover` | `--color-danger-hover` |
| `bg-warning` / `text-warning` | `--color-warning` |
| `bg-info` / `text-info` | `--color-info` |

Opacity modifiers work normally: `bg-danger/10`, `border-danger/30`, `bg-surface/90`.

### Adding a new colour token

1. Add to `:root` in `app.css` (light value)
2. Add to `[data-theme="dark"]` in `app.css` (dark value)
3. Add to `@theme inline` in `app.css` (enables the Tailwind utility)
4. Document it in this table

### Swapping the palette

Only the hex values in `:root` and `[data-theme="dark"]` need to change. Variable names are stable — no Blade or component files need updating.

---

## Blade Component Library

All reusable UI in `resources/views/components/`, referenced with dot notation.

### Button variants

`<x-form.button variant="..." size="...">` — sizes: `sm`, `md` (default), `lg`

| Variant | Use for |
|---|---|
| `primary` | Main call-to-action |
| `secondary` | Secondary actions |
| `danger` | Destructive actions (delete, remove) |
| `success` | Confirmatory actions (save, approve) |
| `ghost` | Inline / low-emphasis (reset, cancel links) |

### Modal convention (REQUIRED)

**All modals site-wide must use `<x-ui.modal>` as the outer dialog card.** Never build custom modal HTML inline. The component enforces a static header, scrollable body, and static footer.

```blade
<div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50" x-on:click="open = false"></div>
    <div class="relative z-10 w-full max-w-lg">
        <x-ui.modal title="My Modal" size="md">
            <p>Content goes here.</p>
            <x-slot:footer>
                <x-form.button variant="ghost" x-on:click="open = false">Cancel</x-form.button>
                <x-form.button variant="primary">Save</x-form.button>
            </x-slot:footer>
        </x-ui.modal>
    </div>
</div>
```

For confirm/delete dialogs use `<x-ui.confirm-modal>` — it includes backdrop, transitions, and variant-aware confirm button:

```blade
<div x-data="{ deleteOpen: false }">
    <x-form.button variant="danger" x-on:click="deleteOpen = true">Delete</x-form.button>

    <x-ui.confirm-modal
        :show="'deleteOpen'"
        title="Delete this record?"
        message="This cannot be undone."
        confirm-variant="danger"
        on-confirm="$wire.delete(); deleteOpen = false"
        on-cancel="deleteOpen = false"
    />
</div>
```

Available modal sizes: `sm`, `md` (default), `lg`, `xl`.

### Form element rule (REQUIRED)

**Never write raw `<input>`, `<select>`, or `<textarea>` HTML in templates.** Always use the Blade form components so styling and behaviour are controlled from one place:

| Component | Use for |
|---|---|
| `<x-form.input>` | Standard single-line text input |
| `<x-form.search-input>` | Search field with built-in magnifying glass icon. Accepts `$right` slot (spinner, clear button) and `$dropdown` slot (autocomplete list) |
| `<x-form.select>` | Dropdown select |
| `<x-form.checkbox>` | Checkbox |
| `<x-form.label>` | Form label |
| `<x-form.input-label>` | Input + label pair |
| `<x-form.input-error>` | Validation error message |

All attributes (`x-model`, `wire:model`, `x-on:*`, `aria-*`, etc.) are passed through via `$attributes->merge()`.

`<x-form.search-input>` slot usage:
```blade
<x-form.search-input placeholder="Search…" x-model="query" x-on:input="onInput">
    <x-slot:right>
        {{-- Spinner, clear button, or any right-aligned control --}}
    </x-slot:right>
    <x-slot:dropdown>
        {{-- Autocomplete dropdown — rendered inside the component's relative wrapper --}}
    </x-slot:dropdown>
</x-form.search-input>
```

### iOS zoom prevention

All `<input>`, `<select>`, and `<textarea>` components use `text-base` (1rem = 16px minimum) to prevent iOS zoom on focus. Do not reduce font size below 16px on focusable form elements.

### Pagination

Pagination components are Livewire-coupled. `pagination-selector` accepts Livewire method names as `prev-action` / `next-action` string props. `per-page-selector` receives `wire:model.live` via `$attributes`. Allowed per-page values: 25, 50, 100 — enforced server-side in `updatedPerPage()`.

---

## Livewire + Alpine.js Integration Rules

**Do not assume Alpine state persists across Livewire re-renders.** Every time a Livewire component re-renders, its DOM is morphed. Alpine components inside the morphed region are destroyed and re-initialized unless protected.

### Rule 1 — Use `wire:ignore` for any Alpine-managed or third-party DOM

Any element that owns client-side state (Leaflet map, Tabulator table, Chart.js canvas, TomSelect input) **must** have `wire:ignore` on its root. Without it, Livewire's morphdom pass will replace the element, orphaning the JS object and corrupting the UI.

```blade
<div x-data="fossilMap" wire:ignore class="relative">
    <div id="eonmap-map" class="w-full h-full"></div>
    ...
</div>
```

### Rule 2 — Use `$wire.*` instead of Blade conditionals inside `wire:ignore`'d elements

Blade conditionals (`@if`, `@unless`) are server-rendered. Inside a `wire:ignore` subtree they will never update after the first render. Use Alpine's `$wire` magic property instead — it reads Livewire component properties reactively without any DOM morphing.

```blade
{{-- WRONG: Blade conditional inside wire:ignore — never updates after mount --}}
@if ($hasFilters)
    <div class="result-count">...</div>
@endif

{{-- CORRECT: $wire reads live Livewire state, no re-render needed --}}
<div x-show="$wire.filtersApplied">...</div>
<span x-text="$wire.resultCount.toLocaleString()"></span>
```

### Rule 3 — Alpine components must be imported in `app.js`, not pushed via `@push('scripts')`

`alpine:init` fires during `Livewire.start()` inside `app.js`. Any component registered in a page-level `@push('scripts')` block loads after `alpine:init` has already fired — Alpine will not retry failed `x-data` lookups, leaving the component silently broken.

```js
// app.js — correct order
import './browse.js';   // registers Alpine components BEFORE Livewire.start()
Livewire.start();
```

### Rule 4 — Dispatch plain arrays from Livewire, never PHP objects

Livewire's `$this->dispatch()` serialises its arguments to JSON. PHP DTOs and objects do not serialise reliably — properties are lost or mangled. Always `array_map` to plain arrays before dispatching.

```php
// WRONG — DTO properties may not survive serialisation
$this->dispatch('occurrences-loaded', occurrences: $collection->items);

// CORRECT — plain array with only the keys the JS side needs
$occurrences = array_map(static fn ($dto) => [
    'lat' => $dto->lat,
    'lng' => $dto->lng,
    // ...
], $collection->items);
$this->dispatch('occurrences-loaded', occurrences: $occurrences);
```
