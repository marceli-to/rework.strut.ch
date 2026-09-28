{{--
  Group heading above cards (year, status …), with a rule on top.
  `dense`: Werkliste spacing below the heading.
--}}
@props(['dense' => false])
<h2 {{ $attributes->class([
  'text-xl leading-none! sm:text-md md:text-4xl border-t border-black pt-5 md:pt-7 not-first:mt-16 md:not-first:mt-24',
  'mb-8 md:mb-16' => ! $dense,
  'mb-8 sm:mb-16' => $dense,
]) }}>{{ $slot }}</h2>
