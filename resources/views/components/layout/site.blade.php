@props([
  'title' => null,
  'description' => null,
  'ogImage' => null,
])
<x-layout.partials.html>
  <x-layout.partials.head
    :title="$title"
    :description="$description"
    :og-image="$ogImage"
  />
  <x-layout.partials.body>
    <x-layout.partials.header />
    <x-layout.partials.main>
      {{ $slot }}
    </x-layout.partials.main>
    <x-layout.partials.footer>
      {{ $footer ?? '' }}
    </x-layout.partials.footer>
  </x-layout.partials.body>
</x-layout.partials.html>
