@props([
    'from'       => 0,
    'to'         => 0,
    'total'      => 0,
    'canPrev'    => false,
    'canNext'    => false,
    'prevAction' => 'prevPage',   // Livewire method name
    'nextAction' => 'nextPage',   // Livewire method name
    'showTotal'  => true,         // false when the API cannot provide a grand total
])

{{--
    NOTE: prevAction / nextAction are Livewire method name strings.
    This component is intentionally Livewire-coupled. If non-Livewire (URL-based)
    pagination is needed in future, extend with an 'href' mode.
--}}
<div class="flex items-center gap-3">
    <button
        wire:click="{{ $prevAction }}"
        @disabled(!$canPrev)
        class="rounded-md border border-border bg-surface px-3 py-1.5 text-xs font-medium transition-colors hover:bg-surface-hover disabled:cursor-not-allowed disabled:opacity-40"
    >
        &larr; Prev
    </button>

    <span class="text-xs text-muted">
        @if ($from > 0)
            @if ($showTotal && $total > 0)
                Showing {{ number_format($from) }}&ndash;{{ number_format($to) }} of {{ number_format($total) }}
            @else
                Showing {{ number_format($from) }}&ndash;{{ number_format($to) }}
            @endif
        @else
            No results
        @endif
    </span>

    <button
        wire:click="{{ $nextAction }}"
        @disabled(!$canNext)
        class="rounded-md border border-border bg-surface px-3 py-1.5 text-xs font-medium transition-colors hover:bg-surface-hover disabled:cursor-not-allowed disabled:opacity-40"
    >
        Next &rarr;
    </button>
</div>
