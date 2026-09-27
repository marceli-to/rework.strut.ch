<nav
  x-show="menu"
  x-transition:enter="transition-transform duration-300 ease-out"
  x-transition:enter-start="translate-y-[calc(100%+var(--footer-height))]"
  x-transition:enter-end="translate-y-0"
  x-transition:leave="transition-transform duration-200 ease-in"
  x-transition:leave-start="translate-y-0"
  x-transition:leave-end="translate-y-[calc(100%+var(--footer-height))]"
  x-cloak
  class="fixed bottom-60 left-0 z-10 bg-white w-full p-16 h-174 flex flex-col justify-center md:!hidden">
  <ul class="flex flex-col gap-y-18">
    {{ $slot }}
  </ul>
</nav>