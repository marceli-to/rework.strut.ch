@props([
  'title' => null,
  'description' => null,
  'ogImage' => null,
  'canonical' => null,
  'project' => null,
  'prev' => null,
  'next' => null,
  'context' => 'featured',
])
<x-layout.partials.html>
  <x-layout.partials.head
    :title="$title"
    :description="$description"
    :og-image="$ogImage"
    :canonical="$canonical"
  />
  <x-layout.partials.body>
    <x-projects.header :project="$project" :prev="$prev" :next="$next" :context="$context" />
    <x-layout.partials.main class="relative">
      {{ $slot }}
    </x-layout.partials.main>
    <x-layout.partials.footer>
      {{ $footer ?? '' }}
    </x-layout.partials.footer>
  </x-layout.partials.body>
</x-layout.partials.html>
