{{-- News tile of the homepage grid (legacy partials/news), centred in its box. --}}
@props(['news'])
@php $spacing = 'mb-8 sm:mb-4 md:mb-8'; @endphp
<article class="absolute top-0 left-0 w-[calc(100%-24px)] h-full py-5 text-center border-y border-black sm:h-[calc(100%-24px)]">
  @if ($news->date_label)
    <div>{{ $news->date_label }}</div>
  @endif
  <div @class([
    'absolute top-1/2 w-full h-auto p-20',
    '-translate-y-1/2' => ! $news->date_label,
    'translate-y-[calc(-50%+10px)] sm:translate-y-[calc(-50%+9px)] md:translate-y-[calc(-50%+18px)] lg:translate-y-[calc(-50%+13px)]' => $news->date_label,
  ])>
    <h2 class="{{ $spacing }} text-5xl leading-[1.05] sm:text-md sm:leading-[1.21] md:text-4xl md:leading-[1.129] lg:text-6xl lg:leading-[1.03]">{{ $news->title }}</h2>
    @if ($news->subtitle)
      <p class="{{ $spacing }} text-xl leading-[1.21] sm:text-xs sm:leading-[1.2] md:text-2xl lg:text-4xl lg:leading-[1.129]">{{ $news->subtitle }}</p>
    @endif
    @if ($news->text)
      <p class="{{ $spacing }}">{{ $news->text }}</p>
    @endif
    @if ($image = $news->images->first())
      <figure class="my-16 md:my-24">
        <img src="{{ $image->imageUrl('xs') }}" width="500" height="350" alt="{{ $image->alt ?: $news->title }}" class="block w-full h-auto max-w-[70%] mx-auto">
      </figure>
    @endif
    @if ($news->link_url && $news->link_label)
      <p class="{{ $spacing }}">
        <x-site.link :href="$news->link_url" :target="str_contains($news->link_url, 'strut.ch') ? null : '_blank'">{{ $news->link_label }}</x-site.link>
      </p>
    @endif
  </div>
</article>
