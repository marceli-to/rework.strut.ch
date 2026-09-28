{{-- Page title (legacy %heading-sm: .content h1, project type). --}}
@props(['as' => 'h1'])
<{{ $as }} {{ $attributes->class('text-sm leading-[1.25] sm:text-xs md:text-2xl md:mb-2') }}>{{ $slot }}</{{ $as }}>
