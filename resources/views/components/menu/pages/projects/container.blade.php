<nav>
  <ul class="flex flex-row gap-x-27">
    <x-menu.pages.projects.item href="{{ route('page.projects') }}" label="Auswahl" :active="request()->routeIs('page.projects')" />
    <x-menu.pages.projects.item href="{{ route('page.projects.worklist') }}" label="Werkliste" :active="request()->routeIs('page.projects.worklist')" />
  </ul>
</nav>