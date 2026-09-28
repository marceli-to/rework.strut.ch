{{--
  Green toggle with a chevron (legacy .icon-toggle), for content that follows
  it (modules/toggle.js). The chevron sits on an inline span so it is placed
  like on the legacy inline <a>; the label is underlined while hovered itself.
--}}
@props(['controls'])
<button
  type="button"
  aria-expanded="false"
  aria-controls="{{ $controls }}"
  data-toggle
  {{ $attributes->class('group cursor-pointer text-left text-green') }}><span class="inline pr-24 bg-no-repeat bg-[url(/img/icons/chevron-down-green.svg)] bg-size-[16px_auto] bg-position-[right_top_4px] group-aria-expanded:bg-[url(/img/icons/chevron-up-green.svg)] md:pr-32 md:bg-size-[24px_auto] md:bg-position-[right_top_5px]"><span class="inline bg-repeat-x bg-size-[100%_1px] bg-position-[0_18px] md:bg-position-[0_24px] hover:bg-[linear-gradient(currentColor,currentColor)]">{{ $slot }}</span></span></button>
