{{-- Bücher: masonry of books with description, info toggle and order link. --}}
<x-layout.site title="Bücher" :$page>
  <section>
    <x-site.heading>Bücher</x-site.heading>
    <x-site.masonry>
      @foreach ($books as $book)
        <x-site.masonry.item>
          <header class="text-xl leading-[1.21] sm:text-md md:text-4xl md:leading-[1.129]">
            <h2>{{ $book->title }}</h2>
            @if ($image = $book->images->first())
              <figure class="block my-8 md:my-16">
                <x-site.image :media="$image" size="sm" width="600" height="400" :alt="$image->alt ?: $book->title" loading="eager" class="block w-full h-auto" />
              </figure>
            @endif
            <div class="text-sm leading-[1.13] sm:text-xs sm:leading-[1.2] md:text-2xl">
              <p>{!! nl2br(e($book->description)) !!}</p>
              <div class="mt-8 md:mt-16">
                <x-site.toggle reverse controls="book-{{ $book->id }}">Info</x-site.toggle>
                <div id="book-{{ $book->id }}" class="[word-break:break-word] hyphens-auto mb-8 md:mb-16" hidden>{!! $book->info !!}</div>
                @if ($book->url)
                  <div class="mt-4">
                    @if (str_contains($book->url, '@'))
                      <x-site.arrow-link href="mailto:{{ $book->url }}?subject=Bestellung {{ $book->title }}&body=Ich bestelle 1 Exemplar '{{ $book->title }}'" title="Buch «{{ $book->title }}» Bestellen">Bestellen</x-site.arrow-link>
                    @else
                      <x-site.arrow-link :href="$book->url" target="_blank" rel="noopener" title="Buch «{{ $book->title }}» Bestellen">Bestellen</x-site.arrow-link>
                    @endif
                  </div>
                @endif
              </div>
            </div>
          </header>
        </x-site.masonry.item>
      @endforeach
    </x-site.masonry>
  </section>
</x-layout.site>
