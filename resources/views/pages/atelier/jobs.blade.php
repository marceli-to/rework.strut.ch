<x-layout.site :description="$seo?->jobs_meta_description" :og-image="$page?->og_image" title="Jobs">
  <div class="h-full md:px-16 xl:px-32">
    <x-grid.container class="h-full">

      <x-grid.span class="md:col-span-7 xl:col-span-8 md:-ml-16 xl:-ml-32 md:min-h-0">
        @if($page?->media->count() > 1)
          <div data-slides="atelier-jobs" data-slides-autoplay="4000" class="h-full">
            <x-swiper.container class="h-full">
              @foreach($page->media as $image)
                <x-swiper.item>
                  <x-media.image :media="$image" sizes="(min-width: 768px) 66vw, 100vw" class="aspect-[4/3] md:aspect-auto w-full h-full object-cover" />
                </x-swiper.item>
              @endforeach
            </x-swiper.container>
          </div>
        @elseif($page?->media->count())
          <x-media.image :media="$page->media" sizes="(min-width: 768px) 66vw, 100vw" class="aspect-[4/3] md:aspect-auto w-full h-full object-cover" />
        @endif
      </x-grid.span>

      <x-grid.span class="md:col-span-5 xl:col-span-4 px-16 md:pl-0 md:pr-16 xl:pr-32 md:-mr-16 xl:-mr-32 min-h-0 overflow-auto">
        <div class="py-18 flex flex-col gap-y-48">
          @foreach($jobs as $job)
            <article class="hyphens-auto">
              <h2 class="text-2xl leading-[1.174] mb-16">
                {!! $job->title !!}
              </h2>
              <div class="text-lg leading-[1.33]">
                {!! $job->text !!}
              </div>
            </article>
          @endforeach
        </div>
      </x-grid.span>

    </x-grid.container>
  </div>
  
  <x-slot:footer>
    <x-menu.pages.atelier.container />
  </x-slot>
  
</x-layout.site>
