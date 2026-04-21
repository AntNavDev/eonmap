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

    <x-seo :title="$title ?? null" :description="$description ?? null" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" href="/favicon.ico" sizes="any"><!-- fallback for older browsers -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg text-text antialiased">
    <nav class="border-b border-border bg-surface" aria-label="Main navigation">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="/" class="text-lg font-semibold tracking-tight text-text">
                    Eonmap
                </a>
                <div class="flex items-center gap-6">
                    <x-nav.nav-link href="{{ route('map') }}" :active="request()->routeIs('map')">
                        Map
                    </x-nav.nav-link>
                    <x-nav.nav-link href="{{ route('browse') }}" :active="request()->routeIs('browse')">
                        Browse
                    </x-nav.nav-link>
                    <x-nav.nav-link href="{{ route('guide') }}" :active="request()->routeIs('guide')">
                        Guide
                    </x-nav.nav-link>
                    <livewire:recently-viewed />
                    <x-theme-toggle />
                </div>
            </div>
        </div>
    </nav>

    @include('partials.error-banner')

    <main>
        @yield('content')
    </main>

    @stack('scripts')
    @livewireScriptConfig
</body>
</html>
