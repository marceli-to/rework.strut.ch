{{-- Masonry item: a card with a rule on top, body text size. --}}
<div class="w-full sm:w-[calc(33.33333%-16px)]" data-masonry-item>
  <x-site.article :large="false" {{ $attributes }}>{{ $slot }}</x-site.article>
</div>
