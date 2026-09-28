{{-- Presse, Auszeichnungen, Vorträge: years in three columns (GetEntries). --}}
<x-layout.site :title="$type->label()" :description="$page->meta_description">
  <section>
    <x-site.heading>{{ $type->label() }}</x-site.heading>
    <div class="grid gap-24 sm:grid-cols-[repeat(2,1fr)] md:grid-cols-[repeat(3,1fr)]">
      @foreach ($columns as $years)
        <div>
          <article>
            @foreach ($years as $year => $entries)
              <x-site.card.heading>{{ $year }}</x-site.card.heading>
              @foreach ($entries as $entry)
                <x-site.entry :$entry />
              @endforeach
            @endforeach
          </article>
        </div>
      @endforeach
    </div>
  </section>
</x-layout.site>
