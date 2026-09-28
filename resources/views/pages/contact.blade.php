{{-- Kontakt: text, Impressum and Datenschutz toggles | map. --}}
<x-layout.site title="Kontakt" :description="$contact?->meta_description">
  <section>
    @if ($contact)
      <div class="relative grid gap-24 sm:grid-cols-[1fr_2fr]">
        <div>
          <x-site.heading>{{ $contact->title }}</x-site.heading>
          <x-site.article>
            <x-site.prose large>{!! $contact->text !!}</x-site.prose>
          </x-site.article>
          @if ($imprint)
            <div class="mt-24 md:mt-48">
              <x-site.toggle controls="impressum">Impressum</x-site.toggle>
              <x-site.prose id="impressum" class="mt-16" hidden>{!! $imprint->text !!}</x-site.prose>
            </div>
          @endif
          <div class="mt-8">
            <x-site.toggle controls="datenschutz">Datenschutz</x-site.toggle>
            <x-site.prose id="datenschutz" class="mt-16 [&_ul]:list-disc [&_ul]:my-[1em] [&_ul]:pl-40" hidden>
              @include('pages.partials.privacy')
            </x-site.prose>
          </div>
        </div>
        <div class="sm:mt-18 md:mt-27">
          <div class="min-h-300 sm:min-h-400 md:min-h-500" data-map @if ($mapsKey) data-map-key="{{ $mapsKey }}" @endif></div>
          <div class="mt-4 sm:mt-16">
            <x-site.link href="https://goo.gl/maps/iP116gayDdwGiKFm7" target="_blank" rel="noopener">Auf Google Maps anzeigen</x-site.link>
          </div>
        </div>
      </div>
    @endif
  </section>
</x-layout.site>
