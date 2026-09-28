{{-- Downloads: project documentation per category | Werkliste PDFs | job PDFs. --}}
@php
  $worksPdfs = ['gesamt' => 'Gesamt', 'wohnen' => 'Wohnen', 'gewerbe' => 'Gewerbe', 'oeffentlich' => 'Öffentlich', 'wettbewerb' => 'Wettbewerb', 'status' => 'Nach Status', 'jahr' => 'Nach Jahr', 'typ' => 'Nach Typ'];
  $heading = 'text-xl leading-[1.21] sm:text-md md:text-4xl md:leading-[1.129] mb-8 md:mb-24';
@endphp
<x-layout.site title="Downloads" :description="$page->meta_description">
  <section>
    <x-site.heading>Downloads</x-site.heading>
    <div class="grid gap-24 sm:grid-cols-[repeat(2,1fr)] md:grid-cols-[repeat(3,1fr)]">
      <div>
        <x-site.article :large="false">
          <h2 class="{{ $heading }}">Projektdokumentationen</h2>
          @foreach ($categories as $category)
            <x-site.article as="div" :large="false">
              <h3 class="mb-8">{{ $category->name }}</h3>
              <x-site.file-link :href="route('pdf.category', [$category->id, Str::slug($category->name)])" target="_blank" title="Projektdokumentationen {{ $category->name }}">Alle {{ $category->name }}</x-site.file-link>
              @foreach ($category->types as $type)
                <div class="my-8 md:mb-16">
                  @if ($category->show_types)
                    <h4>{{ $type->name_plural }}</h4>
                  @endif
                  @foreach ($type->projects as $project)
                    @foreach ($project->files as $file)
                      <div>
                        <x-site.file-link :href="$file->url()" target="_blank" title="Projektdokumentation {{ $project->name }}, {{ $project->location }}">{{ $project->name }}, {{ $project->location }}</x-site.file-link>
                      </div>
                    @endforeach
                  @endforeach
                </div>
              @endforeach
            </x-site.article>
          @endforeach
        </x-site.article>
      </div>
      <div>
        <x-site.article :large="false">
          <h2 class="{{ $heading }}">Werkliste</h2>
          <div class="md:pt-8">
            @foreach ($worksPdfs as $variant => $label)
              <div><x-site.file-link :href="route('pdf.works', $variant)" target="_blank">{{ $label }}</x-site.file-link></div>
            @endforeach
          </div>
        </x-site.article>
      </div>
      <div>
        <x-site.article :large="false">
          <h2 class="{{ $heading }}">Jobs</h2>
          @if ($jobs->isNotEmpty())
            <div class="md:pt-8">
              @foreach ($jobs as $job)
                <div>
                  @if ($file = $job->files->first())
                    <x-site.file-link :href="$file->url()" target="_blank" title="Ausschreibung {{ $job->title }}">{{ $job->title }}</x-site.file-link>
                  @endif
                </div>
              @endforeach
            </div>
          @else
            <p>Zur Zeit sind alle unsere Stellen besetzt.</p>
          @endif
        </x-site.article>
      </div>
    </div>
  </section>
</x-layout.site>
