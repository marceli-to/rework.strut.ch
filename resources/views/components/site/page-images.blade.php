{{--
  Images of a content page (Über uns, Jobs), each opening the large version
  in the lightbox; `gallery`: browse through all of them.
--}}
@props(['images', 'alt', 'gallery' => false])
@foreach ($images as $image)
  <figure class="[figure+&]:mt-16 md:[figure+&]:mt-24">
    <x-site.zoom-link :media="$image" :mode="$gallery ? 'gallery' : 'single'">
      <x-site.image :media="$image" size="md" width="960" height="650" :alt="$image->alt ?: $alt" class="block w-full h-auto" />
    </x-site.zoom-link>
  </figure>
@endforeach
