# Eonmap — Claude Code Notes

## Blade Component Library

All reusable UI is in `resources/views/components/`, organised into four folders:

| Folder | Prefix | What lives here |
|---|---|---|
| `form/` | `<x-form.*>` | Inputs, selects, checkboxes, buttons, labels, errors |
| `nav/` | `<x-nav.*>` | Nav links (desktop and responsive) |
| `pagination/` | `<x-pagination.*>` | Pagination bar, selector, per-page selector |
| `ui/` | `<x-ui.*>` | Modal, confirm-modal, dropdown, dropdown-link |

### Modal convention (REQUIRED)

**All modals site-wide must use `<x-ui.modal>` as the outer dialog card.**
Never build custom modal HTML inline. The component enforces a static header, scrollable body, and static footer.

```blade
{{-- Caller controls x-show, backdrop, and positioning --}}
<div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50" x-on:click="open = false"></div>
    <div class="relative z-10 w-full max-w-lg">
        <x-ui.modal title="My Modal" size="md">
            {{-- Scrollable body --}}
            <p>Content goes here.</p>

            <x-slot:footer>
                <x-form.button variant="ghost" x-on:click="open = false">Cancel</x-form.button>
                <x-form.button variant="primary">Save</x-form.button>
            </x-slot:footer>
        </x-ui.modal>
    </div>
</div>
```

For simple confirm/delete dialogs, use `<x-ui.confirm-modal>` instead — it includes the backdrop and transition:

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

### Button variants

`<x-form.button variant="..." size="...">` — sizes: `sm`, `md` (default), `lg`

| Variant | Use for |
|---|---|
| `primary` | Main call-to-action |
| `secondary` | Secondary actions |
| `danger` | Destructive actions (delete, remove) |
| `success` | Confirmatory actions (save, approve) |
| `ghost` | Inline / low-emphasis (reset, cancel links) |

### iOS zoom prevention

All `<input>`, `<select>`, and `<textarea>` components use `text-base` (1rem = 16px minimum)
to prevent iOS from zooming in on focus. Do not reduce font size below 16px on focusable form elements.

### Pagination

Pagination is Livewire-coupled. Components expect `wire:click` method names as props and
`wire:model.live` passed through attributes. If URL-based pagination is needed in future,
extend `pagination-selector` with an `href` mode.

## Design System

All colours use CSS custom properties from `resources/css/app.css`.
Always use `var(--color-*)` tokens — never hardcoded hex values.
Key tokens: `--color-accent`, `--color-danger`, `--color-success`, `--color-surface`,
`--color-border`, `--color-text`, `--color-muted`.
