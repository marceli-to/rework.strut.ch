{{--
  List item with a dash in front, or an arrow when the item links somewhere
  (`link`). Title in medium, text below.
--}}
@props(['link' => false])
<div {{ $attributes->class([
  'relative mb-16 last:mb-0 pl-24 md:pl-32 [word-break:break-word] hyphens-auto',
  'before:absolute before:left-0 before:block',
  'before:top-0 before:w-16 before:h-16 before:bg-[url(/img/icons/arrow-link.svg)] before:bg-no-repeat before:bg-contain before:bg-position-[left_top_3px] md:before:w-22 md:before:h-22 md:before:bg-position-[left_top_4px]' => $link,
  'before:top-7 before:w-14 before:h-1 before:bg-[#1d1d1b] md:before:top-10 md:before:w-20 md:before:h-2' => ! $link,
]) }}>{{ $slot }}</div>
