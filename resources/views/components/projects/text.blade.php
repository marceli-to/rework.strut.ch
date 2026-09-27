<div class="w-full md:h-full px-16 xl:px-32 py-24 md:overflow-auto">
  <x-grid.container>
    <x-grid.span class="md:col-span-8 md:col-start-3 xl:col-span-6 xl:col-start-4">
      <h2 class="text-lg leading-[1.33] mb-18">
        {{ collect([$project->subtitle, $project->year])->filter()->implode(' ') }}
      </h2>
      <div class="flex flex-col gap-y-32">
        @if ($project->description)
          <article class="text-lg leading-[1.33]">
            {!! $project->description !!}
          </article>
        @endif
        @if ($project->info)
          <article>
            {!! $project->info !!}
          </article>
        @endif
      </div>
    </x-grid.span>
  </x-grid.container>
</div>