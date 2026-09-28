// Header on scroll (legacy header.js).
// Up to 900px: shrinks (logo hidden) once the page scrolls down, back at the top.
// From 901px: hides when scrolling down past 170px, comes back small when scrolling up.

import { debounce } from './debounce';

const mobile = window.matchMedia('(max-width: 900px)');
const THRESHOLD = 170;

export function initHeader(root = document) {
	const header = root.querySelector('[data-header]');
	if (!header) return;

	let last = 0;
	const is = (state) => header.dataset.scroll === state;
	const set = (state) => (state ? (header.dataset.scroll = state) : delete header.dataset.scroll);

	window.addEventListener('scroll', debounce(() => {
		const y = window.scrollY;

		if (mobile.matches) {
			if (y > last) set('tiny');
			if (y === 0 && is('tiny')) set(null);
		} else {
			if (y <= 0) {
				// Legacy: only "tiny" is reset at the top; "hidden" stays.
				if (is('tiny')) set(null);
				return;
			}
			if (y > last && y > THRESHOLD) set('hidden');
			else if (y < last && y > THRESHOLD) set('tiny');
		}
		last = y;
	}, 10), { passive: true });
}
