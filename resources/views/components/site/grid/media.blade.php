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
  <x-site.zoom-link :$media mode="gallery" data-caption="{{ $media->caption }}">
    <x-site.image
      :$media
      :size="$direct && $size === 'lg' ? 'lg' : 'md'"
      width="687"
      :height="$size === 'lg' ? 940 : 458"
      :alt="$media->alt ?: $media->caption"
      class="block w-full h-full object-cover" />
  </x-site.zoom-link>
@endif
