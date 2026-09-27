import Alpine from 'alpinejs';
import Collapse from '@alpinejs/collapse';
import { initSlides } from './modules/slides';
import { initLogo } from './modules/logo';
import { initShy } from './modules/shy';

Alpine.plugin(Collapse);
Alpine.start();

initSlides();
initLogo();
initShy();
