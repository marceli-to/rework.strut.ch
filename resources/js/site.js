// Public site: one small module per behaviour, each looks for its data-* hooks.
import { initHeader } from './modules/header';
import { initMenu } from './modules/menu';
import { initToggles } from './modules/toggle';
import { initMap } from './modules/map';
import { initMasonry } from './modules/masonry';
import { initLightbox } from './modules/lightbox';
import { initSlideshow } from './modules/slideshow';

initHeader();
initMenu();
initToggles();
initMap();
initMasonry();
initLightbox();
initSlideshow();
