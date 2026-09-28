{{--
  Block with a rule on top (legacy %article). `large`: the larger text size
  of the legacy .content article; off where the page keeps the body size.
--}}
@props(['as' => 'article', 'large' => true])
<{{ $as }} {{ $attributes->class([
  'border-t border-black pt-5 md:pt-7',
  'text-xl leading-[1.21] sm:text-md md:text-4xl md:leading-[1.129]' => $large,
]) }}>{{ $slot }}</{{ $as }}>
