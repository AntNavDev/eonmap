@props([
    'align' => 'right',
    'width' => 'md',
])

@php
$alignClass = $align === 'left' ? 'left-0' : 'right-0';
$widths = [
    'sm' => 'w-36',
    'md' => 'w-64',
    'lg' => 'w-80',
    'xl' => 'w-96',
];
$widthClass = $widths[$width] ?? $widths['md'];
@endphp

<div
    class="relative"
    x-data="{ open: false }"
    x-on:click.outside="open = false"
>
    {{-- Trigger: wrap in a div so any element type (button, a, etc.) can toggle open --}}
    <div x-on:click="open = !open">
        {{ $trigger }}
    </div>

    {{-- Dropdown panel --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute {{ $alignClass }} z-50 mt-2 {{ $widthClass }} rounded-lg border border-border bg-surface py-1 shadow-lg"
        role="menu"
    >
        {{ $slot }}
    </div>
</div>
