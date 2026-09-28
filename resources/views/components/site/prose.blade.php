{{--
  Rich text from the admin: links get the green hover underline of x-site.link.
  `large`: underline offset for the larger text size (legacy %anchor-underline-lg).
--}}
@props(['as' => 'div', 'large' => false])
<{{ $as }} {{ $attributes->class([
  '[&_a]:inline [&_a]:text-green [&_a]:no-underline [&_a]:bg-repeat-x [&_a]:bg-size-[100%_1px] [&_a:hover]:bg-[linear-gradient(currentColor,currentColor)]',
  '[&_a]:bg-position-[0_18px] md:[&_a]:bg-position-[0_24px]' => ! $large,
  '[&_a]:bg-position-[0_20px] md:[&_a]:bg-position-[0_34px]' => $large,
]) }}>{{ $slot }}</{{ $as }}>
