@props([
    'title' => '',
    'size'  => 'md',
])

@php
$sizes = [
    'sm' => 'max-w-md',
    'md' => 'max-w-lg',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl',
];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

{{--
    Modal wrapper component. All modals site-wide must use this as the outer shell.
    This component is the dialog card only — the caller is responsible for the overlay
    backdrop and x-show/positioning wrapper. See x-ui.confirm-modal for a full example.

    Slots:
      $slot   — scrollable body content
      $footer — static footer (action buttons, etc.)
      $close  — optional close button in the header
--}}
<div class="relative flex w-full {{ $sizeClass }} flex-col rounded-xl bg-surface shadow-xl" style="max-height: 90vh">

    {{-- Static header --}}
    <div class="flex shrink-0 items-center justify-between border-b border-border px-6 py-4">
        <h3 class="text-base font-semibold text-text">{{ $title }}</h3>
        @isset($close)
            {{ $close }}
        @endisset
    </div>

    {{-- Scrollable body --}}
    <div class="flex-1 overflow-y-auto px-6 py-5">
        {{ $slot }}
    </div>

    {{-- Static footer --}}
    @isset($footer)
        <div class="flex shrink-0 items-center justify-end gap-3 border-t border-border px-6 py-4">
            {{ $footer }}
        </div>
    @endisset

</div>
