<!DOCTYPE html>
<html lang="de" class="h-full scroll-smooth">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name', 'CMS') }}</title>
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<x-layout.partials.favicons />
@vite(['resources/css/app.css'])
</head>
<body class="h-full text-sm font-sans tracking-wide text-gray-900 antialiased bg-gray-100">
	<div id="app"></div>
	@vite('resources/js/app/app.js')
</body>
</html>
