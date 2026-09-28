{{-- Werkliste by year, in three columns of whole years. --}}
<x-site.works :$by :$page>
  @foreach ($columns as $years)
    <div>
      <article>
        @foreach ($years as $year => $projects)
          <x-site.card.heading dense>{{ $year }}</x-site.card.heading>
          @foreach ($projects as $project)
            <x-site.works.item :$project />
          @endforeach
        @endforeach
      </article>
    </div>
  @endforeach
</x-site.works>
