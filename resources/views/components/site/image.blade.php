{{--
  Upload image at a legacy size (Media::SIZES) as <picture>: AVIF and WebP
  where the server can write them, the original format as fallback. The
  <picture> box is display: contents, so the <img> lays out as before.
--}}
@props(['media', 'size'])
<picture class="contents">
  @foreach (\App\Support\ImageSupport::modernFormats() as $format)
    <source type="image/{{ $format }}" srcset="{{ $media->imageUrl($size, $format) }}">
  @endforeach
  <img src="{{ $media->imageUrl($size) }}" {{ $attributes }}>
</picture>
