{{-- Werkliste entry; links to the project when it has a detail page. --}}
@props(['project'])
<x-site.card dense :link="$project->has_detail">
  <h3>
    @if ($project->has_detail)
      <x-site.link :href="$project->url">{{ $project->name }}, {{ $project->location }}</x-site.link>
    @else
      {{ $project->name }}, {{ $project->location }}
    @endif
  </h3>
</x-site.card>
