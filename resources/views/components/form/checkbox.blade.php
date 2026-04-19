@props([
    'label' => null,
    'id'    => null,
    'value' => null,
])

<label class="flex items-center gap-2 cursor-pointer text-sm text-[var(--color-text)]">
    <input
        type="checkbox"
        @if($id) id="{{ $id }}" @endif
        @if($value !== null) value="{{ $value }}" @endif
        {{ $attributes->merge([
            'class' => 'rounded border-[var(--color-border)] text-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent-muted)]'
        ]) }}
    />
    @if($label)
        <span>{{ $label }}</span>
    @elseif($slot->isNotEmpty())
        {{ $slot }}
    @endif
</label>
