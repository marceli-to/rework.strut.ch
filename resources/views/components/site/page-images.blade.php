{{--
  Images of a content page (Über uns, Jobs), each opening the large version
  in the lightbox; a gallery when there is more than one.
--}}
@props(['images', 'alt'])
@foreach ($images as $image)
  <figure class="[figure+&]:mt-16 md:[figure+&]:mt-24">
    <a href="{{ $image->imageUrl('lg') }}" data-lightbox="{{ $images->count() > 1 ? 'gallery' : 'single' }}">
      <img src="{{ $image->imageUrl('md') }}" width="960" height="650" alt="{{ $image->alt ?: $alt }}" class="block w-full h-auto">
    </a>
  </figure>
@endforeach
