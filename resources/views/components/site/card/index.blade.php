{{--
  List item with a dash in front, or an arrow when the item links somewhere
  (`link`). `dense`: Werkliste items (no gap between items, no hyphenation).
--}}
@props(['link' => false, 'dense' => false])
<div {{ $attributes->class([
  'relative pl-24 md:pl-32 text-sm leading-[1.13] sm:text-xs sm:leading-[1.2] md:text-2xl',
  'mb-16 last:mb-0 [word-break:break-word] hyphens-auto' => ! $dense,
  'mb-0 last:mb-16 [word-break:keep-all] hyphens-none' => $dense,
  'before:absolute before:left-0 before:block',
  'before:top-0 before:w-16 before:h-16 before:bg-[url(/img/icons/arrow-link.svg)] before:bg-no-repeat before:bg-contain before:bg-position-[left_top_3px] md:before:w-22 md:before:h-22 md:before:bg-position-[left_top_4px]' => $link,
  'before:top-7 before:w-14 before:h-1 before:bg-[#1d1d1b] md:before:top-10 md:before:w-20 md:before:h-2' => ! $link,
]) }}>{{ $slot }}</div>
