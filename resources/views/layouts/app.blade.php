<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Set theme before first paint to avoid flash --}}
    <script>
        (function () {
            var theme = localStorage.getItem('eonmap-theme')
                || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-seo :title="$title ?? 'Eonmap'" :description="$description ?? null" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[var(--color-bg)] text-[var(--color-text)] antialiased">
    <nav class="border-b border-[var(--color-border)] bg-[var(--color-surface)]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="/" class="text-lg font-semibold tracking-tight text-[var(--color-text)]">
                    Eonmap
                </a>
                <div class="flex items-center gap-6">
                    <a
                        href="{{ route('map') }}"
                        class="text-sm font-medium transition-colors {{ request()->routeIs('map') ? 'text-[var(--color-accent)]' : 'text-[var(--color-muted)] hover:text-[var(--color-text)]' }}"
                    >
                        Map
                    </a>
                    <a
                        href="{{ route('browse') }}"
                        class="text-sm font-medium transition-colors {{ request()->routeIs('browse') ? 'text-[var(--color-accent)]' : 'text-[var(--color-muted)] hover:text-[var(--color-text)]' }}"
                    >
                        Browse
                    </a>
                    <x-theme-toggle />
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
    @livewireScriptConfig
</body>
</html>