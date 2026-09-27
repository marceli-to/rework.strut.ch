<x-layout.project
  :project="$project"
  :prev="$prev"
  :next="$next"
  :context="$context"
  :title="$project->full_title"
  :description="$project->meta_description"
  :og-image="$project->og_image"
  :canonical="$canonical">

  <x-projects.images :project="$project" />

  <x-slot:footer>
    <div class="md:hidden">
      <x-projects.browser :prev="$prev" :next="$next" :context="$context" />
    </div>
    <x-projects.menu :project="$project" :context="$context" />
  </x-slot>

</x-layout.project>
