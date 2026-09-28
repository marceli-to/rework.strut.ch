{{--
  Link that opens the large image in the lightbox (modules/lightbox.js).
  The href is the original format (works without JS); the lightbox takes
  the AVIF/WebP version from data-avif / data-webp when the browser can.
  `mode`: single | gallery.
--}}
@props(['media', 'mode' => 'single'])
<a
  href="{{ $media->imageUrl('lg') }}"
  data-lightbox="{{ $mode }}"
  @foreach (\App\Support\ImageSupport::modernFormats() as $format)
    data-{{ $format }}="{{ $media->imageUrl('lg', $format) }}"
  @endforeach
  {{ $attributes }}>{{ $slot }}</a>
