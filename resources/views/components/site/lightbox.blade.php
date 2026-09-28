{{--
  Lightbox for links with data-lightbox="single|gallery" (modules/lightbox.js),
  replacing the legacy fancyBox 3 setup: white background, cross top right,
  in a gallery the left and right half of the screen go back and forward
  (arrow cursors, grey at the ends).
--}}
<dialog
  class="fixed inset-0 m-0 p-0 w-full h-full max-w-none max-h-none overflow-hidden bg-transparent backdrop:bg-transparent"
  aria-label="Bildansicht"
  data-lightbox-dialog>
  <div class="absolute inset-0 bg-white opacity-0 transition-opacity duration-366 ease-[cubic-bezier(.47,0,.74,.71)] data-open:opacity-100" data-lightbox-bg></div>
  <div class="absolute inset-0" data-lightbox-stage></div>
  <button
    type="button"
    class="fixed top-0 left-0 z-10 block w-1/2 h-full cursor-[url(/img/icons/arrow-prev_sm.png),auto] data-inactive:cursor-[url(/img/icons/arrow-prev_sm-grey.png),auto] md:cursor-[url(/img/icons/arrow-prev_lg.png),auto] md:data-inactive:cursor-[url(/img/icons/arrow-prev_lg-grey.png),auto]"
    aria-label="Vorheriges Bild"
    hidden
    data-lightbox-prev></button>
  <button
    type="button"
    class="fixed top-0 right-0 z-10 block w-1/2 h-full cursor-[url(/img/icons/arrow-next_sm.png),auto] data-inactive:cursor-[url(/img/icons/arrow-next_sm-grey.png),auto] md:cursor-[url(/img/icons/arrow-next_lg.png),auto] md:data-inactive:cursor-[url(/img/icons/arrow-next_lg-grey.png),auto]"
    aria-label="Nächstes Bild"
    hidden
    data-lightbox-next></button>
  <button
    type="button"
    class="absolute top-10 right-10 z-20 block w-18 h-18 cursor-pointer bg-no-repeat bg-cover bg-[url(/img/icons/cross.svg)] md:top-20 md:right-20 md:w-27 md:h-27"
    aria-label="Schliessen"
    autofocus
    data-lightbox-close></button>
  <template data-lightbox-slide>
    <figure class="absolute m-0 transition-opacity duration-366 origin-top-left">
      <picture class="contents">
        <source type="image/avif" data-format="avif">
        <source type="image/webp" data-format="webp">
        <img alt="" class="block w-full h-full">
      </picture>
      <figcaption class="absolute left-0 right-0 -bottom-24 pl-10 text-left pointer-events-none sm:pl-0 md:-bottom-32"></figcaption>
    </figure>
  </template>
</dialog>
