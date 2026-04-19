@props(['href' => '/', 'active' => false])

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'text-sm font-medium transition-colors ' . ($active
            ? 'text-accent'
            : 'text-muted hover:text-text')
    ]) }}
>
    {{ $slot }}
</a>
