@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- ─── Back link ─────────────────────────────────────────────────── --}}
    <a
        href="/map"
        class="inline-flex items-center gap-1 text-sm text-[var(--color-muted)] hover:text-[var(--color-text)] transition-colors mb-6"
    >
        &larr; Back to map
    </a>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_24rem]">

        {{-- ─── Left column — detail cards ──────────────────────────── --}}
        <div class="space-y-6">

            {{-- Taxonomy card --}}
            <div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <h1 class="text-2xl font-bold italic text-[var(--color-text)] mb-1">
                    {{ $occurrence->acceptedName }}
                </h1>

                <span class="inline-block rounded-full bg-[var(--color-accent-subtle)] border border-[var(--color-accent-muted)] px-2.5 py-0.5 text-xs font-medium text-[var(--color-accent)] mb-4">
                    {{ ucfirst($occurrence->acceptedRank) }}
                </span>

                @php
                    $breadcrumb = array_filter([
                        $occurrence->phylum,
                        $occurrence->class,
                        $occurrence->order,
                        $occurrence->family,
                        $occurrence->genus,
                    ]);
                @endphp

                @if (count($breadcrumb) > 0)
                    <p class="text-sm text-[var(--color-muted)]">
                        {{ implode(' › ', $breadcrumb) }}
                    </p>
                @endif
            </div>

            {{-- Location card --}}
            <div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <h2 class="text-xs font-semibold uppercase tracking-widest text-[var(--color-muted)] mb-4">Location</h2>

                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    @if ($occurrence->country)
                        <div>
                            <dt class="text-[var(--color-muted)] text-xs mb-0.5">Country</dt>
                            <dd class="font-medium">{{ $occurrence->country }}</dd>
                        </div>
                    @endif

                    @if ($occurrence->state)
                        <div>
                            <dt class="text-[var(--color-muted)] text-xs mb-0.5">State / Province</dt>
                            <dd class="font-medium">{{ $occurrence->state }}</dd>
                        </div>
                    @endif

                    @if ($occurrence->formation)
                        <div>
                            <dt class="text-[var(--color-muted)] text-xs mb-0.5">Formation</dt>
                            <dd class="font-medium">{{ $occurrence->formation }}</dd>
                        </div>
                    @endif

                    @if ($occurrence->environment)
                        <div>
                            <dt class="text-[var(--color-muted)] text-xs mb-0.5">Environment</dt>
                            <dd class="font-medium">{{ $occurrence->environment }}</dd>
                        </div>
                    @endif

                    @if ($occurrence->lat !== null && $occurrence->lng !== null)
                        <div>
                            <dt class="text-[var(--color-muted)] text-xs mb-0.5">Coordinates</dt>
                            <dd class="font-medium font-mono text-xs">
                                {{ number_format($occurrence->lat, 4) }},
                                {{ number_format($occurrence->lng, 4) }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Age card --}}
            <div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <h2 class="text-xs font-semibold uppercase tracking-widest text-[var(--color-muted)] mb-4">Age</h2>

                <div class="flex items-center gap-3 mb-4">
                    @if ($occurrence->earlyInterval)
                        <span class="font-medium text-sm">{{ $occurrence->earlyInterval }}</span>
                    @endif
                    @if ($occurrence->lateInterval && $occurrence->lateInterval !== $occurrence->earlyInterval)
                        <span class="text-[var(--color-muted)] text-sm">&mdash;</span>
                        <span class="font-medium text-sm">{{ $occurrence->lateInterval }}</span>
                    @endif
                </div>

                @if ($occurrence->maxMa !== null && $occurrence->minMa !== null)
                    <p class="text-sm text-[var(--color-muted)] mb-3">
                        {{ number_format($occurrence->maxMa, 1) }} &ndash; {{ number_format($occurrence->minMa, 1) }} Ma
                    </p>

                    {{-- Position bar on 0–540 Ma scale --}}
                    @php
                        $barLeft  = round((540 - $occurrence->maxMa) / 540 * 100, 2);
                        $barWidth = round(($occurrence->maxMa - $occurrence->minMa) / 540 * 100, 2);
                        $barWidth = max($barWidth, 0.5);
                    @endphp
                    <div class="relative h-3 rounded-full bg-[var(--color-surface-sunken)] overflow-hidden">
                        <div
                            class="absolute top-0 h-full rounded-full bg-[var(--color-accent)]"
                            style="left: {{ $barLeft }}%; width: {{ $barWidth }}%"
                        ></div>
                    </div>
                    <div class="flex justify-between text-xs text-[var(--color-muted)] mt-1">
                        <span>540 Ma</span>
                        <span>0 Ma</span>
                    </div>
                @endif
            </div>

            {{-- Collection card --}}
            <div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <h2 class="text-xs font-semibold uppercase tracking-widest text-[var(--color-muted)] mb-4">Collection</h2>

                <p class="text-sm">
                    Collection
                    <a
                        href="https://paleobiodb.org/classic/displayCollResults?collection_no={{ $occurrence->collectionNo }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="font-mono text-[var(--color-accent)] hover:underline"
                    >
                        #{{ $occurrence->collectionNo }}
                    </a>
                    <span class="text-[var(--color-muted)] text-xs ml-1">(opens PBDB)</span>
                </p>

                <p class="text-xs text-[var(--color-muted)] mt-2">
                    Occurrence #{{ $occurrence->occurrenceNo }}
                </p>
            </div>

        </div>

        {{-- ─── Right column — mini map ──────────────────────────────── --}}
        <div>
            @if ($occurrence->lat !== null && $occurrence->lng !== null)
                <div
                    x-data="occurrenceMiniMap({{ $occurrence->lat }}, {{ $occurrence->lng }}, '{{ addslashes($occurrence->acceptedName) }}')"
                    id="occurrence-mini-map"
                    class="h-96 w-full rounded-xl border border-[var(--color-border)] overflow-hidden"
                ></div>
            @else
                <div class="h-96 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-surface-sunken)] flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-sm font-medium text-[var(--color-text)]">Location not available</p>
                        <p class="text-xs text-[var(--color-muted)] mt-1">No coordinates recorded for this occurrence.</p>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/occurrence.js')
@endpush