import Swiper from 'swiper';
import { Navigation, Autoplay } from 'swiper/modules';
import 'swiper/css';

export function initSlides(root = document) {
  root.querySelectorAll('[data-slides]').forEach((el) => {
    if (el.offsetParent === null) return;

    const id = el.dataset.slides;
    const sel = (role) => id ? `[data-slides-${role}="${id}"]` : `[data-slides-${role}]`;

    const counter = id ? root.querySelector(`[data-slides-counter="${id}"]`) : null;
    const counterCurrent = counter?.querySelector('[data-slides-counter-current]');
    const counterTotal = counter?.querySelector('[data-slides-counter-total]');

    const autoplayDelay = parseInt(el.dataset.slidesAutoplay || '', 10);
    const autoplay = autoplayDelay > 0
      ? { delay: autoplayDelay, disableOnInteraction: false }
      : false;

    const swiper = new Swiper(el.querySelector('.swiper'), {
      modules: [Navigation, Autoplay],
      slidesPerView: 1,
      loop: true,
      spaceBetween: 0,
      autoplay,
      navigation: {
        prevEl: root.querySelector(sel('prev')),
        nextEl: root.querySelector(sel('next')),
      },
      on: {
        slideChange(s) {
          if (counterCurrent) counterCurrent.textContent = s.realIndex + 1;
        },
      },
    });

    if (counter) {
      const real = el.querySelectorAll('.swiper-slide:not(.swiper-slide-duplicate)').length;
      if (counterTotal) counterTotal.textContent = real;
      if (counterCurrent) counterCurrent.textContent = swiper.realIndex + 1;
    }
  });
}
