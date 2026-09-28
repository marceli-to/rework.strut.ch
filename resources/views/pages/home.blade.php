{{-- Homepage: highlight slideshow (modules/slideshow.js) and the grid. --}}
<x-layout.site home :description="$page->meta_description">
  <section>
    @if ($slides->isNotEmpty())
      <figure class="relative mb-24" data-slideshow>
        <div class="swiper overflow-hidden aspect-[16/10]">
          <div class="swiper-wrapper">
            @foreach ($slides as $slide)
              @php $project = $slide->media->mediable; @endphp
              <div class="swiper-slide">
                <a href="{{ $project->url }}" title="{{ config('app.name') }} - {{ $project->title }}" class="group block relative w-full h-full">
                  <x-site.caption :$project />
                  @if ($slide->media->isVideo())
                    <video autoplay muted playsinline class="block w-full h-full object-cover">
                      <source src="{{ $slide->media->url() }}">
                    </video>
                  @else
                    <img src="{{ $slide->media->imageUrl('lg') }}" width="1600" height="1066" alt="{{ $project->name }}, {{ $project->location }}" class="block w-full h-full object-cover">
                  @endif
                </a>
              </div>
            @endforeach
          </div>
        </div>
      </figure>
    @endif
    <div class="relative overflow-x-hidden">
      <div class="relative w-[calc(100%+24px)]">
        @foreach ($rows as $row)
          <x-site.grid.home :columns="$row['columns']" />
        @endforeach
      </div>
    </div>
  </section>
</x-layout.site>
