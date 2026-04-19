@props([
    'from'           => 0,
    'to'             => 0,
    'total'          => 0,
    'canPrev'        => false,
    'canNext'        => false,
    'prevAction'     => 'prevPage',
    'nextAction'     => 'nextPage',
    'perPage'        => 25,
    'perPageOptions' => [25, 50, 100],
    'perPageModel'   => 'perPage',   // Livewire property name to wire:model.live
])

<div class="flex items-center gap-4">
    <x-pagination.pagination-selector
        :from="$from"
        :to="$to"
        :total="$total"
        :can-prev="$canPrev"
        :can-next="$canNext"
        :prev-action="$prevAction"
        :next-action="$nextAction"
    />

    <x-pagination.per-page-selector
        :options="$perPageOptions"
        :selected="$perPage"
        wire:model.live="{{ $perPageModel }}"
    />
</div>
