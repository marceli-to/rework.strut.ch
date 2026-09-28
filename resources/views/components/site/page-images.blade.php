{{--
  Images of a content page (Über uns, Jobs), each opening the large version
  in the lightbox; `gallery`: browse through all of them.
--}}
@props(['images', 'alt', 'gallery' => false])
@foreach ($images as $image)
  <figure class="[figure+&]:mt-16 md:[figure+&]:mt-24">
    <a href="{{ $image->imageUrl('lg') }}" data-lightbox="{{ $gallery ? 'gallery' : 'single' }}">
      <img src="{{ $image->imageUrl('md') }}" width="960" height="650" alt="{{ $image->alt ?: $alt }}" class="block w-full h-auto">
    </a>
  </figure>
@endforeach
