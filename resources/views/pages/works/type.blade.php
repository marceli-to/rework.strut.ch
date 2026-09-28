{{-- Werkliste by type: one column per category; type headings only when the category shows types. --}}
<x-site.works :$by :$page>
  @foreach ($categories as $category)
    <div>
      <x-site.article>
        <h2 class="mb-8 sm:mb-16">{{ $category->name }}</h2>
        @foreach ($category->types as $type)
          @if ($category->show_types)
            <x-site.article>
              <h3 class="text-sm leading-[1.13] sm:text-xs sm:leading-[1.2] md:text-2xl">{{ $type->name_plural }}</h3>
              @foreach ($type->projects as $project)
                <x-site.works.item :$project />
              @endforeach
            </x-site.article>
          @else
            <div>
              @foreach ($type->projects as $project)
                <x-site.works.item :$project />
              @endforeach
            </div>
          @endif
        @endforeach
      </x-site.article>
    </div>
  @endforeach
</x-site.works>
