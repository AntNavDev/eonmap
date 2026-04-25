@props([
    'title'       => null,
    'description' => 'Explore fossil occurrences from the Paleobiology Database on an interactive map.',
    'canonical'   => null,
])

@php
    $fullTitle = $title ? 'EonMap — ' . $title : 'EonMap';
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonical ?? url()->current() }}">

<meta property="og:type"        content="website">
<meta property="og:title"       content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url"         content="{{ $canonical ?? url()->current() }}">
<meta property="og:site_name"   content="EonMap">

<meta name="twitter:card"        content="summary">
<meta name="twitter:title"       content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $description }}">
