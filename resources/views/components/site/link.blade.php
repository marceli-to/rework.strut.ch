{{-- Green text link, underlined on hover (legacy %anchor-underline). --}}
<a {{ $attributes->class('inline text-green no-underline bg-repeat-x bg-size-[100%_1px] bg-position-[0_18px] md:bg-position-[0_24px] hover:bg-[linear-gradient(currentColor,currentColor)]') }}>{{ $slot }}</a>
