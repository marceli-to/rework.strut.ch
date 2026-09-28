{{--
  Nav entry with the legacy underline: a 2px background line at a fixed offset,
  shown on hover and when active. Without href it renders the label of a toggle
  (a <span>; the font size then sits on the button).
--}}
@props(['href' => null, 'size' => 'nav', 'active' => false])
@php $tag = $href ? 'a' : 'span'; @endphp
<{{ $tag }}
  @if ($href) href="{{ $href }}" @endif
  @if ($active) data-active @if ($href) aria-current="page" @endif @endif
  {{ $attributes->class([
    'inline text-black no-underline bg-repeat-x bg-size-[100%_2px] hover:bg-[linear-gradient(currentColor,currentColor)] data-active:bg-[linear-gradient(currentColor,currentColor)]',
    $size === 'nav' ? 'bg-position-[0_26px]' : 'bg-position-[0_22px]',
    'text-3xl leading-[1.29] sm:leading-[1.25]' => $href && $size === 'nav',
    'text-lg leading-[1.2]' => $href && $size === 'subnav',
  ]) }}>{{ $slot }}</{{ $tag }}>
