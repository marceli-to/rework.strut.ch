{{-- Block with a rule on top and the larger text size (legacy .content article). --}}
@props(['as' => 'article'])
<{{ $as }} {{ $attributes->class('border-t border-black pt-5 md:pt-7 text-xl leading-[1.21] sm:text-md md:text-4xl md:leading-[1.129]') }}>{{ $slot }}</{{ $as }}>
