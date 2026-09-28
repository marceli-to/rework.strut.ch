{{-- Werkliste by status: Ausgeführt | In Planung + Studie | Wettbewerb (by prize). --}}
<x-site.works :$by :$page>
  @foreach ([['executed'], ['planned', 'study']] as $keys)
    @if (array_intersect_key($status, array_flip($keys)))
      <div>
        @foreach ($keys as $key)
          @isset($status[$key])
            <article>
              <x-site.card.heading dense>{{ $status[$key]['label'] }}</x-site.card.heading>
              @foreach ($status[$key]['projects'] as $project)
                <x-site.works.item :$project />
              @endforeach
            </article>
          @endisset
        @endforeach
      </div>
    @endif
  @endforeach
  @if ($competition)
    <div>
      <x-site.article>
        <h2 class="mb-8 sm:mb-16">Wettbewerb</h2>
        @foreach ($competition as $group)
          <article>
            <x-site.article as="div">
              <h3 class="text-sm leading-[1.13] sm:text-xs sm:leading-[1.2] md:text-2xl">{{ $group['label'] }}</h3>
              @foreach ($group['projects'] as $project)
                <x-site.works.item :$project />
              @endforeach
            </x-site.article>
          </article>
        @endforeach
      </x-site.article>
    </div>
  @endif
</x-site.works>
