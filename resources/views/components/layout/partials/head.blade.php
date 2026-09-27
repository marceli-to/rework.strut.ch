@props([
  'title' => null,
  'description' => null,
  'ogImage' => null,
  'canonical' => null,
])
@php
  $appName = config('app.name');
  $defaultDescription = config('app.meta_description');
  $metaTitle = $title ?? $appName;
  $metaTitle = $metaTitle !== $appName ? "{$metaTitle} – {$appName}" : $appName;
  $metaDescription = $description ?? $seo?->og_description ?? $defaultDescription;
  $ogImageUrl = \App\Support\OgImage::url($ogImage, $seo);
  $canonicalUrl = $canonical ?? url()->current();
@endphp
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="format-detection" content="telephone=no, address=no, email=no">
<meta name="view-transition" content="same-origin">
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $canonicalUrl }}" />
<x-layout.partials.favicons />
<meta property="og:title" content="{{ $metaTitle }}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ $canonicalUrl }}" />
<meta property="og:image" content="{{ $ogImageUrl }}" />
<meta property="og:description" content="{{ $metaDescription }}" />
<meta property="og:site_name" content="{{ $appName }}" />
<meta property="og:locale" content="de_CH" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $metaTitle }}" />
<meta name="twitter:description" content="{{ $metaDescription }}" />
<meta name="twitter:image" content="{{ $ogImageUrl }}" />
@vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
