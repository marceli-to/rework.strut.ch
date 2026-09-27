@props(['media', 'fit' => 'crop', 'preset' => 'image_slide', 'showCaption' => false, 'class' => ''])
@php
  $first = $media instanceof \App\Models\Media ? $media : $media->first();
  $orientation = $first?->orientation ?? 'unknown';
  $variants = config('swiper.' . $preset . '.orientations');
  $variant = $variants[$orientation] ?? $variants['default'];
  $caption = $showCaption ? $first?->caption : null;
@endphp
<x-swiper.item :class="$class">
  @if($caption)
    <div class="h-full flex flex-col">
      <div class="flex-1 min-h-0 flex items-center justify-center overflow-hidden [&>picture]:block [&>picture]:h-full">
        <x-media.image :media="$media" :fit="$fit" :sizes="$variant['sizes']" class="h-full w-auto max-w-none" />
      </div>
      <div class="py-20 px-16 text-md text-center max-w-xl mx-auto">{{ $caption }}</div>
    </div>
  @else
    <div class="h-full flex items-center justify-center overflow-hidden [&>picture]:block [&>picture]:h-full">
      <x-media.image :media="$media" :fit="$fit" :sizes="$variant['sizes']" class="h-full w-auto max-w-none" />
    </div>
  @endif
</x-swiper.item>
