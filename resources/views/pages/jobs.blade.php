{{-- Jobs: listings (or the page text) | page images. --}}
<x-layout.site title="Jobs" :$page>
  <section>
    <div class="grid gap-24 sm:grid-cols-[1fr_2fr]">
      <div>
        <x-site.heading>Jobs</x-site.heading>
        @forelse ($jobs as $job)
          <x-site.article class="[word-break:break-word] hyphens-auto [article+&]:mt-24 md:[article+&]:mt-48">
            <h2 class="mb-16 md:mb-32">{{ $job->title }}</h2>
            <p class="mb-16 md:mb-32">{{ $job->lead }}</p>
            <x-site.prose class="text-sm leading-[1.13] sm:text-xs sm:leading-[1.2] md:text-2xl">
              {!! $job->info !!}
              @if ($file = $job->files->first())
                <br>
                <a href="{{ $file->url() }}" target="_blank" aria-label="Download Stellenausschreibung">Download Stellenausschreibung</a>
              @endif
            </x-site.prose>
          </x-site.article>
        @empty
          <x-site.article class="[word-break:break-word] hyphens-auto">{!! $page->text !!}</x-site.article>
        @endforelse
      </div>
      <div class="sm:mt-18 md:mt-27">
        <x-site.page-images :images="$page->images" :alt="config('app.name') . ' - Jobs'" :gallery="$page->images->count() > 1" />
      </div>
    </div>
  </section>
</x-layout.site>
