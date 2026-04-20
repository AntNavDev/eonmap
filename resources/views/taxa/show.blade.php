@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- ─── Page header ────────────────────────────────────────────────── --}}
    <div class="mb-8 flex flex-wrap items-center gap-4">
        <h1 class="text-3xl font-bold italic text-text">{{ $name }}</h1>

        <span class="inline-flex items-center rounded-full bg-accent-subtle border border-accent-muted px-3 py-1 text-sm font-medium text-accent">
            {{ number_format($totalCount) }} occurrences
        </span>

        @if ($fetchedCount < $totalCount)
            <span class="text-sm text-muted">
                (charts based on first {{ number_format($fetchedCount) }})
            </span>
        @endif

        <a
            href="https://paleobiodb.org/classic/checkTaxonInfo?taxon_name={{ urlencode($name) }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-1 text-sm text-muted hover:text-text transition-colors"
        >
            View on PBDB
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>

    @if ($totalCount === 0)
        @include('partials.empty-state', ['message' => 'No occurrences found for ' . $name . '.'])
    @else
        <div class="space-y-8">

            {{-- ─── Section 1: Occurrence counts by geologic period ────── --}}
            <div
                x-data="taxonCharts({ occurrences: @js(array_values($occurrences->items)) })"
                class="rounded-xl border border-border bg-surface p-6"
            >
                <h2 class="text-xs font-semibold uppercase tracking-widest text-muted mb-6">
                    Occurrences by Geologic Period
                </h2>
                <canvas id="period-chart" class="max-h-80" role="img" aria-label="Bar chart of occurrence counts by geologic period for {{ $name }}"></canvas>
            </div>

            {{-- ─── Section 2: Geologic timeline ──────────────────────── --}}
            <div
                x-data="taxonTimeline({ occurrences: @js(array_values($occurrences->items)), name: @js($name) })"
                class="rounded-xl border border-border bg-surface p-6"
            >
                <h2 class="text-xs font-semibold uppercase tracking-widest text-muted mb-6">
                    Temporal Range
                </h2>
                <div id="taxon-timeline" class="h-32" role="img" aria-label="Geologic timeline showing the temporal range of {{ $name }}"></div>
            </div>

            {{-- ─── Section 3: Geographic distribution ────────────────── --}}
            <div class="rounded-xl border border-border bg-surface p-6">
                <h2 class="text-xs font-semibold uppercase tracking-widest text-muted mb-6">
                    Geographic Distribution
                </h2>
                <div
                    x-data="taxonMiniMap({ occurrences: @js(array_values($occurrences->items)) })"
                    id="taxon-map"
                    class="h-96 w-full rounded-lg overflow-hidden"
                    role="application"
                    aria-label="Map showing geographic distribution of {{ $name }}"
                ></div>
            </div>

            {{-- ─── Section 4: Classification summary ─────────────────── --}}
            <div class="rounded-xl border border-border bg-surface p-6">
                <h2 class="text-xs font-semibold uppercase tracking-widest text-muted mb-6">
                    Classification Summary
                </h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">

                    {{-- By Phylum --}}
                    <div>
                        <h3 class="text-sm font-semibold text-text mb-3">By Phylum</h3>
                        @if (count($byPhylum) > 0)
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-border">
                                    @foreach ($byPhylum as $phylum => $count)
                                        <tr>
                                            <td class="py-1.5 text-text">{{ $phylum }}</td>
                                            <td class="py-1.5 text-right font-mono text-muted">
                                                {{ number_format($count) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-sm text-muted">No data.</p>
                        @endif
                    </div>

                    {{-- By Class --}}
                    <div>
                        <h3 class="text-sm font-semibold text-text mb-3">By Class</h3>
                        @if (count($byClass) > 0)
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-border">
                                    @foreach ($byClass as $class => $count)
                                        <tr>
                                            <td class="py-1.5 text-text">{{ $class }}</td>
                                            <td class="py-1.5 text-right font-mono text-muted">
                                                {{ number_format($count) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-sm text-muted">No data.</p>
                        @endif
                    </div>

                    {{-- By Environment --}}
                    <div>
                        <h3 class="text-sm font-semibold text-text mb-3">By Environment</h3>
                        @if (count($byEnvironment) > 0)
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-border">
                                    @foreach ($byEnvironment as $env => $count)
                                        <tr>
                                            <td class="py-1.5 text-text">{{ $env }}</td>
                                            <td class="py-1.5 text-right font-mono text-muted">
                                                {{ number_format($count) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-sm text-muted">No data.</p>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    @endif

</div>
@endsection

@push('scripts')
    @vite('resources/js/taxon.js')
@endpush
