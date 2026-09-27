@props(['id' => 'landing', 'layout' => 'inline'])

@if($layout === 'sides')

  <button
    type="button"
    data-slides-prev="{{ $id }}"
    aria-label="Vorheriger Slide"
    class="absolute top-1/2 -translate-y-1/2 left-16 xl:left-32 z-10 cursor-pointer w-15 h-27">
    <x-icons.chevron-left class="w-full h-auto" />
  </button>

  <button
    type="button"
    data-slides-next="{{ $id }}"
    aria-label="Nächster Slide"
    class="absolute top-1/2 -translate-y-1/2 right-16 xl:right-32 z-10 cursor-pointer w-15 h-27">
    <x-icons.chevron-right class="w-full h-auto" />
  </button>

@else

  <div class="flex gap-x-52">

    <button
      type="button"
      data-slides-prev="{{ $id }}"
      aria-label="Vorheriger Slide"
      class="cursor-pointer w-15 h-27">
      <x-icons.chevron-left class="w-full h-auto" />
    </button>

    <button
      type="button"
      data-slides-next="{{ $id }}"
      aria-label="Nächster Slide"
      class="cursor-pointer w-15 h-27">
      <x-icons.chevron-right class="w-full h-auto" />
    </button>

  </div>

@endif
