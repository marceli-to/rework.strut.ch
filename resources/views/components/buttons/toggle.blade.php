@props(['label'])
<button
  {{ $attributes->merge(['class' => 'cursor-pointer flex items-center gap-6 w-full']) }}
  aria-label="{{ $label }}"
  @click="open = !open">
  <span>{{ $label }}</span>
  <span :class="open ? 'rotate-180' : ''">
    <x-icons.chevron-down class="w-18 h-auto" />
  </span>
</button>
