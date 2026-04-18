@props([
    'title'       => 'Eonmap',
    'description' => 'Explore fossil occurrences from the Paleobiology Database on an interactive map.',
    'canonical'   => null,
])

<title>{{ $title !== 'Eonmap' ? $title . ' — Eonmap' : 'Eonmap' }}</title>
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonical ?? url()->current() }}">

<meta property="og:type"        content="website">
<meta property="og:title"       content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url"         content="{{ $canonical ?? url()->current() }}">
<meta property="og:site_name"   content="Eonmap">

<meta name="twitter:card"        content="summary">
<meta name="twitter:title"       content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
