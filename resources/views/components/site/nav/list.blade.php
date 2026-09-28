{{--
  Submenu list, hidden until opened (data-open, set by modules/menu.js).
  `top`: dropdown of a section; `current` = the section of the current page,
  open on mobile only. Nested lists (inside "Bauten") with `current` stay open.
  `indent`: project list. `flush`: no bottom margin when open.
--}}
@props(['top' => false, 'current' => false, 'indent' => false, 'flush' => false])
<ul
  @if ($current) data-current @endif
  {{ $attributes->class([
    'hidden data-open:block',
    'max-md:data-current:block md:absolute md:left-auto' => $top,
    'data-current:block md:relative' => ! $top,
    'pl-25 [&>li]:py-3 md:[&>li]:py-0' => $indent,
    $flush ? 'mb-0!' : 'max-md:data-open:mb-16 max-md:in-data-open:mb-16',
  ]) }}>{{ $slot }}</ul>
