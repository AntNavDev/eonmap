@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">

    {{-- Page header --}}
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-text">How to Use Eonmap</h1>
        <p class="mt-2 text-muted">Everything you need to go from zero to exploring the fossil record.</p>
    </div>

    {{-- Table of contents --}}
    <nav class="mb-12 rounded-xl border border-border bg-surface p-6" aria-label="Guide sections">
        <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-muted">On this page</p>
        <ol class="space-y-1 text-sm">
            <li><a href="#simple" class="text-accent hover:underline">The Simple Version</a></li>
            <li class="pl-4"><a href="#what-is-eonmap" class="text-muted hover:text-text">What is Eonmap?</a></li>
            <li class="pl-4"><a href="#what-is-pbdb" class="text-muted hover:text-text">What is the Paleobiology Database?</a></li>
            <li class="pl-4"><a href="#what-is-occurrence" class="text-muted hover:text-text">What is a fossil occurrence?</a></li>
            <li class="pl-4"><a href="#getting-started" class="text-muted hover:text-text">Getting started in 3 steps</a></li>
            <li class="pl-4"><a href="#pages-overview" class="text-muted hover:text-text">What each page does</a></li>
            <li class="mt-2"><a href="#advanced" class="text-accent hover:underline">The Advanced Version</a></li>
            <li class="pl-4"><a href="#filters" class="text-muted hover:text-text">Filter reference</a></li>
            <li class="pl-4"><a href="#map-modes" class="text-muted hover:text-text">Map modes &amp; controls</a></li>
            <li class="pl-4"><a href="#browse-table" class="text-muted hover:text-text">Browse table &amp; CSV export</a></li>
            <li class="pl-4"><a href="#taxon-pages" class="text-muted hover:text-text">Taxon pages</a></li>
            <li class="pl-4"><a href="#occurrence-detail" class="text-muted hover:text-text">Occurrence detail pages</a></li>
            <li class="pl-4"><a href="#api-details" class="text-muted hover:text-text">How the API works</a></li>
        </ol>
    </nav>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- SIMPLE SECTION                                                      --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}

    <section id="simple" class="mb-16">
        <h2 class="mb-6 text-2xl font-bold text-text">The Simple Version</h2>

        {{-- What is Eonmap --}}
        <div id="what-is-eonmap" class="mb-8 rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-3 text-lg font-semibold text-text">What is Eonmap?</h3>
            <p class="text-sm leading-relaxed text-text">
                Eonmap is an interactive map for exploring where fossils have been found across the world.
                You can search for any group of animals or plants — from dinosaurs to trilobites to ancient sharks —
                and see every fossil site recorded by scientists, plotted on a map or listed in a table.
            </p>
            <p class="mt-3 text-sm leading-relaxed text-text">
                Think of it like a search engine for the fossil record. Instead of searching the web,
                you are searching a global scientific database of fossil sites. Every dot on the map
                is a real place where a real fossil was found and recorded by a researcher.
            </p>
            <div class="mt-4 rounded-lg bg-accent-subtle border border-accent-muted p-4 text-sm text-text">
                <strong class="text-accent">Why would I use this?</strong>
                <ul class="mt-2 space-y-1 list-disc list-inside text-muted">
                    <li>You are curious where dinosaurs lived and want to see it on a map</li>
                    <li>You want to understand how life was distributed across ancient continents</li>
                    <li>You are researching a specific fossil group, time period, or region</li>
                    <li>You want to download a dataset of fossil occurrences for your own analysis</li>
                    <li>You just find the history of life on Earth fascinating</li>
                </ul>
            </div>
        </div>

        {{-- What is PBDB --}}
        <div id="what-is-pbdb" class="mb-8 rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-3 text-lg font-semibold text-text">What is the Paleobiology Database?</h3>
            <p class="text-sm leading-relaxed text-text">
                All of the data on Eonmap comes from the
                <a href="https://paleobiodb.org" target="_blank" rel="noopener noreferrer" class="text-accent hover:underline">Paleobiology Database (PBDB)</a>,
                a free, public scientific database maintained by researchers worldwide.
                It contains records of fossil occurrences collected from the published scientific literature —
                when a paleontologist publishes a paper describing fossils from a site, that data gets entered
                into PBDB.
            </p>
            <p class="mt-3 text-sm leading-relaxed text-text">
                Eonmap does not store any data of its own. Every search you perform sends a query directly
                to the PBDB API and displays whatever PBDB returns. This means the data is always current
                with the scientific literature (results are cached for one hour to keep things fast).
            </p>
        </div>

        {{-- What is an occurrence --}}
        <div id="what-is-occurrence" class="mb-8 rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-3 text-lg font-semibold text-text">What is a fossil occurrence?</h3>
            <p class="text-sm leading-relaxed text-text">
                An <strong>occurrence</strong> is one record of a taxon being found at one location.
                If a paleontologist found three different species at the same dig site, that is three occurrences.
                If the same species was found at ten different sites across Montana, that is ten occurrences.
            </p>
            <p class="mt-3 text-sm leading-relaxed text-text">
                Each occurrence has a name (the accepted scientific name of what was found), a location
                (latitude and longitude of the collection site), an age (in millions of years), and various
                other details like the rock formation it came from and the environment it was deposited in.
            </p>
            <p class="mt-3 text-sm leading-relaxed text-muted text-sm">
                Note: one occurrence does not necessarily mean one individual animal. It means one scientific
                collection event — a site might yield dozens of specimens from the same taxon, but they
                are grouped into a single occurrence record.
            </p>
        </div>

        {{-- Getting started --}}
        <div id="getting-started" class="mb-8 rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-3 text-lg font-semibold text-text">Getting started in 3 steps</h3>
            <ol class="space-y-5">
                <li class="flex gap-4">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-accent text-on-accent text-xs font-bold">1</span>
                    <div>
                        <p class="text-sm font-semibold text-text">Go to the Map page</p>
                        <p class="mt-1 text-sm text-muted">The map starts empty. You will see a message saying "Add a filter to search" — this is normal. The map waits for you to tell it what to look for before fetching any data.</p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-accent text-on-accent text-xs font-bold">2</span>
                    <div>
                        <p class="text-sm font-semibold text-text">Pick a Quick Start preset or set your own filters</p>
                        <p class="mt-1 text-sm text-muted">The panel on the left has eight preset searches — "Age of Dinosaurs", "T. rex Country", "Trilobite World", and others. Click any preset to instantly populate the filters and run the search. Or choose your own organism, time period, and location.</p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-accent text-on-accent text-xs font-bold">3</span>
                    <div>
                        <p class="text-sm font-semibold text-text">Press "Apply Filters" and explore</p>
                        <p class="mt-1 text-sm text-muted">The map will populate with markers showing every matching fossil site. Click any marker to see its details. Switch to the Browse page to see the same results in a sortable table, or download the full set as a CSV file.</p>
                    </div>
                </li>
            </ol>
        </div>

        {{-- Pages overview --}}
        <div id="pages-overview" class="rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-4 text-lg font-semibold text-text">What each page does</h3>
            <div class="space-y-4">
                <div class="rounded-lg border border-border p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-widest text-muted">Map</span>
                        <span class="text-xs text-muted font-mono">/map</span>
                    </div>
                    <p class="text-sm text-text">The main exploration page. Apply filters and see matching fossil sites plotted on an interactive world map. Markers cluster when zoomed out and spread apart as you zoom in. Clicking a cluster zooms into it; clicking a single marker opens a popup with the taxon name and a link to the full occurrence detail page.</p>
                </div>
                <div class="rounded-lg border border-border p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-widest text-muted">Browse</span>
                        <span class="text-xs text-muted font-mono">/browse</span>
                    </div>
                    <p class="text-sm text-text">The same search results shown as a paginated, sortable table. Useful when you want to scan taxon names, locations, and ages in a structured format. You can also download the full result set as a CSV file from this page.</p>
                </div>
                <div class="rounded-lg border border-border p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-widest text-muted">Occurrence detail</span>
                        <span class="text-xs text-muted font-mono">/occurrences/{id}</span>
                    </div>
                    <p class="text-sm text-text">A deep-dive page for a single fossil occurrence. Shows the full taxonomic classification, location details, geological age with a timeline bar, the PBDB collection number (with a link back to PBDB), and a mini-map showing exactly where the specimen was found.</p>
                </div>
                <div class="rounded-lg border border-border p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-widest text-muted">Taxon page</span>
                        <span class="text-xs text-muted font-mono">/taxa/{name}</span>
                    </div>
                    <p class="text-sm text-text">A summary page for any taxonomic name — a genus, family, order, or higher group. Shows a bar chart of occurrences by geologic period, a temporal range timeline, a world map of all known fossil sites, and a breakdown of the data by phylum, class, and depositional environment.</p>
                </div>
            </div>
        </div>
    </section>

    <hr class="border-border mb-16">

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- ADVANCED SECTION                                                    --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}

    <section id="advanced">
        <h2 class="mb-6 text-2xl font-bold text-text">The Advanced Version</h2>

        {{-- Filter reference --}}
        <div id="filters" class="mb-8 rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-1 text-lg font-semibold text-text">Filter reference</h3>
            <p class="mb-5 text-sm text-muted">All filters are optional and combinable. Leave everything blank to match all organisms across all time and all locations — though for most searches you will want at least one filter to narrow the results.</p>

            <div class="space-y-5 text-sm">

                <div>
                    <p class="font-semibold text-text">Organism</p>
                    <p class="mt-1 text-muted">Filters by taxonomic group using PBDB's <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">base_name</code> parameter, which matches the given name and all taxa nested beneath it. For example, selecting "Dinosaurs" (<code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">Dinosauria</code>) returns every known dinosaur species in the database, not just records filed under that exact name.</p>
                    <p class="mt-2 text-muted">The dropdown offers fifteen curated groups. If you need something more specific — a particular genus, family, or any name not in the list — click "Type a custom name" to enter a PBDB taxon name directly. Examples: <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">Allosauridae</code>, <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">Pterosauria</code>, <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">Homo</code>.</p>
                </div>

                <div>
                    <p class="font-semibold text-text">Time Period</p>
                    <p class="mt-1 text-muted">Filters to occurrences whose geological age overlaps with the selected interval. The dropdown covers all twelve major Phanerozoic periods from the Cambrian (541–485 Ma) through the Quaternary (2.6 Ma – present). This maps to PBDB's <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">interval</code> parameter.</p>
                </div>

                <div>
                    <p class="font-semibold text-text">Age Range (Ma)</p>
                    <p class="mt-1 text-muted">A numeric alternative to Time Period. Enter minimum and maximum ages in Ma (millions of years ago). The range 0–540 covers the entire Phanerozoic eon — the period of complex animal life. You can use this alongside the Time Period filter or instead of it. Maps to PBDB's <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">min_ma</code> and <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">max_ma</code> parameters.</p>
                </div>

                <div>
                    <p class="font-semibold text-text">Environment</p>
                    <p class="mt-1 text-muted">Filters by the depositional environment of the rock formation the fossil was found in — where the sediment was originally laid down, not necessarily where the animal lived. You can select multiple environments simultaneously. Available options:</p>
                    <div class="mt-2 grid grid-cols-2 gap-x-6 gap-y-1 sm:grid-cols-3">
                        @foreach ([
                            'Terrestrial', 'Marine', 'Carbonate', 'Siliciclastic',
                            'Reef / bioherm', 'Lacustrine', 'Fluvial', 'Deltaic',
                            'Estuary / bay', 'Paralic', 'Peritidal', 'Offshore', 'Coastal',
                        ] as $env)
                            <span class="text-muted text-xs">{{ $env }}</span>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="font-semibold text-text">Country</p>
                    <p class="mt-1 text-muted">Restricts results to a single country using ISO 3166-1 alpha-2 codes sent to PBDB's <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">cc</code> parameter. The dropdown lists seventeen major fossil-bearing countries. If the country you need is not listed, it can still be reached via the Bounding Box filter.</p>
                </div>

                <div>
                    <p class="font-semibold text-text">ID Quality</p>
                    <p class="mt-1 text-muted">Controls the certainty of the taxonomic identification. <strong>Any</strong> (default) returns all records. <strong>Certain only</strong> excludes records where the identification was tentative (e.g. <em>cf.</em> or <em>aff.</em> qualifiers in the literature). <strong>Uncertain only</strong> returns only those tentative identifications. Maps to PBDB's <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">idqual</code> parameter.</p>
                </div>

                <div>
                    <p class="font-semibold text-text">Bounding Box</p>
                    <p class="mt-1 text-muted">Restricts results to a geographic rectangle you draw directly on the map. Click the rectangle icon in the Leaflet draw toolbar, drag a box over the area of interest, and the coordinates are filled in automatically. The bounding box is sent as latitude/longitude bounds (<code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">latmin</code>, <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">latmax</code>, <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">lngmin</code>, <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">lngmax</code>) to PBDB. Click "Clear" in the filter panel to remove it.</p>
                </div>

            </div>
        </div>

        {{-- Quick Start presets --}}
        <div class="mb-8 rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-1 text-lg font-semibold text-text">Quick Start presets</h3>
            <p class="mb-4 text-sm text-muted">The filter panel includes eight curated preset searches designed to highlight different eras and groups. Each preset pre-fills all relevant filters and applies the search immediately.</p>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ([
                    ['🦕', 'Age of Dinosaurs',  'Non-avian dinosaurs from the Triassic through the end-Cretaceous extinction (252–66 Ma).'],
                    ['🦖', 'T. rex Country',     'Tyrannosaurid fossils from North America during the Cretaceous period, filtered to the United States.'],
                    ['🧊', 'Ice Age Giants',     'Pleistocene megafauna — mammals from 0–2.6 Ma, covering woolly mammoths and their contemporaries.'],
                    ['🦣', 'Rise of Mammals',    'Mammal fossils from the Paleogene (23–66 Ma), the era that followed the non-avian dinosaur extinction.'],
                    ['🪲', 'Trilobite World',    'Trilobites across all time periods and locations.'],
                    ['🐚', 'Ammonites',          'Ammonoid cephalopods across all time.'],
                    ['🌊', 'Cambrian Seas',       'Marine life from the Cambrian period — the dawn of complex animal life.'],
                    ['💀', 'The Great Dying',     'Fossils from around the Permian–Triassic boundary (245–260 Ma), the largest mass extinction in Earth\'s history.'],
                ] as [$emoji, $name, $desc])
                    <div class="flex gap-3 rounded-lg border border-border p-3 text-sm">
                        <span class="text-xl leading-none mt-0.5">{{ $emoji }}</span>
                        <div>
                            <p class="font-medium text-text">{{ $name }}</p>
                            <p class="mt-0.5 text-muted text-xs">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Map modes --}}
        <div id="map-modes" class="mb-8 rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-1 text-lg font-semibold text-text">Map modes &amp; controls</h3>
            <p class="mb-5 text-sm text-muted">Three toggle controls appear in the top-right corner of the map after results load.</p>
            <div class="space-y-4 text-sm">
                <div>
                    <p class="font-semibold text-text">Heatmap</p>
                    <p class="mt-1 text-muted">Switches from individual markers to a density heatmap. Useful when results are dense and clustered markers become hard to read — the heatmap makes geographic concentrations of fossil sites immediately visible. Powered by the Leaflet.heat plugin.</p>
                </div>
                <div>
                    <p class="font-semibold text-text">Paleo (Paleocoordinates)</p>
                    <p class="mt-1 text-muted">Repositions each marker to its reconstructed ancient location — where that piece of crust was when the fossil was deposited, rather than where it sits today after millions of years of plate tectonics. PBDB provides these paleocoordinates (<code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">pla</code> / <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">plo</code> fields) as part of the <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">paleoloc</code> response block. Only occurrences with valid paleocoordinates move; others stay in place.</p>
                </div>
                <div>
                    <p class="font-semibold text-text">Basemap</p>
                    <p class="mt-1 text-muted">Switches the background map tile layer. Three options are available:</p>
                    <ul class="mt-2 space-y-1 list-disc list-inside text-muted">
                        <li><strong class="text-text">OSM</strong> — OpenStreetMap. The default. Shows roads, place names, and terrain.</li>
                        <li><strong class="text-text">Esri Imagery</strong> — Satellite imagery from Esri. Useful for examining the geography of fossil sites.</li>
                        <li><strong class="text-text">CartoDB Dark</strong> — A dark minimalist base. Marker colours stand out clearly against it.</li>
                    </ul>
                </div>
            </div>
            <div class="mt-5 rounded-lg bg-surface-raised border border-border p-3 text-xs text-muted">
                <strong class="text-text">Result cap:</strong> The map fetches up to 500 occurrences per query. If your filters match more than 500 records, a counter in the bottom-right of the map will show "Showing 500 of X occurrences". Use the Bounding Box or additional filters to narrow the result set if you want to see the full picture for a particular region.
            </div>
        </div>

        {{-- Browse table --}}
        <div id="browse-table" class="mb-8 rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-1 text-lg font-semibold text-text">Browse table &amp; CSV export</h3>
            <p class="mb-4 text-sm text-muted">The Browse page shows the same results as the map in a paginated table. The Map and Browse pages share the same filter panel — filters applied on one page carry over to the other.</p>
            <div class="mb-4">
                <p class="text-sm font-semibold text-text mb-2">Table columns</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left text-xs text-muted uppercase tracking-wide">
                                <th class="pb-2 pr-4 font-medium">Column</th>
                                <th class="pb-2 font-medium">What it shows</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ([
                                ['Occurrence #',    'The PBDB occurrence number. Links to the occurrence detail page.'],
                                ['Accepted Name',   'The currently accepted scientific name for what was found.'],
                                ['Rank',            'The taxonomic rank of the accepted name (genus, species, family, etc.).'],
                                ['Early Interval',  'The name of the earliest possible geological interval for this occurrence.'],
                                ['Late Interval',   'The name of the latest possible geological interval (may be the same as Early).'],
                                ['Max Age (Ma)',     'The oldest possible age of the occurrence in millions of years.'],
                                ['Min Age (Ma)',     'The youngest possible age of the occurrence in millions of years.'],
                                ['Country',         'Country where the fossil was collected.'],
                                ['State',           'State or province where the fossil was collected.'],
                                ['Formation',       'The geological formation the fossil was found in.'],
                                ['Environment',     'The depositional environment of the formation.'],
                            ] as [$col, $desc])
                                <tr>
                                    <td class="py-2 pr-4 font-mono text-xs text-accent whitespace-nowrap">{{ $col }}</td>
                                    <td class="py-2 text-muted text-xs">{{ $desc }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <p class="text-sm font-semibold text-text mb-1">Pagination &amp; sorting</p>
                <p class="text-sm text-muted">Results are paginated at 25, 50, or 100 rows per page. Click any column header to sort by that column; click again to reverse the sort direction. Each page change fetches a fresh API request against PBDB using an offset, so large result sets are navigable without loading everything at once.</p>
            </div>
            <div class="mt-4">
                <p class="text-sm font-semibold text-text mb-1">CSV export</p>
                <p class="text-sm text-muted">The "Export CSV" button downloads all occurrences matching the current filters — not just the current page — as a comma-separated file. The download is streamed directly from PBDB through Eonmap's export endpoint (<code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">/api/export/occurrences</code>) and includes the same fields shown in the table. This is useful for further analysis in Excel, R, Python, or any other tool.</p>
            </div>
        </div>

        {{-- Taxon pages --}}
        <div id="taxon-pages" class="mb-8 rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-1 text-lg font-semibold text-text">Taxon pages</h3>
            <p class="mb-4 text-sm text-muted">Navigate to <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">/taxa/{name}</code> for any valid PBDB taxon name — for example, <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">/taxa/Dinosauria</code> or <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">/taxa/Tyrannosaurus</code>. You can also reach taxon pages by clicking taxon name links where they appear in the interface.</p>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="font-semibold text-text">Occurrences by Geologic Period</p>
                    <p class="mt-1 text-muted">A bar chart (Chart.js) showing how many occurrences fall within each geological period. Useful for understanding when a group was most prevalent in the fossil record.</p>
                </div>
                <div>
                    <p class="font-semibold text-text">Temporal Range</p>
                    <p class="mt-1 text-muted">A timeline (vis-timeline) plotting the individual age ranges of occurrences along a geological time axis. Shows the full stratigraphic spread of the group at a glance.</p>
                </div>
                <div>
                    <p class="font-semibold text-text">Geographic Distribution</p>
                    <p class="mt-1 text-muted">A world map showing all fossil sites for the taxon, using the same Leaflet map as the main Map page.</p>
                </div>
                <div>
                    <p class="font-semibold text-text">Classification Summary</p>
                    <p class="mt-1 text-muted">Three ranked tables breaking down the occurrence set by phylum, class, and depositional environment. Useful for understanding what is actually inside a broad grouping — for example, what phyla make up the "Cambrian Seas" results.</p>
                </div>
            </div>
            <div class="mt-4 rounded-lg bg-surface-raised border border-border p-3 text-xs text-muted">
                <strong class="text-text">Data note:</strong> Taxon pages fetch up to 1,000 occurrences from PBDB for rendering the charts and maps. The occurrence count badge shows the true total from PBDB. For very common groups the badge may show a larger number than what is reflected in the charts — a "(charts based on first 1,000)" note will appear when this applies.
            </div>
        </div>

        {{-- Occurrence detail --}}
        <div id="occurrence-detail" class="mb-8 rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-1 text-lg font-semibold text-text">Occurrence detail pages</h3>
            <p class="mb-4 text-sm text-muted">Each occurrence in the browse table or map popup links to a detail page at <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">/occurrences/{id}</code>, where <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">id</code> is the PBDB occurrence number.</p>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="font-semibold text-text">Taxonomy card</p>
                    <p class="mt-1 text-muted">The accepted scientific name, taxonomic rank, and classification breadcrumb (Phylum › Class › Order › Family › Genus) as recorded in PBDB.</p>
                </div>
                <div>
                    <p class="font-semibold text-text">Location card</p>
                    <p class="mt-1 text-muted">Country, state or province, geological formation, depositional environment, and decimal coordinates of the collection site.</p>
                </div>
                <div>
                    <p class="font-semibold text-text">Age card</p>
                    <p class="mt-1 text-muted">The geological interval name(s) and the age range in Ma. A visual bar shows where this occurrence sits on a 0–540 Ma timeline, giving immediate context for how old the fossil is relative to the entire Phanerozoic.</p>
                </div>
                <div>
                    <p class="font-semibold text-text">Collection card</p>
                    <p class="mt-1 text-muted">The PBDB collection number, which groups occurrences from the same dig site. Clicking the collection number opens the original collection record on paleobiodb.org, where you can see all other taxa found at the same site.</p>
                </div>
                <div>
                    <p class="font-semibold text-text">Mini-map</p>
                    <p class="mt-1 text-muted">A small Leaflet map pinpointing the collection site. If coordinates were not recorded in PBDB, a "Location not available" placeholder is shown instead.</p>
                </div>
            </div>
            <p class="mt-4 text-sm text-muted">Occurrence detail pages are also tracked in a <strong class="text-text">Recently Viewed</strong> list accessible from the navigation bar. The last five occurrences you visited in your current session are stored there for quick re-access.</p>
        </div>

        {{-- API details --}}
        <div id="api-details" class="rounded-xl border border-border bg-surface p-6">
            <h3 class="mb-1 text-lg font-semibold text-text">How the API works</h3>
            <p class="mb-4 text-sm text-muted">Eonmap is a read-only front-end for one PBDB endpoint. Understanding this helps set expectations about what the data represents and why some searches are slower than others.</p>

            <div class="space-y-4 text-sm">
                <div>
                    <p class="font-semibold text-text">Endpoint</p>
                    <p class="mt-1 text-muted">Every search calls:</p>
                    <pre class="mt-2 rounded-lg bg-surface-raised border border-border px-4 py-3 text-xs font-mono overflow-x-auto text-text">GET https://paleobiodb.org/data1.2/occs/list.json</pre>
                    <p class="mt-2 text-muted">The filters you set are translated into query string parameters — for example, selecting "Dinosaurs" and "Cretaceous" produces <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">base_name=Dinosauria&interval=Cretaceous</code>. You can explore the PBDB API directly at <a href="https://paleobiodb.org/data1.2/" target="_blank" rel="noopener noreferrer" class="text-accent hover:underline">paleobiodb.org/data1.2</a> if you want to build your own queries.</p>
                </div>

                <div>
                    <p class="font-semibold text-text">Response blocks</p>
                    <p class="mt-1 text-muted">Eonmap requests five data blocks from PBDB on every search: <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">coords</code> (latitude/longitude), <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">class</code> (full taxonomic classification), <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">loc</code> (country, state, formation, environment), <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">time</code> (geological intervals and Ma ages), and <code class="font-mono text-xs bg-surface-raised px-1 py-0.5 rounded">paleoloc</code> (reconstructed ancient coordinates).</p>
                </div>

                <div>
                    <p class="font-semibold text-text">Caching</p>
                    <p class="mt-1 text-muted">Every unique combination of filters is cached for one hour. The first person to run a given search waits for PBDB to respond (which can occasionally take 30–60 seconds for large queries); anyone running the identical search within the next hour gets the cached result instantly. The cache is keyed by an MD5 hash of the full parameter set.</p>
                </div>

                <div>
                    <p class="font-semibold text-text">Record limits</p>
                    <p class="mt-1 text-muted">The map fetches a maximum of 500 occurrences per query. The browse table paginates through results in pages of 25, 50, or 100. Taxon pages fetch up to 1,000 occurrences for their charts and maps. These limits exist to keep response sizes and render times manageable — PBDB's full dataset for a broad group like Bivalvia is far larger than is practical to plot on a single map.</p>
                </div>

                <div>
                    <p class="font-semibold text-text">No authentication required</p>
                    <p class="mt-1 text-muted">PBDB is a public API with no key requirement. Eonmap does not create an account, store any personal data, or perform any write operations. It is purely a read-only interface over publicly available scientific data.</p>
                </div>
            </div>
        </div>

    </section>

</div>
@endsection
