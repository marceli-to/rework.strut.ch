  <footer class="sticky bottom-0 left-0 z-20 w-full bg-white border-t border-t-black shrink-0 h-[var(--footer-height)] px-16 xl:px-32 flex justify-between items-center">

    @if (!request()->routeIs('page.project.*'))
      <x-menu.main.mobile.button class="md:hidden" />
    @endif

    {{ $slot }}

  </footer>

@if (!request()->routeIs('page.project.*'))
  <x-menu.main.mobile.container>
    <x-menu.main.mobile.item href="{{ route('page.projects') }}" label="Projekte" :active="request()->routeIs('page.projects*')" />
    <x-menu.main.mobile.item href="{{ route('page.atelier.profile') }}" label="Atelier" :active="request()->routeIs('page.atelier.*')" />
    <x-menu.main.mobile.item href="{{ route('page.contact') }}" label="Kontakt" :active="request()->routeIs('page.contact')" />
  </x-menu.main.mobile.container>
@endif