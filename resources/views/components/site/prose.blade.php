{{-- Rich text from the admin: links get the green hover underline of x-site.link. --}}
@props(['as' => 'div'])
<{{ $as }} {{ $attributes->class('[&_a]:inline [&_a]:text-green [&_a]:no-underline [&_a]:bg-repeat-x [&_a]:bg-size-[100%_1px] [&_a]:bg-position-[0_18px] md:[&_a]:bg-position-[0_24px] [&_a:hover]:bg-[linear-gradient(currentColor,currentColor)]') }}>{{ $slot }}</{{ $as }}>
