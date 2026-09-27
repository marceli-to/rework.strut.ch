@props(['prev' => null, 'next' => null, 'context' => 'featured'])
@php
  $imagesRoute = 'page.project.' . $context . '.images';
  $backRoute = $context === 'worklist' ? 'page.projects.worklist' : 'page.projects';
@endphp
<nav>
  <ul class="flex items-center gap-x-60">
    <li class="flex items-center gap-x-52 order-2 md:order-1">
      @if($prev)
        <a href="{{ route($imagesRoute, $prev->slug) }}" aria-label="Vorheriges Projekt" class="block w-15 h-27 text-black">
          <x-icons.chevron-left />
        </a>
      @endif
      @if($next)
        <a href="{{ route($imagesRoute, $next->slug) }}" aria-label="Nächstes Projekt" class="block w-15 h-27 text-black">
          <x-icons.chevron-right />
        </a>
      @endif
    </li>
    <li class="order-1 md:order-2">
      <a
        href="{{ route($backRoute) }}"
        aria-label="Zurück"
        class="block w-27 h-27 text-black">
        <x-icons.cross class="w-full h-auto" />
      </a>
    </li>
  </ul>
</nav>
