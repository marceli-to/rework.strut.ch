// Main navigation (legacy menu.js). Mobile: the button shows the full menu.
// Desktop: submenus open as dropdowns and the nav grows to the open list.
// The legacy JS switches at 901px while the CSS switches at 900px; kept as is.

import { debounce } from './debounce';

const desktop = window.matchMedia('(min-width: 901px)');

export function initMenu(root = document) {
	const nav = root.querySelector('[data-menu]');
	const button = root.querySelector('[data-menu-button]');
	if (!nav) return;

	const isVisible = (el) => el && getComputedStyle(el).display !== 'none';
	const open = (ul) => ul.setAttribute('data-open', '');
	// Lists of the current project stay open (legacy: display block !important);
	// only a top-level list drops its initial "current" state.
	const close = (ul) => {
		ul.removeAttribute('data-open');
		if (ul.previousElementSibling?.hasAttribute('data-submenu-parent')) ul.removeAttribute('data-current');
	};
	const closeWithin = (el) => el.querySelectorAll('ul').forEach(close);

	const growToOpenList = () => {
		if (!desktop.matches) return;
		const list = nav.querySelector('ul[data-open]');
		nav.style.height = `${(list?.offsetHeight ?? 0) + 30}px`;
	};
	const resetHeight = () => {
		if (desktop.matches) nav.style.height = '';
	};

	const html = document.documentElement;
	const isOpen = () => html.hasAttribute('data-menu-open');
	const toggleMenu = (show) => {
		html.toggleAttribute('data-menu-open', show);
		button?.setAttribute('aria-expanded', String(show));
	};

	const toggleSubmenu = (btn) => {
		const list = btn.nextElementSibling;
		const item = btn.parentElement;

		if (btn.hasAttribute('data-submenu-parent')) {
			if (isVisible(list)) {
				closeWithin(item);
				resetHeight();
			} else {
				nav.querySelectorAll('ul ul').forEach(close);
				open(list);
				growToOpenList();
			}
		} else if (isVisible(list)) {
			closeWithin(item);
			growToOpenList();
		} else {
			// Close the lists of every sibling item on the way up.
			for (let li = item; li && nav.contains(li); li = li.parentElement.closest('li')) {
				[...li.parentElement.children].filter((sibling) => sibling !== li).forEach(closeWithin);
			}
			open(list);
			growToOpenList();
		}
		btn.setAttribute('aria-expanded', String(isVisible(list)));
	};

	button?.addEventListener('click', () => toggleMenu(!isOpen()));

	nav.addEventListener('click', (event) => {
		const btn = event.target.closest('[data-submenu-button]');
		if (btn) toggleSubmenu(btn);
	});

	document.addEventListener('click', (event) => {
		if (desktop.matches && !nav.contains(event.target)) {
			nav.querySelectorAll('ul[data-open]').forEach(close);
			resetHeight();
		}
	});

	window.addEventListener('resize', debounce(() => {
		if (!desktop.matches) return;
		toggleMenu(false);
		growToOpenList();
	}, 200));

	document.addEventListener('keydown', (event) => {
		if (event.key !== 'Escape') return;
		if (isOpen()) {
			toggleMenu(false);
			button?.focus();
		} else if (desktop.matches) {
			nav.querySelectorAll('ul[data-open]').forEach(close);
			resetHeight();
		}
	});
}
