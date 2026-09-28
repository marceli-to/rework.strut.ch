{{-- Press, award or lecture entry. The title links to the PDF, else to the URL. --}}
@props(['entry'])
@php
  $href = $entry->files->first()?->url() ?? $entry->url;
  $image = $entry->images->first();
@endphp
<x-site.card :link="(bool) $href">
  <h3 class="font-medium">
    @if ($href)
      <x-site.link :$href target="_blank" rel="noopener" :title="$entry->title">{{ $entry->title }}</x-site.link>
    @else
      {{ $entry->title }}
    @endif
  </h3>
  <div>{{ $entry->description }}@if ($entry->project), {{ $entry->project->name }} {{ $entry->project->location }} ({{ $entry->project->year }})@endif</div>
  @if ($image)
    <figure class="mt-8 mb-4 md:mb-16">
      <img src="{{ $image->imageUrl('xs') }}" width="600" height="400" alt="{{ $image->alt ?: $entry->title }}" class="block w-[70%] h-auto">
    </figure>
  @endif
</x-site.card>
