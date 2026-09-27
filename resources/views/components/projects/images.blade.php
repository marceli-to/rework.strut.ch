@php
  $images = $project->media->filter(fn ($m) => $m->isImage() && $m->variant !== 'mobile');
@endphp

@if($images->count())

  <div
    data-slides="project-{{ $project->id }}"
    class="relative h-full hidden md:block md:px-56 xl:px-0">
    <x-swiper.container class="h-full">
      @foreach($images as $image)
        <x-swiper.image :media="$image" fit="max" preset="image_slide_wide" :show-caption="true" />
      @endforeach
    </x-swiper.container>
    <x-swiper.navigation :id="'project-' . $project->id" layout="sides" />
  </div>

  <div class="md:hidden flex flex-col gap-y-18">
    @foreach($images as $image)
      <div>
        <x-media.image :media="$image" fit="max" sizes="100vw" class="w-full h-auto" />
        @if($image->caption)
          <div class="{{ $loop->last ? 'py-18' : 'pt-18' }} px-16 text-md text-center">{{ $image->caption }}</div>
        @endif
      </div>
    @endforeach
  </div>

@endif