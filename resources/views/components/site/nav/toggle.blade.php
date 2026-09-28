{{-- Opens the list that follows it (modules/menu.js). `parent` = top-level section. --}}
@props(['size' => 'nav', 'active' => false, 'parent' => false])
<button
  type="button"
  aria-expanded="false"
  data-submenu-button
  @if ($parent) data-submenu-parent @endif
  @class([
    'cursor-pointer text-left',
    'text-3xl leading-[1.29] sm:leading-[1.25]' => $size === 'nav',
    'text-lg leading-[1.2]' => $size === 'subnav',
  ])><x-site.nav.link :size="$size" :active="$active">{{ $slot }}</x-site.nav.link></button>
