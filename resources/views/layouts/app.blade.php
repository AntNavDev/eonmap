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
    <nav x-data="{ open: false }" class="relative z-[1100] border-b border-border bg-surface" aria-label="Main navigation">
        <div class="flex h-16 items-center justify-between px-4 sm:px-6">
            <a href="/" class="text-lg font-semibold tracking-tight text-text">
                Eonmap
            </a>

            {{-- Desktop link row --}}
            <div class="hidden sm:flex items-center gap-6">
                <x-nav.nav-link href="{{ route('map') }}" :active="request()->routeIs('map')">
                    Map
                </x-nav.nav-link>
                <x-nav.nav-link href="{{ route('browse') }}" :active="request()->routeIs('browse')">
                    Browse
                </x-nav.nav-link>
                <x-nav.nav-link href="{{ route('taxa.index') }}" :active="request()->routeIs('taxa.*')">
                    Taxa
                </x-nav.nav-link>
                <x-nav.nav-link href="{{ route('guide') }}" :active="request()->routeIs('guide')">
                    Guide
                </x-nav.nav-link>
                <livewire:recently-viewed />
                <x-theme-toggle />
            </div>

            {{-- Mobile: theme toggle + hamburger --}}
            <div class="flex items-center gap-1 sm:hidden">
                <x-theme-toggle />
                <button
                    x-on:click="open = !open"
                    :aria-expanded="open"
                    aria-controls="mobile-menu"
                    aria-label="Toggle navigation menu"
                    class="rounded-md p-2 text-muted hover:bg-surface-hover hover:text-text transition-colors"
                >
                    {{-- Hamburger --}}
                    <svg x-show="!open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    {{-- Close --}}
                    <svg x-show="open" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu panel --}}
        <div
            id="mobile-menu"
            x-show="open"
            x-cloak
            x-on:click.outside="open = false"
            class="sm:hidden border-t border-border bg-surface"
        >
            <x-nav.responsive-nav-link href="{{ route('map') }}" :active="request()->routeIs('map')">
                Map
            </x-nav.responsive-nav-link>
            <x-nav.responsive-nav-link href="{{ route('browse') }}" :active="request()->routeIs('browse')">
                Browse
            </x-nav.responsive-nav-link>
            <x-nav.responsive-nav-link href="{{ route('taxa.index') }}" :active="request()->routeIs('taxa.*')">
                Taxa
            </x-nav.responsive-nav-link>
            <x-nav.responsive-nav-link href="{{ route('guide') }}" :active="request()->routeIs('guide')">
                Guide
            </x-nav.responsive-nav-link>
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
