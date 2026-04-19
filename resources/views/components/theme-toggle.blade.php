<button
    x-data
    @click="$store.theme.toggle()"
    :aria-label="$store.theme.current === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
    class="rounded-md p-2 text-muted hover:bg-surface-hover hover:text-text transition-colors"
>
    {{-- Sun icon — shown in dark mode --}}
    <svg x-show="$store.theme.current === 'dark'" xmlns="http://www.w3.org/2000/svg"
         class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
    </svg>

    {{-- Moon icon — shown in light mode --}}
    <svg x-show="$store.theme.current === 'light'" xmlns="http://www.w3.org/2000/svg"
         class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
    </svg>
</button>
