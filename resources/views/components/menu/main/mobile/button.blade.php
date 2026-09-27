<button 
  type="button" 
  class="cursor-pointer w-35 h-27 flex items-center justify-center {{ $class ?? '' }}"
  x-cloak
  aria-label="Menü anzeigen/verbergen"
  @click="menu = !menu">
  <template x-if="!menu">
    <x-icons.burger class="w-35 h-auto" />
  </template>
  <template x-if="menu">
    <x-icons.cross class="w-27 h-auto" />
  </template>
</button>
  