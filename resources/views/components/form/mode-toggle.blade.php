@props([
    'isCustom' => false,
    'defaultLabel' => 'Select',
    'customLabel' => 'Custom',
    'onDefault' => '',
    'onCustom' => '',
])

<div class="mt-1.5 flex overflow-hidden rounded-md border border-border text-xs">
    <button
        type="button"
        wire:click="{{ $onDefault }}"
        @class([
            'flex-1 px-2.5 py-1.5 font-medium transition-colors',
            'bg-accent text-on-accent' => ! $isCustom,
            'bg-surface text-muted hover:bg-surface-hover' => $isCustom,
        ])
    >{{ $defaultLabel }}</button>
    <button
        type="button"
        wire:click="{{ $onCustom }}"
        @class([
            'flex-1 border-l border-border px-2.5 py-1.5 font-medium transition-colors',
            'bg-accent text-on-accent' => $isCustom,
            'bg-surface text-muted hover:bg-surface-hover' => ! $isCustom,
        ])
    >{{ $customLabel }}</button>
</div>
