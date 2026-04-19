@props([
    'show'           => 'false',   // Alpine expression string, e.g. 'deleteOpen'
    'title'          => 'Confirm',
    'message'        => 'Are you sure?',
    'confirmVariant' => 'danger',
    'confirmLabel'   => 'Confirm',
    'cancelLabel'    => 'Cancel',
    'onConfirm'      => '',        // Inline JS to run on confirm click
    'onCancel'       => '',        // Inline JS to run on cancel/backdrop click
])

{{--
    Usage example:

    <div x-data="{ deleteOpen: false }">
        <x-form.button variant="danger" @click="deleteOpen = true">Delete</x-form.button>

        <x-ui.confirm-modal
            :show="'deleteOpen'"
            title="Delete this record?"
            message="This cannot be undone."
            confirm-variant="danger"
            on-confirm="$wire.delete(); deleteOpen = false"
            on-cancel="deleteOpen = false"
        />
    </div>
--}}
<div
    x-show="{{ $show }}"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    {{-- Backdrop --}}
    <div
        class="fixed inset-0 bg-black/50"
        @if($onCancel) x-on:click="{{ $onCancel }}" @endif
    ></div>

    {{-- Dialog card --}}
    <div
        class="relative z-10 w-full max-w-md"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        <x-ui.modal :title="$title" size="sm">

            <p class="text-sm text-[var(--color-text)]">{{ $message }}</p>

            <x-slot:footer>
                @if($onCancel)
                    <x-form.button variant="ghost" x-on:click="{{ $onCancel }}">
                        {{ $cancelLabel }}
                    </x-form.button>
                @endif
                <x-form.button :variant="$confirmVariant" x-on:click="{{ $onConfirm }}">
                    {{ $confirmLabel }}
                </x-form.button>
            </x-slot:footer>

        </x-ui.modal>
    </div>
</div>
