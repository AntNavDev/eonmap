@props([
    'options'  => [25, 50, 100],
    'selected' => 25,
])

{{--
    Pass wire:model.live="yourProperty" as an attribute; it flows through to the <select>.
    pr-8 (2rem) ensures the native dropdown arrow never overlaps the number.
    text-base (1rem = 16px) prevents iOS zoom on focus.
--}}
<div class="flex items-center gap-2">
    <label class="whitespace-nowrap text-xs text-muted">Per page</label>
    <select
        {{ $attributes->merge([
            'class' => 'rounded-md border border-border bg-surface-sunken py-1.5 pl-3 pr-8 text-base text-text focus:outline-none focus:ring-2 focus:ring-accent-muted'
        ]) }}
    >
        @foreach ($options as $option)
            <option value="{{ $option }}" @selected($option === $selected)>{{ $option }}</option>
        @endforeach
    </select>
</div>
