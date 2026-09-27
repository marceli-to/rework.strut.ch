@props(['project', 'context' => 'featured'])
@php
  $imagesRoute = 'page.project.' . $context . '.images';
  $textRoute = 'page.project.' . $context . '.text';
@endphp
<nav class="md:flex md:justify-between md:items-center md:w-full">
  <ul class="flex flex-row gap-x-27">
    <li>
      <a
        href="{{ route($imagesRoute, $project->slug) }}"
        aria-label="Bilder"
        @class([
          'text-lg leading-none hover:text-accent transition-colors [.is-active]:text-accent',
          'is-active' => request()->routeIs('page.project.*.images'),
        ])>
        Bilder
      </a>
    </li>
    <li>
      <a
        href="{{ route($textRoute, $project->slug) }}"
        aria-label="Text"
        @class([
          'text-lg leading-none hover:text-accent transition-colors [.is-active]:text-accent',
          'is-active' => request()->routeIs('page.project.*.text'),
        ])>
        Text
      </a>
    </li>
  </ul>
  @if(request()->routeIs('page.project.*.images'))
    <div
      data-slides-counter="project-{{ $project->id }}"
      class="hidden md:block text-lg leading-none tabular-nums"
      aria-live="polite">
      <span data-slides-counter-current>1</span>/<span data-slides-counter-total>1</span>
    </div>
  @endif
</nav>
