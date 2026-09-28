{{-- Über uns: text | images, then the team (masonry) with a CV toggle each. --}}
<x-layout.site title="Über uns" :page="$meta">
  <section>
    @if ($page)
      <div class="mb-42 sm:flex sm:items-start md:mb-84">
        <div class="mb-24 sm:w-[calc(33.33333%-16px)] sm:mr-24">
          <x-site.heading>{{ $page->title }}</x-site.heading>
          <x-site.article class="[word-break:break-word] hyphens-auto">{!! $page->text !!}</x-site.article>
        </div>
        <div class="sm:w-[calc(66.66667%-8px)] sm:mt-18 md:mt-27">
          <x-site.page-images :images="$page->images" :alt="config('app.name') . ' - Team'" />
        </div>
      </div>
    @endif
    <h2>Team</h2>
    <x-site.masonry>
      @foreach ($team as $member)
        <x-site.masonry.item>
          <header class="text-xl leading-[1.21] sm:text-md md:text-4xl md:leading-[1.129]">
            <h3 class="text-green">
              @if ($member->email)
                <a href="mailto:{{ $member->email }}">{{ $member->firstname }} {{ $member->lastname }}</a>
              @else
                {{ $member->firstname }} {{ $member->lastname }}
              @endif
            </h3>
            @if ($member->role){{ $member->role }}@else&nbsp;@endif<br>
            @if ($member->position){{ $member->position }}@else&nbsp;@endif<br>
          </header>
          @if ($image = $member->images->first())
            <figure class="block mt-4 mb-8 max-w-[40%] md:max-w-[50%]">
              <x-site.image :media="$image" size="sm" width="432" height="500" :alt="$image->alt ?: config('app.name') . ' - ' . $member->firstname . ' ' . $member->lastname" class="block w-full h-auto" />
            </figure>
          @endif
          <div>
            @if ($member->phone) <a href="tel:{{ $member->phone }}">{{ $member->phone }}</a><br> @endif
            @if ($member->email) <a href="mailto:{{ $member->email }}">{{ $member->email }}</a> @endif
          </div>
          <x-site.toggle controls="cv-{{ $member->id }}">Lebenslauf</x-site.toggle>
          <div id="cv-{{ $member->id }}" class="mt-8" hidden>{!! $member->cv !!}</div>
        </x-site.masonry.item>
      @endforeach
    </x-site.masonry>
  </section>
</x-layout.site>
