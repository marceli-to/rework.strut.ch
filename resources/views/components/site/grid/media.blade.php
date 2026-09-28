{{--
  Image (opens the lightbox gallery) or video of a project grid cell.
  Image sizes as legacy: a tall cell shown directly gets the large image,
  everything else the medium one.
--}}
@props(['item', 'size', 'direct' => false])
@php $media = $item->media; @endphp
@if ($media?->isVideo())
  <video autoplay muted loop playsinline class="block w-full h-full object-cover">
    <source src="{{ $media->url() }}">
  </video>
@elseif ($media)
  <a href="{{ $media->imageUrl('lg') }}" data-lightbox="gallery" data-caption="{{ $media->caption }}">
    <img
      src="{{ $media->imageUrl($direct && $size === 'lg' ? 'lg' : 'md') }}"
      width="687"
      height="{{ $size === 'lg' ? 940 : 458 }}"
      alt="{{ $media->alt ?: $media->caption }}"
      class="block w-full h-full object-cover">
  </a>
@endif
