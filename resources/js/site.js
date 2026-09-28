// Public site: one small module per behaviour, each looks for its data-* hooks.
import { initHeader } from './modules/header';
import { initMenu } from './modules/menu';

initHeader();
initMenu();
