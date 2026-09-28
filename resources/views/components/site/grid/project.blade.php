{{--
  One row of the project image grid (legacy grid-2x1fr + grid-stack).
  A column with a single cell shows the media directly (natural height,
  cropped to the row); stacked cells keep their layout ratio (sm 687×458,
  lg 687×940), a spacer fills the rest. Columns without items are left out.
--}}
@props(['columns'])
<div class="grid gap-24 sm:grid-cols-[repeat(2,1fr)] not-first:mt-24">
  @foreach ($columns as $column)
    @continue(! $column['filled'])
    <div>
      @if (count($column['cells']) === 1)
        <x-site.grid.media :item="$column['cells'][0]['item']" :size="$column['cells'][0]['size']" direct />
      @else
        <div class="flex flex-col h-full">
          @foreach ($column['cells'] as $cell)
            @if ($cell['size'] === 'spacer')
              <div class="flex-auto overflow-hidden first-of-type:mb-24"></div>
            @elseif ($cell['item'])
              <div @class([
                'flex-none overflow-hidden first-of-type:mb-24',
                'aspect-[687/458]' => $cell['size'] === 'sm',
                'aspect-[687/940]' => $cell['size'] === 'lg',
              ])><x-site.grid.media :item="$cell['item']" :size="$cell['size']" /></div>
            @endif
          @endforeach
        </div>
      @endif
    </div>
  @endforeach
</div>
