{{--
  One row of the homepage grid (legacy ratio-boxes): every cell is a box with
  a fixed ratio, also when empty; images and videos link to their project,
  news cells show the news tile.
--}}
@props(['columns'])
@php $frs = implode(' ', array_column($columns, 'fr')); @endphp
<div @class([
  'sm:grid',
  'sm:grid-cols-[1fr]' => $frs === '1',
  'sm:grid-cols-[repeat(2,1fr)]' => $frs === '1 1',
  'sm:grid-cols-[repeat(3,1fr)]' => $frs === '1 1 1',
  'sm:grid-cols-[2fr_1fr]' => $frs === '2 1',
  'sm:grid-cols-[1fr_2fr]' => $frs === '1 2',
])>
  @foreach ($columns as $column)
    <div>
      @foreach ($column['cells'] as $cell)
        <div @class([
          'relative h-0 overflow-hidden bg-white mb-16 sm:mb-0',
          'pt-[66.666666666666667%]' => in_array($cell['size'], ['a', 'b']),
          'pt-[68.444444444444444%]' => in_array($cell['size'], ['c', 'd']),
          'pt-[136.888888888888889%]' => $cell['size'] === 'e',
        ])>
          @if ($media = $cell['item']?->media)
            <a href="{{ $media->mediable->url }}" class="group block absolute top-0 left-0 w-[calc(100%-24px)] h-[calc(100%-24px)]">
              <figure class="h-full">
                <x-site.caption :project="$media->mediable" />
                @if ($media->isVideo())
                  <video autoplay muted loop playsinline class="block w-full h-full object-cover">
                    <source src="{{ $media->url() }}">
                  </video>
                @else
                  <x-site.image :$media size="lg" width="1398" height="932" :alt="$media->alt ?: $media->caption" class="block w-full h-full object-cover" />
                @endif
              </figure>
            </a>
          @endif
          @if ($news = $cell['item']?->news)
            <x-site.news :$news />
          @endif
        </div>
      @endforeach
    </div>
  @endforeach
</div>
