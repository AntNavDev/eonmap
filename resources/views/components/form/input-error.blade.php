@props(['messages' => null])

@if($messages)
    @php
        $message = is_array($messages) ? collect($messages)->first() : $messages;
    @endphp
    @if($message)
        <p {{ $attributes->merge(['class' => 'mt-1 text-xs text-danger']) }}>
            {{ $message }}
        </p>
    @endif
@endif
