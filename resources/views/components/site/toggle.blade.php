{{--
  Green toggle with a chevron (legacy .icon-toggle), for content that follows
  it (modules/toggle.js). The chevron sits on an inline span so it is placed
  like on the legacy inline <a>; the label is underlined while hovered itself.
  `reverse`: chevron on the left (the right padding stays, as in legacy).
--}}
@props(['controls', 'reverse' => false])
<button
  type="button"
  aria-expanded="false"
  aria-controls="{{ $controls }}"
  data-toggle
  {{ $attributes->class('group cursor-pointer text-left text-green') }}><span @class([
    'inline pr-24 bg-no-repeat bg-[url(/img/icons/chevron-down-green.svg)] bg-size-[16px_auto] group-aria-expanded:bg-[url(/img/icons/chevron-up-green.svg)] md:pr-32 md:bg-size-[24px_auto]',
    'bg-position-[right_top_4px] md:bg-position-[right_top_5px]' => ! $reverse,
    'pl-24 bg-position-[left_top_4px] md:pl-32 md:bg-position-[left_top_5px]' => $reverse,
  ])><span class="inline bg-repeat-x bg-size-[100%_1px] bg-position-[0_18px] md:bg-position-[0_24px] hover:bg-[linear-gradient(currentColor,currentColor)]">{{ $slot }}</span></span></button>
