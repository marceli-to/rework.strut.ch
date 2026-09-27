<nav>
  <ul class="flex flex-row gap-x-27">
    <x-menu.pages.atelier.item href="{{ route('page.atelier.profile') }}" label="Profil" :active="request()->routeIs('page.atelier.profile')" />
    <x-menu.pages.atelier.item href="{{ route('page.atelier.team') }}" label="Team" :active="request()->routeIs('page.atelier.team')" />
    <x-menu.pages.atelier.item href="{{ route('page.atelier.jobs') }}" label="Jobs" :active="request()->routeIs('page.atelier.jobs')" />
  </ul>
</nav>