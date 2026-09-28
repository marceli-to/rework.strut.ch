{{--
  Header and main navigation. $nav comes from GetNavigation (view composer).
  JS state: header[data-scroll="tiny|hidden"] (modules/header.js),
  html[data-menu-open] and ul[data-open] (modules/menu.js).
--}}
<header
  class="fixed z-5 w-full h-header md:h-header-md bg-white
    header-tiny:h-header-tiny header-tiny:bg-transparent
    md:header-tiny:h-header-tiny-md md:header-tiny:bg-white md:header-tiny:translate-y-0 md:header-tiny:transition-transform md:header-tiny:duration-360 md:header-tiny:delay-240 md:header-tiny:ease-out
    md:header-hidden:-translate-y-full md:header-hidden:transition-transform md:header-hidden:duration-320 md:header-hidden:ease-out
    menu-open:h-full! menu-open:bg-white!"
  data-header>
  <div class="relative z-20 mx-auto max-w-page-xs px-10 sm:max-w-none md:px-20 md:pt-20 lg:max-w-page-lg">
    <button
      type="button"
      class="block md:hidden relative top-10 z-1 w-42 h-27 cursor-pointer bg-no-repeat bg-top-left bg-[url(/img/icons/menu-burger.svg)] bg-size-[42px_23px] aria-expanded:bg-[url(/img/icons/menu-cross.svg)] aria-expanded:bg-size-[27px_27px]"
      aria-expanded="false"
      aria-controls="site-nav"
      aria-label="Menü anzeigen"
      data-menu-button></button>
    <a
      href="/"
      class="block absolute z-15 top-10 left-[calc(100%-126px)] w-116 h-60 md:top-20 md:left-auto md:right-20 md:w-155 md:h-80 header-tiny:opacity-0 menu-open:opacity-100!"
      title="Home | strut.ch">
      <img src="/img/logo-strut.svg" width="313" height="161" alt="{{ config('app.name') }}" class="block w-full h-auto">
    </a>
  </div>
  <nav
    id="site-nav"
    class="fixed left-0 -z-1 w-full min-h-full pt-[calc(var(--spacing-header)-27px)] pb-20 overflow-y-auto bg-white opacity-0 pointer-events-none transition-opacity duration-0 ease-in
      menu-open:z-10 menu-open:h-full menu-open:opacity-100 menu-open:pointer-events-auto menu-open:duration-160
      md:relative md:top-0 md:z-10 md:h-40 md:min-h-auto md:pt-0 md:pb-0 md:overflow-visible md:opacity-100 md:pointer-events-auto md:menu-open:h-40"
    aria-label="Hauptnavigation"
    data-menu>
    <div class="relative mx-auto max-w-page-xs px-10 sm:max-w-none md:px-20 lg:max-w-page-lg">
      <ul class="md:inline-block md:overflow-hidden md:align-top">
        <x-site.nav.item top>
          <x-site.nav.toggle parent :active="$nav['projects']['active']">{{ $nav['projects']['label'] }}</x-site.nav.toggle>
          <x-site.nav.list top :current="$nav['projects']['active']">
            @foreach ($nav['projects']['categories'] as $category)
              <x-site.nav.item>
                <x-site.nav.toggle :active="$category['active']">{{ $category['label'] }}</x-site.nav.toggle>
                @if ($category['show_types'])
                  <x-site.nav.list :current="$category['active']">
                    @foreach ($category['types'] as $type)
                      <x-site.nav.item>
                        <x-site.nav.toggle size="subnav" :active="$type['active']">{{ $type['label'] }}</x-site.nav.toggle>
                        <x-site.nav.list indent flush :current="$type['active']">
                          @foreach ($type['projects'] as $project)
                            <x-site.nav.item>
                              <x-site.nav.link size="subnav" :href="$project['url']" :active="$project['active']" :title="$project['label']">{{ $project['label'] }}</x-site.nav.link>
                            </x-site.nav.item>
                          @endforeach
                        </x-site.nav.list>
                      </x-site.nav.item>
                    @endforeach
                  </x-site.nav.list>
                @else
                  {{-- Legacy: the toggle opens only the list right after it (the first type). --}}
                  @foreach ($category['types'] as $type)
                    <x-site.nav.list indent :current="$category['active'] && $loop->first">
                      @foreach ($type['projects'] as $project)
                        <x-site.nav.item>
                          <x-site.nav.link size="subnav" :href="$project['url']" :active="$project['active']" :title="$project['label']">{{ $project['label'] }}</x-site.nav.link>
                        </x-site.nav.item>
                      @endforeach
                    </x-site.nav.list>
                  @endforeach
                @endif
              </x-site.nav.item>
            @endforeach
          </x-site.nav.list>
        </x-site.nav.item>
        <x-site.nav.item top>
          <x-site.nav.link :href="route($nav['works']['route'])" :active="$nav['works']['active']" :title="$nav['works']['label']">{{ $nav['works']['label'] }}</x-site.nav.link>
        </x-site.nav.item>
        @foreach (['publications', 'about'] as $key)
          <x-site.nav.item top>
            <x-site.nav.toggle parent :active="$nav[$key]['active']">{{ $nav[$key]['label'] }}</x-site.nav.toggle>
            <x-site.nav.list top :current="$nav[$key]['active']">
              @foreach ($nav[$key]['links'] as $entry)
                <x-site.nav.item>
                  <x-site.nav.link :href="route($entry['route'])" :active="$entry['active']" :title="$entry['label']">{{ $entry['label'] }}</x-site.nav.link>
                </x-site.nav.item>
              @endforeach
            </x-site.nav.list>
          </x-site.nav.item>
        @endforeach
        <x-site.nav.item top>
          <x-site.nav.link :href="route($nav['contact']['route'])" :active="$nav['contact']['active']" :title="$nav['contact']['label']">{{ $nav['contact']['label'] }}</x-site.nav.link>
        </x-site.nav.item>
      </ul>
    </div>
  </nav>
</header>
