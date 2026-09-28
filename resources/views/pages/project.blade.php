{{--
  Project detail: type + browse arrows, title, "Info" (description, info,
  downloads; an overlay from md, closes on outside click), image grid, and
  below sm a teaser for the next project.
--}}
@php $body = 'text-sm leading-[1.13] sm:text-xs sm:leading-[1.2] md:text-2xl'; @endphp
<x-layout.site :title="$project->name . ', ' . $project->location . ' - ' . $project->categoryType->name_singular" :description="$project->meta_description" :og-image="$project->ogImage()" :canonical="url($project->url)">
  <section>
    <header class="relative">
      <div>
        <x-site.heading as="h2">{{ $project->categoryType->name_singular }}</x-site.heading>
        @if ($prev && $next)
          <nav class="group hidden absolute right-0 top-0 sm:block" aria-label="Projekte blättern">
            <span class="hidden pr-8 group-has-[[data-prev]:hover]:inline-block">Vorheriges Projekt</span>
            <span class="hidden pr-8 group-has-[[data-next]:hover]:inline-block">Nächstes Projekt</span>
            <a href="{{ $prev->url }}" class="inline-block w-22 h-13 mr-4 text-[0px]! bg-no-repeat bg-contain bg-center bg-[url(/img/icons/arrow-prev_sm.svg)]" aria-label="Vorheriges Projekt: {{ $prev->name }}, {{ $prev->location }}" data-prev></a>
            <a href="{{ $next->url }}" class="inline-block w-22 h-13 ml-4 text-[0px]! bg-no-repeat bg-contain bg-center bg-[url(/img/icons/arrow-next_sm.svg)]" aria-label="Nächstes Projekt: {{ $next->name }}, {{ $next->location }}" data-next></a>
          </nav>
        @endif
      </div>
    </header>
    <x-site.article :large="false" class="relative">
      <button
        type="button"
        class="hidden absolute right-0 top-6 cursor-pointer text-green text-xl leading-none! sm:text-md md:block md:text-4xl aria-expanded:size-27 aria-expanded:bg-no-repeat aria-expanded:bg-size-[27px_27px] aria-expanded:bg-[url(/img/icons/cross.svg)]"
        title="Projektbeschreibung anzeigen"
        aria-expanded="false"
        aria-controls="project-info"
        data-toggle="open"
        data-toggle-dismiss><span class="in-aria-expanded:hidden">Info</span></button>
      <h1 class="text-xl leading-none! sm:text-md md:text-4xl mb-12 md:mb-24">{{ $project->name }}, {{ $project->location }}</h1>
      <div class="mb-24 md:mb-0">
        @foreach ($rows as $row)
          <x-site.grid.project :columns="$row['columns']" />
        @endforeach
      </div>
      <div
        id="project-info"
        class="{{ $body }} md:absolute md:left-0 md:top-60 md:w-full md:pb-16 md:bg-white md:opacity-0 md:-z-1 md:data-open:opacity-100 md:data-open:z-1">
        <div class="grid gap-24 sm:grid-cols-[repeat(2,1fr)]">
          <x-site.prose class="[word-break:break-word] hyphens-auto">{!! $project->description !!}</x-site.prose>
          <x-site.prose>
            {!! $project->info !!}
            <p>
              @foreach ($project->files as $file)
                <x-site.file-link :href="$file->url()" target="_blank" title="Download Projektdokumentation">{{ $project->name }}, {{ $project->location }}</x-site.file-link>
              @endforeach
            </p>
          </x-site.prose>
        </div>
      </div>
      @if ($next)
        <div class="mt-32 sm:hidden">
          <x-site.article :large="false" class="pt-4!">
            <a href="{{ $next->url }}" class="text-green no-underline" title="Nächstes Projekt">
              <span>Nächstes Projekt</span>
              <h3 class="mt-4 text-black text-xl leading-[1.21] sm:text-md">{{ $next->name }}, {{ $next->location }}</h3>
              @if ($image = $next->images->first())
                <figure class="block mt-4">
                  <x-site.image :media="$image" size="sm" width="900" height="500" :alt="$next->name . ', ' . $next->location" class="block w-full h-auto max-w-[70%]" />
                </figure>
              @endif
            </a>
          </x-site.article>
        </div>
      @endif
    </x-site.article>
  </section>
</x-layout.site>
