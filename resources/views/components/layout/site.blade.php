{{--
  Public page layout. SEO: `title` (" - Strut Architekten" is appended),
  `page` (its meta description and Opengraph image), or `description` and
  `ogImage` (Media) directly; `canonical` defaults to the current URL.
--}}
@props([
  'title' => null,
  'description' => null,
  'page' => null,
  'ogImage' => null,
  'canonical' => null,
  'home' => false,
])
@php
  $siteName = config('app.name');
  $fullTitle = $title ? "{$title} - {$siteName}" : $siteName;
  $description = $description ?: $page?->meta_description ?: 'Strut Architekten Winterthur';
  $ogImage ??= $page?->ogImage();
  $canonical ??= url()->current();
@endphp
<!DOCTYPE html>
<html lang="de" class="min-h-full overflow-y-scroll">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $description }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $ogImage?->ogUrl() ?? \App\Support\OgImage::url() }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:site_name" content="{{ $siteName }}">
<x-layout.partials.favicons />
<meta name="theme-color" content="#ffffff">
<meta name="format-detection" content="telephone=no">
@vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body class="min-h-full font-regular text-sm leading-[1.13] sm:text-xs sm:leading-[1.2] md:text-2xl text-black bg-white antialiased [text-rendering:optimizeLegibility]">
<x-site.header />
<main class="pt-header md:pt-header-md">
  <div @class([
    'relative mx-auto max-w-page-xs px-10 pb-10 sm:max-w-none md:px-20 lg:max-w-page-lg',
    $home ? 'md:pb-0' : 'md:pb-20',
  ])>{{ $slot }}</div>
</main>
<x-site.lightbox />
</body>
</html>
