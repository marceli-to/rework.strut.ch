// Public site: one small module per behaviour, each looks for its data-* hooks.
import { initHeader } from './modules/header';
import { initMenu } from './modules/menu';
import { initToggles } from './modules/toggle';
import { initMap } from './modules/map';

initHeader();
initMenu();
initToggles();
initMap();
