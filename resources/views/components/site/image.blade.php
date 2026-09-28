{{--
  Upload image at a legacy size (Media::SIZES) as <picture>: AVIF and WebP
  where the server can write them, the original format as fallback. The
  <picture> box is display: contents, so the <img> lays out as before.
  Lazy by default; pass loading="eager" for images at the top of a page and
  in the masonry (it lays out once all its images have loaded).
--}}
@props(['media', 'size'])
<picture class="contents">
  @foreach (\App\Support\ImageSupport::modernFormats() as $format)
    <source type="image/{{ $format }}" srcset="{{ $media->imageUrl($size, $format) }}">
  @endforeach
  <img src="{{ $media->imageUrl($size) }}" {{ $attributes->merge(['loading' => 'lazy', 'decoding' => 'async']) }}>
</picture>
