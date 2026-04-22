@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- ─── Page header ────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-text">Taxa</h1>
        <p class="mt-2 text-muted">
            Explore notable groups in the fossil record. Each taxon links to its occurrence data and distribution.
        </p>
    </div>

    {{-- ─── Taxon search ───────────────────────────────────────────────── --}}
    <div x-data="taxaSearch" class="mb-10 w-full">

        {{-- Input row --}}
        <x-form.search-input
            x-model="query"
            x-on:input="onInput"
            x-on:keydown.escape="clear"
            placeholder="e.g. Tyrannosaurus, Belemnoidea, Scleractinia …"
            autocomplete="off"
            spellcheck="false"
            aria-label="Search taxa by name"
            aria-autocomplete="list"
            x-bind:aria-expanded="dropdownOpen"
            class="rounded-xl border-border bg-surface shadow-sm transition-colors focus:border-accent focus:ring-accent-muted"
        >
            {{-- Spinner / clear button --}}
            <x-slot:right>
                <template x-if="loading">
                    <svg class="h-4 w-4 animate-spin text-muted" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                </template>
                <template x-if="!loading && query.length > 0">
                    <button
                        type="button"
                        x-on:click="clear"
                        class="rounded p-0.5 text-muted transition-colors hover:text-text"
                        aria-label="Clear search"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </template>
            </x-slot:right>

            {{-- Dropdown suggestions --}}
            <x-slot:dropdown>
                <div
                    x-show="dropdownOpen"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-on:click.outside="dropdownOpen = false"
                    class="absolute z-20 mt-1 w-full rounded-xl border border-border bg-surface shadow-lg"
                    role="listbox"
                >
                    <ul class="divide-y divide-border py-1">
                        <template x-for="taxon in results" :key="taxon.name">
                            <li
                                role="option"
                                x-on:click="select(taxon)"
                                class="flex items-center justify-between px-4 py-2.5 transition-colors hover:bg-surface-hover"
                            >
                                <span class="italic text-text" x-text="taxon.name"></span>
                                <span
                                    x-show="taxon.rank"
                                    x-text="taxon.rank"
                                    class="ml-3 shrink-0 rounded-full border border-accent-muted bg-accent-subtle px-2 py-0.5 text-xs text-accent"
                                ></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </x-slot:dropdown>
        </x-form.search-input>

        {{-- Preview card (shown after selecting a result) --}}
        <div
            x-show="selected !== null"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="mt-4 w-full"
        >
            <div class="rounded-xl border border-border bg-surface p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">

                    {{-- Left: name + metadata --}}
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <span
                                x-show="selected && selected.rank"
                                x-text="selected && selected.rank"
                                class="inline-flex items-center rounded-full border border-accent-muted bg-accent-subtle px-2.5 py-0.5 text-xs font-medium text-accent"
                            ></span>
                        </div>

                        <h3
                            class="text-2xl font-semibold italic text-text"
                            x-text="selected && selected.name"
                        ></h3>

                        <p
                            x-show="selected && selected.parent_name"
                            class="mt-1 text-sm text-muted"
                        >
                            Within <span class="italic" x-text="selected && selected.parent_name"></span>
                        </p>
                    </div>

                    {{-- Right: CTA --}}
                    <a
                        :href="selected ? taxonUrl(selected.name) : '#'"
                        class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2 text-sm font-medium text-on-accent transition-colors hover:bg-accent-hover"
                    >
                        More details
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <p class="mt-4 text-sm text-muted">
                    View the full occurrence record: geographic distribution, geologic timeline, and classification breakdown.
                </p>
            </div>
        </div>

        {{-- Empty state: query typed but no results --}}
        <div
            x-show="!loading && !dropdownOpen && query.length >= 2 && results.length === 0 && selected === null"
            class="mt-4 rounded-xl border border-border bg-surface px-6 py-8 text-center"
        >
            <p class="text-sm text-muted">
                No taxa found matching <span class="italic text-text" x-text='`"${query}"`'></span>.
                The taxa table may not be seeded yet — run <code class="rounded bg-surface-raised px-1.5 py-0.5 font-mono text-xs">artisan taxa:seed</code> to populate it.
            </p>
        </div>

    </div>

    {{-- ─── Curated card grid ──────────────────────────────────────────── --}}
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-text">Featured taxa</h2>
        <p class="mt-1 text-sm text-muted">A curated selection of notable groups across the fossil record.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($taxa as $taxon)
            <a
                href="{{ route('taxa.show', $taxon['name']) }}"
                class="group flex flex-col rounded-xl border border-border bg-surface p-6 transition-colors hover:border-accent hover:bg-surface-hover"
            >
                {{-- Rank badge --}}
                <div class="mb-3 flex items-center justify-between">
                    <span class="inline-flex items-center rounded-full border border-accent-muted bg-accent-subtle px-2.5 py-0.5 text-xs font-medium text-accent">
                        {{ $taxon['rank'] }}
                    </span>
                </div>

                {{-- Taxon name --}}
                <h2 class="text-xl font-semibold italic text-text transition-colors group-hover:text-accent">
                    {{ $taxon['name'] }}
                </h2>

                {{-- Era range --}}
                <div class="mt-1 flex items-center gap-1.5 text-xs text-muted">
                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                    </svg>
                    <span>{{ $taxon['era'] }}</span>
                </div>

                {{-- Description --}}
                <p class="mt-3 grow text-sm leading-relaxed text-muted">
                    {{ $taxon['description'] }}
                </p>

                {{-- Explore link --}}
                <div class="mt-4 flex items-center gap-1 text-sm font-medium text-accent">
                    Explore occurrences
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

</div>
@endsection
