@props(['project', 'prev' => null, 'next' => null, 'context' => 'featured'])
<header class="sticky top-0 left-0 z-20 w-full bg-white border-b border-b-black shrink-0 h-[var(--header-height-sm)] px-16 xl:px-32 flex justify-between items-center">
  <h1 class="text-2xl leading-[1.174]">
    {{ $project->full_title }}
  </h1>
  <div class="hidden md:block">
    <x-projects.browser :prev="$prev" :next="$next" :context="$context" />
  </div>
</header>
