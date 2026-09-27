@props(['project'])

@if ($project->detail)
  <a
    href="{{ route('page.project.worklist.images', $project->slug) }}"
    class="hover:text-accent transition-colors group w-full block">

    <div>

      <h2 class="text-xl leading-[1.174]">
        {{ $project->title }}@if($project->location), {{ $project->location }}@endif
      </h2>

      <div class="text-md leading-[1.31]">
        {{ $project->subtitle }} {{ $project->year }}
      </div>

    </div>

  </a>
@else
  <div class="text-silver">
    <h2 class="text-xl leading-[1.174]">
      {{ $project->title }}@if($project->location), {{ $project->location }}@endif
    </h2>

    <div class="text-md leading-[1.31]">
      {{ $project->subtitle }} {{ $project->year }}
    </div>
  </div>
@endif
