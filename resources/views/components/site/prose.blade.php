{{--
  Rich text from the admin: its links (no class, unlike component links) get the green hover underline of x-site.link.
  `large`: underline offset for the larger text size (legacy %anchor-underline-lg).
--}}
@props(['as' => 'div', 'large' => false])
<{{ $as }} {{ $attributes->class([
  '[&_a:not([class])]:inline [&_a:not([class])]:text-green [&_a:not([class])]:no-underline [&_a:not([class])]:bg-repeat-x [&_a:not([class])]:bg-size-[100%_1px] [&_a:not([class]):hover]:bg-[linear-gradient(currentColor,currentColor)]',
  '[&_a:not([class])]:bg-position-[0_18px] md:[&_a:not([class])]:bg-position-[0_24px]' => ! $large,
  '[&_a:not([class])]:bg-position-[0_20px] md:[&_a:not([class])]:bg-position-[0_34px]' => $large,
]) }}>{{ $slot }}</{{ $as }}>
