@props(['value' => null])

{{-- Generic label for groups/sections that don't map to a single input --}}
<span {{ $attributes->merge(['class' => 'block text-sm font-medium text-[var(--color-text)] mb-1']) }}>
    {{ $value ?? $slot }}
</span>
