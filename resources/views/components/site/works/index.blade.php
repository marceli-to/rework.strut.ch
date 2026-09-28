{{-- Werkliste page: view tabs, PDF link and the columns ($slot). --}}
@props(['by', 'page'])
@php
  $views = ['status' => ['Status', 'page.works.status', 'status'], 'year' => ['Jahr', 'page.works.year', 'jahr'], 'type' => ['Typ', 'page.works.type', 'typ']];
@endphp
<x-layout.site title="Werkliste" :description="$page->meta_description">
  <section>
    <nav class="sm:grid sm:grid-cols-2 sm:gap-24 md:grid-cols-[2fr_1fr]" aria-label="Werkliste">
      <div class="mb-8 sm:mb-0">
        <ul>
          @foreach ($views as $key => [$label, $route])
            <li class="inline-block mr-16">
              <a href="{{ route($route) }}" @if ($key === $by) data-active aria-current="page" @endif class="inline-block pr-10 text-black bg-no-repeat transition-[background-image] duration-120 ease-out bg-position-[right_1px_top_7px] bg-size-[4px_auto] hover:bg-[url(/img/icons/dot.svg)] data-active:bg-[url(/img/icons/dot.svg)] md:mb-2 md:mr-24 md:pr-12 md:bg-position-[right_0_top_10px] md:bg-size-[5px_auto]">{{ $label }}</a>
            </li>
          @endforeach
        </ul>
      </div>
      <div class="mb-8 sm:mb-0 md:pl-8">
        <x-site.file-link :href="route('pdf.works', $views[$by][2])" target="_blank">Werkliste nach {{ $views[$by][0] }}</x-site.file-link>
      </div>
    </nav>
    <div class="grid sm:grid-cols-2 sm:gap-x-24 md:grid-cols-3">{{ $slot }}</div>
  </section>
</x-layout.site>
