<header 
  class="bg-white sticky top-0 z-40 md:relative border-b border-b-black w-full h-[var(--header-height-md)] xl:h-[var(--header-height-lg)] px-16 xl:px-32 pt-16 xl:pt-21 shrink-0 transition-transform duration-600 ease-out will-change-transform [&.is-hidden]:-translate-y-full motion-reduce:transition-none motion-reduce:[&.is-hidden]:translate-y-0" 
  data-shy>
  <x-grid.container>
    <x-grid.span class="md:col-span-6 xl:col-span-8">
      @if (request()->routeIs('page.landing'))
        <x-icons.logo class="w-212 xl:w-214 h-auto overflow-visible [&_[data-logo-path]]:transition-transform [&_[data-logo-path]]:duration-900 [&_[data-logo-path]]:delay-600 [&_[data-logo-path]]:ease-in-out [&_[data-logo-path]]:translate-x-[calc(100vw-100%)] md:[&_[data-logo-path]]:translate-x-[calc(100vw-100%)] xl:[&_[data-logo-path]]:translate-x-[calc(100vw-48px-100%)] [&_[data-logo-path]]:will-change-transform [&.is-ready_[data-logo-path]]:translate-x-0 motion-reduce:[&_[data-logo-path]]:transition-none motion-reduce:[&_[data-logo-path]]:translate-x-0" />
      @else
        <a 
          href="{{ route('page.landing') }}"
          class="block"
          aria-label="Zurück zur Startseite">
          <x-icons.logo class="w-212 xl:w-214 h-auto" />
        </a>
      @endif
    </x-grid.span>

    <x-grid.span class="hidden md:flex md:flex-row justify-end md:col-span-6 xl:col-span-4">
      <x-menu.main.desktop.container>
        <x-menu.main.desktop.item href="{{ route('page.projects') }}" label="Projekte" :active="request()->routeIs('page.project*')" />
        <x-menu.main.desktop.item href="{{ route('page.atelier.profile') }}" label="Atelier" :active="request()->routeIs('page.atelier.*')" />
        <x-menu.main.desktop.item href="{{ route('page.contact') }}" label="Kontakt" :active="request()->routeIs('page.contact')" />
      </x-menu.main.desktop.container>
    </x-grid.span>

  </x-grid.container>
</header>