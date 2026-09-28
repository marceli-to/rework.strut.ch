@props([
  'title' => null,
  'description' => null,
  'home' => false,
])
@php
  $siteName = config('app.name');
  $fullTitle = $title ? "{$title} - {$siteName}" : $siteName;
  $description ??= 'Strut Architekten Winterthur';
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $description }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ $siteName }}">
<x-layout.partials.favicons />
<meta name="theme-color" content="#ffffff">
<meta name="format-detection" content="telephone=no">
@vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body>
<x-site.header />
<main class="pt-90 md:pt-170">
  <div @class(['page-block pb-10', $home ? 'md:pb-0' : 'md:pb-20'])>{{ $slot }}</div>
</main>
</body>
</html>
