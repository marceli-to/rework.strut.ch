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

	window.addEventListener('scroll', debounce(() => {
		const y = window.scrollY;

		if (mobile.matches) {
			if (y > last) header.classList.add('is-tiny');
			if (y === 0) header.classList.remove('is-tiny');
		} else {
			if (y <= 0) {
				header.classList.remove('is-tiny');
				return;
			}
			if (y > last && y > THRESHOLD) {
				header.classList.add('is-hidden');
				header.classList.remove('is-tiny');
			} else if (y < last && y > THRESHOLD) {
				header.classList.remove('is-hidden');
				header.classList.add('is-tiny');
			}
		}
		last = y;
	}, 10), { passive: true });
}
