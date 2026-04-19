@props([
    'label' => null,
    'id'    => null,
    'value' => null,
])

<label class="flex items-center gap-2 cursor-pointer text-sm text-text">
    <input
        type="checkbox"
        @if($id) id="{{ $id }}" @endif
        @if($value !== null) value="{{ $value }}" @endif
        {{ $attributes->merge([
            'class' => 'rounded border-border text-accent focus:ring-2 focus:ring-accent-muted'
        ]) }}
    />
    @if($label)
        <span>{{ $label }}</span>
    @elseif($slot->isNotEmpty())
        {{ $slot }}
    @endif
</label>
