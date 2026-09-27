<x-layout.site :description="$seo?->projects_meta_description" title="Werkliste – Projekte">

  <div class="h-full px-16 xl:px-32 py-24 overflow-auto">

    <x-grid.container class="min-h-full flex flex-col gap-y-32 xl:gap-y-47 pb-22">
      @foreach($projects as $project)
        <x-grid.span class="md:col-span-4 xl:col-span-3">
          <x-projects.cards.work :project="$project" />
        </x-grid.span>
      @endforeach
    </x-grid.container>
    
  </div>
  
  <x-slot:footer>
    <x-menu.pages.projects.container />
  </x-slot>

</x-layout.site>
