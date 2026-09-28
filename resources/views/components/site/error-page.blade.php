{{-- Error page in the site layout (legacy web/errors): code as title, text, link home. --}}
@props(['code', 'title'])
<x-layout.site :title="$code . ' - ' . $title">
  <section>
    <div>
      <div>
        <x-site.heading>{{ $code }}</x-site.heading>
        <x-site.article>
          {{ $slot }}
          <p><a href="/" title="Zur Startseite" class="text-black underline">Zur Start­seite.</a></p>
        </x-site.article>
      </div>
      <div></div>
    </div>
  </section>
</x-layout.site>
