@props(['href' => '/', 'active' => false])

<a
    href="{{ $href }}"
    @if($active) aria-current="page" @endif
    {{ $attributes->merge([
        'class' => 'text-sm font-medium transition-colors ' . ($active
            ? 'text-accent'
            : 'text-muted hover:text-text')
    ]) }}
>
    {{ $slot }}
</a>
