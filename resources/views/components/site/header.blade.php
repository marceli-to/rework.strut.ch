{{--
  Header and main navigation. $nav comes from GetNavigation (view composer).
  States set by JS: header .is-tiny/.is-hidden (modules/header.js),
  html.has-menu, nav.is-visible, ul[data-open] (modules/menu.js).
  ul[data-current] marks lists of the current page: open on mobile (top level)
  or always (inside "Bauten").
--}}
@php
  $sub = 'hidden data-open:block md:absolute md:left-auto';
  $open = 'max-md:data-open:mb-16 max-md:in-data-open:mb-16';
  $item = 'block md:inline-block md:mr-30';
  $subItem = 'block md:mr-0 md:w-full';
  $toggle = 'type-nav cursor-pointer text-left';
  $link = 'type-nav link-nav';
  $leaf = 'type-subnav link-nav link-subnav';
  $indent = 'pl-25 [&>li]:py-3 md:[&>li]:py-0';
@endphp
<header
  class="fixed z-5 w-full h-90 md:h-170 bg-white
    [&.is-tiny]:h-45 [&.is-tiny]:bg-transparent
    md:[&.is-tiny]:h-70 md:[&.is-tiny]:bg-white md:[&.is-tiny]:translate-y-0 md:[&.is-tiny]:transition-transform md:[&.is-tiny]:duration-360 md:[&.is-tiny]:delay-240 md:[&.is-tiny]:ease-out
    md:[&.is-hidden]:-translate-y-full md:[&.is-hidden]:transition-transform md:[&.is-hidden]:duration-320 md:[&.is-hidden]:ease-out
    in-[.has-menu]:h-full! in-[.has-menu]:bg-white!"
  data-header>
  <div class="page-block z-20 md:pt-20">
    <button
      type="button"
      class="block md:hidden relative top-10 z-1 w-42 h-27 cursor-pointer bg-no-repeat bg-top-left bg-[url(/img/icons/menu-burger.svg)] bg-size-[42px_23px] aria-expanded:bg-[url(/img/icons/menu-cross.svg)] aria-expanded:bg-size-[27px_27px]"
      aria-expanded="false"
      aria-controls="site-nav"
      aria-label="Menü anzeigen"
      data-menu-button></button>
    <a
      href="/"
      class="block absolute z-15 top-10 left-[calc(100%-126px)] w-116 h-60 md:top-20 md:left-auto md:right-20 md:w-155 md:h-80 in-[.is-tiny]:opacity-0 in-[.has-menu]:opacity-100!"
      title="Home | strut.ch">
      <img src="/img/logo-strut.svg" width="313" height="161" alt="{{ config('app.name') }}" class="block w-full h-auto">
    </a>
  </div>
  <nav
    id="site-nav"
    class="fixed left-0 -z-1 w-full min-h-full pt-63 pb-20 overflow-y-auto bg-white opacity-0 pointer-events-none transition-opacity duration-0 ease-in
      [&.is-visible]:z-10 [&.is-visible]:h-full [&.is-visible]:opacity-100 [&.is-visible]:pointer-events-auto [&.is-visible]:duration-160
      md:relative md:top-0 md:z-10 md:h-40 md:min-h-auto md:pt-0 md:pb-0 md:overflow-visible md:opacity-100 md:pointer-events-auto md:[&.is-visible]:h-40"
    aria-label="Hauptnavigation"
    data-menu>
    <div class="page-block">
      <ul class="md:inline-block md:overflow-hidden md:align-top">
        <li class="{{ $item }}">
          <button type="button" class="{{ $toggle }}" aria-expanded="false" data-submenu-button data-submenu-parent><span class="link-nav" @if ($nav['projects']['active']) data-active @endif>{{ $nav['projects']['label'] }}</span></button>
          <ul class="{{ $sub }} {{ $open }} max-md:data-current:block" @if ($nav['projects']['active']) data-current @endif>
            @foreach ($nav['projects']['categories'] as $category)
              <li class="{{ $subItem }}">
                <button type="button" class="{{ $toggle }}" aria-expanded="false" data-submenu-button><span class="link-nav" @if ($category['active']) data-active @endif>{{ $category['label'] }}</span></button>
                @if ($category['show_types'])
                  <ul class="hidden data-open:block data-current:block {{ $open }} md:relative" @if ($category['active']) data-current @endif>
                    @foreach ($category['types'] as $type)
                      <li class="{{ $subItem }}">
                        <button type="button" class="{{ str_replace('type-nav', 'type-subnav', $toggle) }}" aria-expanded="false" data-submenu-button><span class="link-nav link-subnav" @if ($type['active']) data-active @endif>{{ $type['label'] }}</span></button>
                        <ul class="hidden data-open:block data-current:block {{ $indent }} mb-0! md:relative" @if ($type['active']) data-current @endif>
                          @foreach ($type['projects'] as $project)
                            <li class="{{ $subItem }}">
                              <a href="{{ $project['url'] }}" class="{{ $leaf }}" title="{{ $project['label'] }}" @if ($project['active']) data-active aria-current="page" @endif>{{ $project['label'] }}</a>
                            </li>
                          @endforeach
                        </ul>
                      </li>
                    @endforeach
                  </ul>
                @else
                  @foreach ($category['types'] as $type)
                    <ul class="hidden data-open:block data-current:block {{ $indent }} {{ $open }} md:relative" @if ($category['active'] && $loop->first) data-current @endif>
                      @foreach ($type['projects'] as $project)
                        <li class="{{ $subItem }}">
                          <a href="{{ $project['url'] }}" class="{{ $leaf }}" title="{{ $project['label'] }}" @if ($project['active']) data-active aria-current="page" @endif>{{ $project['label'] }}</a>
                        </li>
                      @endforeach
                    </ul>
                  @endforeach
                @endif
              </li>
            @endforeach
          </ul>
        </li>
        <li class="{{ $item }}">
          <a href="{{ route($nav['works']['route']) }}" class="{{ $link }}" title="{{ $nav['works']['label'] }}" @if ($nav['works']['active']) data-active aria-current="page" @endif>{{ $nav['works']['label'] }}</a>
        </li>
        @foreach (['publications', 'about'] as $key)
          <li class="{{ $item }}">
            <button type="button" class="{{ $toggle }}" aria-expanded="false" data-submenu-button data-submenu-parent><span class="link-nav" @if ($nav[$key]['active']) data-active @endif>{{ $nav[$key]['label'] }}</span></button>
            <ul class="{{ $sub }} {{ $open }} max-md:data-current:block" @if ($nav[$key]['active']) data-current @endif>
              @foreach ($nav[$key]['links'] as $entry)
                <li class="{{ $subItem }}">
                  <a href="{{ route($entry['route']) }}" class="{{ $link }}" title="{{ $entry['label'] }}" @if ($entry['active']) data-active aria-current="page" @endif>{{ $entry['label'] }}</a>
                </li>
              @endforeach
            </ul>
          </li>
        @endforeach
        <li class="{{ $item }}">
          <a href="{{ route($nav['contact']['route']) }}" class="{{ $link }}" title="{{ $nav['contact']['label'] }}" @if ($nav['contact']['active']) data-active aria-current="page" @endif>{{ $nav['contact']['label'] }}</a>
        </li>
      </ul>
    </div>
  </nav>
</header>
