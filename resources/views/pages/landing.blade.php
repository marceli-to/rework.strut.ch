<x-layout.site :description="$seo?->landing_meta_description">
  <div data-slides="landing" class="h-[calc(100dvh_-_var(--header-height-md)_-_var(--footer-height))] xl:h-[calc(100dvh_-_var(--header-height-lg)_-_var(--footer-height))]">
    <x-swiper.container class="h-full">
      @foreach($slides as $slide)
        @if($slide->type === 'image' && $slide->media->count())
          @if($slide->link_url)
            <x-swiper.item>
              <a href="{{ $slide->link_url }}" class="block h-full group" aria-label="{{ $slide->media->first()?->alt ?: 'Projekt ansehen' }}">
                <div class="h-full flex items-center justify-center overflow-hidden [&>picture]:block [&>picture]:h-full">
                  <x-media.image :media="$slide->media" fit="crop" sizes="100vw" class="h-full w-auto max-w-none group-hover:brightness-90 transition-all duration-300" />
                </div>
              </a>
            </x-swiper.item>
          @else
            <x-swiper.image :media="$slide->media" />
          @endif
        @elseif($slide->type === 'image_text')
          <x-swiper.item class="md:px-16 xl:px-32">
            @if($slide->link_url)
              <a href="{{ $slide->link_url }}" class="block h-full overflow-y-auto md:overflow-visible hover:text-accent group">
                <div class="h-full flex flex-col md:grid md:grid-cols-12 md:gap-x-30">
                  @if($slide->media->count())
                    <div class="shrink-0 md:col-span-8 md:-ml-16 xl:-ml-32 md:h-full">
                      <x-media.image :media="$slide->media" sizes="(min-width: 768px) 50vw, 100vw" class="w-full h-auto md:aspect-auto md:h-full md:object-cover group-hover:brightness-90 transition-all duration-300" />
                    </div>
                  @endif
                  @if($slide->text)
                    <div class="shrink-0 px-16 md:col-span-4 md:px-0">
                      <article class="hyphens-auto text-2xl leading-[1.174] py-18">
                        {!! $slide->text !!}
                      </article>
                    </div>
                  @endif
                </div>
              </a>
            @else
              <div class="h-full overflow-y-auto flex flex-col md:overflow-visible md:grid md:grid-cols-12 md:gap-x-30">
                @if($slide->media->count())
                  <div class="shrink-0 md:col-span-8 md:-ml-16 xl:-ml-32 md:h-full">
                    <x-media.image :media="$slide->media" sizes="(min-width: 768px) 50vw, 100vw" class="w-full h-auto md:aspect-auto md:h-full md:object-cover" />
                  </div>
                @endif
                @if($slide->text)
                  <div class="shrink-0 px-16 md:col-span-4 md:px-0">
                    <article class="hyphens-auto text-2xl leading-[1.174] py-18">
                      {!! $slide->text !!}
                    </article>
                  </div>
                @endif
              </div>
            @endif
          </x-swiper.item>
        @endif
      @endforeach
    </x-swiper.container>
  </div>

  <x-slot:footer>
    <x-swiper.navigation />
  </x-slot>
</x-layout.site>
