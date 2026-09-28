// Show/hide toggles (legacy contact.js): a [data-toggle] button shows or hides
// the element named in its aria-controls, then fires `toggle:change` (bubbles),
// e.g. for the masonry to re-layout.

export function initToggles(root = document) {
	root.addEventListener('click', (event) => {
		const button = event.target.closest('[data-toggle]');
		const target = button && document.getElementById(button.getAttribute('aria-controls'));
		if (!target) return;

		const open = target.hidden;
		target.hidden = !open;
		button.setAttribute('aria-expanded', String(open));
		button.dispatchEvent(new CustomEvent('toggle:change', { bubbles: true }));
	});
}
